# 🚂 Railway Deployment Guide for Laravel Backend

## Why Railway?
- **No cold starts** - your app stays warm (unlike Render free tier)
- **Same network as MySQL** - your MySQL is already on Railway = faster queries
- **500 free hours/month** - enough for low-traffic apps
- **Built-in Redis** - easy to add for faster caching

---

## Prerequisites
✅ Railway account (sign up at https://railway.app)
✅ MySQL database already running on Railway
✅ Laravel backend code pushed to GitHub

---

## 🚀 Deployment Steps

### Step 1: Create New Service on Railway

1. Go to https://railway.app/dashboard
2. Click **"New Project"**
3. Select **"Deploy from GitHub repo"**
4. Connect your GitHub account and select your backend repository
5. Railway will auto-detect Laravel and configure it

### Step 2: Link Your Existing MySQL Database

1. In your project, click on the new Laravel service
2. Click **"Variables"** tab
3. Click **"+ New Variable"** → **"Add Reference"**
4. Select your MySQL service
5. Railway will automatically inject `DATABASE_URL`

**OR** manually add the connection:
- Find your MySQL service → "Connect" tab
- Copy the **MySQL Connection URL** (looks like `mysql://root:pass@containers-us-west-123.railway.app:3306/railway`)
- Add it as `DATABASE_URL` variable

### Step 3: Generate APP_KEY

Run locally to generate a key:
```bash
cd valorant-skin-collector-backend
php artisan key:generate --show
```

Copy the output (starts with `base64:...`)

### Step 4: Add Environment Variables

In the Railway service → **Variables** tab, add these:

```bash
# Application
APP_NAME=Valorant Skin Collector
APP_ENV=production
APP_KEY=base64:YOUR_KEY_FROM_STEP_3
APP_DEBUG=false
APP_URL=${{RAILWAY_PUBLIC_DOMAIN}}

# Database - Railway auto-injects this if you linked MySQL
DATABASE_URL=${{MYSQL_URL}}

# Cache & Session
CACHE_DRIVER=file
SESSION_DRIVER=database
QUEUE_CONNECTION=database

# Logging
LOG_CHANNEL=stack
LOG_LEVEL=error

# Frontend URL (update after deploying Vercel)
FRONTEND_URL=https://valorant-skin-collector.vercel.app,http://localhost:3000
```

**Important:**
- `${{RAILWAY_PUBLIC_DOMAIN}}` is a Railway variable that auto-resolves to your domain
- `${{MYSQL_URL}}` auto-references your MySQL service connection
- Update `FRONTEND_URL` with your actual Vercel URL

### Step 5: Enable Public Domain

1. In your Laravel service settings
2. Go to **"Settings"** tab
3. Click **"Generate Domain"** under "Networking"
4. Railway will give you a URL like: `https://your-app.up.railway.app`

### Step 6: Deploy!

Railway will automatically:
1. Detect Laravel using `nixpacks.toml`
2. Install Composer dependencies
3. Run migrations (via start command)
4. Start the PHP server

Watch the **"Deployments"** tab for logs.

### Step 7: Test Your Deployment

Once deployed, visit:
```
https://your-app.up.railway.app/api/health
```

You should see:
```json
{
  "status": "healthy",
  "timestamp": "2025-...",
  "service": "valorant-skin-collector-api"
}
```

Test the skins endpoint:
```
https://your-app.up.railway.app/api/v1/skins?perPage=10
```

---

## 📝 Configuration Files Explained

### `nixpacks.toml`
Tells Railway how to build and run your Laravel app:
- **[phases.setup]**: Installs PHP 8.2 and required extensions
- **[phases.install]**: Runs `composer install`
- **[phases.build]**: Optimizes config and routes
- **[start]**: Runs migrations and starts server on Railway's PORT

### `.railwayignore`
Tells Railway what files to ignore during deployment (like `.gitignore`)

---

## 🔄 Updating Your App

Railway auto-deploys on every push to your main branch:

```bash
git add .
git commit -m "Update Laravel app"
git push origin main
```

Railway detects the push and redeploys automatically.

---

## 🎛️ Railway Variables Reference

Use these Railway-provided variables:

| Variable | Description | Example |
|----------|-------------|---------|
| `${{RAILWAY_PUBLIC_DOMAIN}}` | Your app's public URL | `your-app.up.railway.app` |
| `${{MYSQL_URL}}` | MySQL connection string | `mysql://root:pass@...` |
| `${{PORT}}` | Port Railway assigns | `8000` (handled automatically) |

---

## 🚀 Performance Optimizations

### 1. Switch to File Cache (Recommended)
Already configured in the variables above:
```bash
CACHE_DRIVER=file
```

### 2. Add Redis for Better Caching (Optional - $5/mo)

1. In your Railway project, click **"+ New"**
2. Select **"Database"** → **"Add Redis"**
3. Railway will auto-inject `REDIS_URL`
4. Update your variables:
   ```bash
   CACHE_DRIVER=redis
   REDIS_URL=${{REDIS.REDIS_URL}}
   ```

### 3. Enable Laravel Octane (Advanced)

For even better performance, consider Laravel Octane with Swoole/RoadRunner.

---

## 🔧 Troubleshooting

### "No application encryption key has been specified"
- Make sure `APP_KEY` is set in Variables
- Must start with `base64:`

### "SQLSTATE[HY000] [2002] Connection refused"
- Check `DATABASE_URL` is correct
- Ensure MySQL service is in the same project
- Try using `${{MYSQL_URL}}` reference

### "Migrations not running"
- Check deployment logs for errors
- Make sure start command includes `php artisan migrate --force`
- Verify database connection

### "502 Bad Gateway"
- Check if app is listening on `PORT` environment variable
- Review logs in "Deployments" tab

---

## 📊 Railway vs Render Comparison

| Feature | Railway | Render (Free) |
|---------|---------|---------------|
| Cold starts | ❌ None | ✅ 15min = 30-60s delay |
| MySQL location | 🟢 Same network | 🔴 External (Railway) |
| Free hours | 500/month | Unlimited (but spins down) |
| Auto-deploy | ✅ Yes | ✅ Yes |
| Custom domains | ✅ Yes | ✅ Yes |

**Expected performance improvement: 2-10x faster** (no cold starts + same network as DB)

---

## 🎯 Next Steps

After Railway deployment:

1. ✅ Update Vercel environment variable:
   ```
   NUXT_PUBLIC_API_BASE_URL=https://your-app.up.railway.app
   ```

2. ✅ Update Railway `FRONTEND_URL` with your Vercel domain

3. ✅ Test the full flow (login, collection, wishlist)

4. ✅ Consider adding Redis for even better performance

---

## 💰 Cost Estimate

**Free tier:**
- 500 hours/month (enough for ~20 days of 24/7 runtime)
- $5/month if you exceed hours

**With Redis:**
- +$5/month for Redis database

**Total: $0-10/month** (vs. Render which would be $7/month for always-on)

---

## 📚 Additional Resources

- Railway Docs: https://docs.railway.app/
- Laravel on Railway: https://docs.railway.app/guides/laravel
- Railway CLI: https://docs.railway.app/develop/cli
