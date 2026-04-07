<?php
namespace App\Models\System;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'target_role',
        'title',
        'message',
        'link',
        'priority',
        'is_read'
    ];
    
    // Scopes for easy filtering
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }
}