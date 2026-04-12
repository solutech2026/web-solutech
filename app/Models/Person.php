<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Person extends Model
{
    use HasFactory;

    protected $table = 'persons';

    protected $fillable = [
        'user_id',
        'category',
        'subcategory',
        'name',
        'lastname',
        'document_id',
        'email',
        'phone',
        'photo',
        'photo_url',
        'gender',
        'birth_date',
        'company_id',
        'nfc_card_id',
        'position',
        'department',
        'bio',
        'grade',
        'section',
        'education_level',
        'academic_year',
        'grade_level',
        'period',
        'average_grade',
        'grades_documents',
        'emergency_contact_name',
        'emergency_phone',
        'allergies',
        'medical_conditions',
        'schedule',
        'teacher_type',
        'subjects',
        'last_access_at',
        'avatar_color',
        'bio_url'
    ];

    protected $casts = [
        'birth_date' => 'date',
        'grades_documents' => 'array',
        'schedule' => 'array',
        'subjects' => 'array',
        'last_access_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ============================================
    // BOOT
    // ============================================
    
    protected static function boot()
    {
        parent::boot();

        // Generar bio_url automáticamente al crear
        static::creating(function ($person) {
            if (empty($person->bio_url)) {
                $person->bio_url = self::generateUniqueBioUrl();
            }
        });
    }

    // ============================================
    // ACCESSORS (Atributos virtuales)
    // ============================================
    
    /**
     * Get the full name attribute.
     */
    public function getFullNameAttribute()
    {
        return $this->name . ($this->lastname ? ' ' . $this->lastname : '');
    }

    /**
     * Get the photo URL attribute.
     */
    public function getPhotoUrlAttribute()
    {
        // Si tiene photo_url guardado, usarlo
        if (isset($this->attributes['photo_url']) && $this->attributes['photo_url']) {
            return $this->attributes['photo_url'];
        }
        
        // Si tiene photo guardada en storage, usar storage url
        if (isset($this->attributes['photo']) && $this->attributes['photo']) {
            return Storage::url($this->attributes['photo']);
        }
        
        // Si no tiene foto, generar avatar con iniciales
        return $this->getDefaultAvatar();
    }

    /**
     * Get default avatar based on name.
     */
    private function getDefaultAvatar()
    {
        $name = urlencode($this->full_name);
        return "https://ui-avatars.com/api/?background=6366f1&color=fff&rounded=true&size=100&name={$name}";
    }

    /**
     * Get category label.
     */
    public function getCategoryLabelAttribute()
    {
        return $this->category == 'employee' ? 'Empleado' : 'Personal Escolar';
    }

    /**
     * Get subcategory label.
     */
    public function getSubcategoryLabelAttribute()
    {
        $labels = [
            'student' => 'Estudiante',
            'teacher' => 'Docente',
            'administrative' => 'Administrativo'
        ];
        return $labels[$this->subcategory] ?? '';
    }

    /**
     * Get grade level label.
     */
    public function getGradeLevelLabelAttribute()
    {
        $grades = [
            '1st' => '1er Grado',
            '2nd' => '2do Grado',
            '3rd' => '3er Grado',
            '4th' => '4to Grado',
            '5th' => '5to Grado',
            '6th' => '6to Grado',
            '7th' => '1er Año',
            '8th' => '2do Año',
            '9th' => '3er Año',
            '10th' => '4to Año',
            '11th' => '5to Año',
        ];
        return $grades[$this->grade_level] ?? $this->grade_level;
    }

    /**
     * Get period label.
     */
    public function getPeriodLabelAttribute()
    {
        $periods = [
            'first' => 'Primer Lapso',
            'second' => 'Segundo Lapso',
            'third' => 'Tercer Lapso',
        ];
        return $periods[$this->period] ?? $this->period;
    }

    /**
     * Get teacher type label.
     */
    public function getTeacherTypeLabelAttribute()
    {
        $types = [
            'regular' => 'Docente Regular',
            'substitute' => 'Docente Suplente',
            'special_education' => 'Educación Especial',
            'part_time' => 'Medio Tiempo',
        ];
        return $types[$this->teacher_type] ?? $this->teacher_type;
    }

    /**
     * Get bio full URL attribute.
     */
    public function getBioFullUrlAttribute()
    {
        return $this->bio_url ? url('/bio/' . $this->bio_url) : null;
    }

    // ============================================
    // RELACIONES
    // ============================================
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function nfcCard()
    {
        return $this->belongsTo(NfcCard::class, 'nfc_card_id');
    }

    public function accessLogs()
    {
        return $this->hasMany(AccessLog::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function reportCards()
    {
        return $this->hasMany(ReportCard::class);
    }

    // ============================================
    // SCOPES
    // ============================================
    
    public function scopeEmployees($query)
    {
        return $query->where('category', 'employee');
    }

    public function scopeSchoolStaff($query)
    {
        return $query->where('category', 'school');
    }

    public function scopeStudents($query)
    {
        return $query->where('subcategory', 'student');
    }

    public function scopeTeachers($query)
    {
        return $query->where('subcategory', 'teacher');
    }

    public function scopeAdministrative($query)
    {
        return $query->where('subcategory', 'administrative');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeHasNfc($query)
    {
        return $query->whereNotNull('nfc_card_id');
    }

    public function scopeWithoutNfc($query)
    {
        return $query->whereNull('nfc_card_id');
    }

    // ============================================
    // MÉTODOS AUXILIARES
    // ============================================
    
    public function hasNfcCard()
    {
        return !is_null($this->nfc_card_id);
    }

    public function updateAverageGrade()
    {
        $reportCards = $this->reportCards;
        if ($reportCards->count() > 0) {
            $average = $reportCards->avg('average');
            $this->update(['average_grade' => $average]);
        }
    }

    /**
     * Generar URL única para la biografía pública
     */
    public static function generateUniqueBioUrl()
    {
        do {
            $url = Str::random(12);
        } while (self::where('bio_url', $url)->exists());
        
        return $url;
    }

    /**
     * Regenerar bio_url (si es necesario)
     */
    public function regenerateBioUrl()
    {
        $this->bio_url = self::generateUniqueBioUrl();
        $this->save();
        
        return $this->bio_url;
    }
}
