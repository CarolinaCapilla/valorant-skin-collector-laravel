<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSkin extends Model
{
    protected $fillable = [
        'user_id', 'skin_uuid', 'chroma_uuid', 'level_uuid', 'owned', 'wishlist', 'metadata'
    ];

    protected $casts = [
        'owned' => 'boolean',
        'wishlist' => 'boolean',
        'metadata' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}