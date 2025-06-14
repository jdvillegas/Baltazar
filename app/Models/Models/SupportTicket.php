<?php

namespace App\Models\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class SupportTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'resolved_by',
        'title',
        'description',
        'priority',
        'status',
        'resolution',
        'resolved_at'
    ];

    protected $casts = [
        'resolved_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function resolver()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function getStatusBadge()
    {
        switch ($this->status) {
            case 'pending':
                return '<span class="badge bg-warning">Pendiente</span>';
            case 'in_progress':
                return '<span class="badge bg-info">En Progreso</span>';
            case 'resolved':
                return '<span class="badge bg-success">Resuelto</span>';
            default:
                return '<span class="badge bg-secondary">Desconocido</span>';
        }
    }

    public function getPriorityBadge()
    {
        switch ($this->priority) {
            case 'low':
                return '<span class="badge bg-success">Baja</span>';
            case 'medium':
                return '<span class="badge bg-warning">Media</span>';
            case 'high':
                return '<span class="badge bg-danger">Alta</span>';
            default:
                return '<span class="badge bg-secondary">Desconocido</span>';
        }
    }
}
