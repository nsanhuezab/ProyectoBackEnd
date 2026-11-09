<?php

namespace App\Models;

use App\Models\Concerns\RecordsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasFactory, RecordsActivity;

    protected $fillable = [
        'owner_user_id',
        'razon_social',
        'rut',
        'rubro',
        'direccion',
        'sitio_web',
        'contacto_nombre',
        'contacto_email',
        'telefono',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function offers(): HasMany
    {
        return $this->hasMany(Offer::class, 'empresa_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(CompanyReview::class);
    }

    public function activityLabel(): string
    {
        return 'Empresa «'.$this->razon_social.'»';
    }
}
