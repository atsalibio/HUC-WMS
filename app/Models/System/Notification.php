<?php
namespace App\Models\System;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'Notification';
    public $timestamps = false;
    protected $fillable = [
        'UserID',
        'TargetRole',
        'Title',
        'Message',
        'Link',
        'Priority',
        'IsRead'
    ];

    // Scopes for easy filtering
    public function scopeUnread($query)
    {
        return $query->where('IsRead', false);
    }
}
