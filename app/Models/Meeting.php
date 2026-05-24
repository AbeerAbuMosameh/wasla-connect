<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    protected $fillable = [
        'date', 'time', 'duration', 'type', 'title', 'status',
        'topics', 'accomplished', 'action_items', 'notes',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function formattedDate(): string
    {
        return $this->date->format('d M Y');
    }
}
