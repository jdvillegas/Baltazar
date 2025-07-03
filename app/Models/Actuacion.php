<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Actuacion extends Model
{
    protected $table = 'actuaciones';
    
    protected $fillable = [
        'case_model_id',
        'idRegActuacion',
        'llaveProceso',
        'fechaRegistro',
        'fechaInicial',
        'fechaFinal',
        'fechaActuacion',
        'consActuacion',
        'conDocumento',
        'codRegla',
        'cant',
        'anotacion',
        'actuacion',
    ];

    protected $casts = [
        'fechaRegistro' => 'datetime',
        'fechaInicial' => 'datetime',
        'fechaFinal' => 'datetime',
        'fechaActuacion' => 'datetime',
        'consActuacion' => 'integer',
        'conDocumento' => 'boolean',
        'cant' => 'integer',
    ];

    public function caseModel(): BelongsTo
    {
        return $this->belongsTo(CaseModel::class);
    }
}
