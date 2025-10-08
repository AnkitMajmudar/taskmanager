<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_name',
        'description',
        'is_completed',
        'due_date',
        'priority',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'due_date' => 'datetime',
    ];

    // helper: is due within 24 hours and incomplete
    public function isDueSoon(): bool
    {
        if (!$this->due_date) return false;
        $now = Carbon::now();
        return !$this->is_completed && $this->due_date->greaterThan($now) && $this->due_date->diffInHours($now) <= 24;
    }
}
