<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class student extends Model
{

    use HasFactory;

    protected $fillable = [
        'id_student',
        'carrera',
        'nivel_avance'
    ];

}
