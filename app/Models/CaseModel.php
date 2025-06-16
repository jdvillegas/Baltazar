<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CaseModel extends Model
{
    protected $table = 'case_models';
    protected $fillable = [
        'title',
        'description',
        'status',
        'user_id',
        'anulled_at',
        'departamento',
        'despacho',
        'es_privado',
        'fecha_proceso',
        'fecha_ultima_actuacion',
        'id_conexion',
        'id_proceso',
        'llave_proceso',
        'sujetos_procesales'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'fecha_proceso' => 'datetime',
        'fecha_ultima_actuacion' => 'datetime',
        'es_privado' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function getActiveCount($userId)
    {
        return self::where('user_id', $userId)
            ->whereIn('status', ['pendiente', 'en_proceso', 'anulado'])
            ->count();
    }
}
