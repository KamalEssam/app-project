<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicationVisit extends Model
{
    protected $table = 'medication_visit';

    protected $fillable = [
        'medication_id',
        'visit_id'
    ];

    public function getIdAttribute($value)
    {
        return (int)$value;
    }

    public function getMedicationIdAttribute($value)
    {
        return (int)$value;
    }

    public function getVisitIdAttribute($value)
    {
        return (int)$value;
    }
}
