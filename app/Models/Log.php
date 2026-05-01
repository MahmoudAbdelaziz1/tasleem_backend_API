<?php
// app/Models/Log.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'logs';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'log_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'action_type',
        'action_name',
        'module',
        'entity_type',
        'entity_id',
        'old_data',
        'new_data',
        'ip_address',
        'user_agent',
        'status',
        'message',
        'error_code',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array',
    ];

    /**
     * Relationships
     */

   
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Accessors & Mutators
     */

  
    public function getActorNameAttribute()
    {
        return $this->user ? $this->user->name : 'unauth user';
    }


    public function getChangesDescriptionAttribute()
    {
        if (!$this->old_data || !$this->new_data) {
            return null;
        }

        $changes = [];
        foreach ($this->new_data as $key => $value) {
            $oldValue = $this->old_data[$key] ?? null;
            if ($oldValue != $value) {
                $changes[] = "$key: Form '$oldValue' to '$value'";
            }
        }

        return implode(', ', $changes);
    }

    /**
     * Scopes
     */

 
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

  
    public function scopeInModule($query, $module)
    {
        return $query->where('module', $module);
    }

  
    public function scopeOfActionType($query, $type)
    {
        return $query->where('action_type', $type);
    }


    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }

    
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    
    public function scopeForEntity($query, $entityType, $entityId)
    {
        return $query->where('entity_type', $entityType)
                     ->where('entity_id', $entityId);
    }

    /**
     * Boot method for model events
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($log) {
           
            if (empty($log->user_agent) && isset($_SERVER['HTTP_USER_AGENT'])) {
                $log->user_agent = $_SERVER['HTTP_USER_AGENT'];
            }

           
            if (empty($log->ip_address) && isset($_SERVER['REMOTE_ADDR'])) {
                $log->ip_address = $_SERVER['REMOTE_ADDR'];
            }
        });
    }
}