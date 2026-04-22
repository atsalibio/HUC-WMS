<?php
namespace App\Models\System;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'Notifications';
    public $timestamps = true;
    const CREATED_AT = 'CreatedAt';
    const UPDATED_AT = 'UpdatedAt';
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
