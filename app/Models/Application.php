<?php

namespace App\Models;

use App\Models\Concerns\RecordsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Postulación de un estudiante a una oferta.
 */
class Application extends Model
{
    use HasFactory, RecordsActivity;

    protected $fillable = [
        'student_id',
        'offer_id',
        'reviewed_by',
        'status',
        'cv_path',
        'motivation',
        'matching_score',
        'decision_at',
    ];

    protected function casts(): array
    {
        return [
            'matching_score' => 'decimal:2',
            'decision_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function practice(): HasOne
    {
        return $this->hasOne(Practice::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ApplicationMessage::class)->oldest();
    }

    public function activityLabel(): string
    {
        return 'Postulación #'.$this->id;
    }
}
