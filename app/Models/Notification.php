<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Notification extends Model
{
    protected $fillable = [
        'title',
        'message',
        'sender_id'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('read_at')
            ->withTimestamps();
    }

    public function markAsRead(): void
    {
        $this->users()->updateExistingPivot(auth()->id(), [
            'read_at' => now()
        ]);
    }

    public function isReadBy(User $user): bool
    {
        return $this->users()->wherePivot('read_at', '!=', null)
            ->where('users.id', $user->id)
            ->exists();
    }
}
