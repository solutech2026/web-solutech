<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'person_id', 'period', 'academic_year', 'grade_level',
        'file_path', 'file_name', 'average', 'subjects_grades'
    ];

    protected $casts = [
        'subjects_grades' => 'array',
    ];

    public function person()
    {
        return $this->belongsTo(Person::class);
    }
}