<?php

namespace App\Models;

class ClassModel extends BaseModel
{
    public const DEFAULT_RELATIONS = ['plan', 'user'];

    protected $table = 'classes';

    protected $fillable = [
        'plan_id', 'user_id', 'weekday', 'start_time',
        'end_time', 'capacity', 'room', 'status',
    ];

    protected $appends = ['registrations_count'];

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function registrations()
    {
        return $this->hasMany(ClassRegistration::class, 'class_id', 'id');
    }

    public function getRegistrationsCountAttribute()
    {
        if (array_key_exists('registrations_count', $this->attributes)) {
            return $this->attributes['registrations_count'];
        }

        return $this->registrations()->count();
    }
}
