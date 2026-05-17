<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'target_type',
        'target_id',
        'description',
        'ip_address',
        'user_agent',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function log($action, $target = null, $description = null)
    {
        return self::create([
            'user_id'     => auth()->id(),
            'action'      => $action,
            'target_type' => $target ? get_class($target) : null,
            'target_id'   => $target ? $target->id : null,
            'description' => $description,
            'ip_address'  => request()->ip(),
            'user_agent'  => request()->userAgent(),
        ]);
    }
}
