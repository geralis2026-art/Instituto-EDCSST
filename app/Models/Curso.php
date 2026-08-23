<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * Programa de capacitación ofrecido por el instituto.
 *
 * `intensidad_horaria` es el valor oficial de horas del curso; se copia
 * a cada certificado emitido para preservar el dato aunque el curso
 * cambie después.
 */
class Curso extends Model
{
    use HasFactory;

    protected $table = 'cursos';

    protected $fillable = [
        'user_id',
        'categoria_id',
        'nombre',
        'slug',
        'descripcion_corta',
        'duracion',
        'intensidad_horaria',
        'tiene_aula_virtual',
        'imagen',
        'destacado',
        'activo',
    ];

    protected $casts = [
        'destacado'          => 'boolean',
        'activo'             => 'boolean',
        'tiene_aula_virtual' => 'boolean',
        'intensidad_horaria' => 'integer',
    ];

    /** Genera el slug automáticamente al guardar e invalida el caché del home. */
    protected static function booted(): void
    {
        // Sin PropietarioScope: los cursos son un catálogo centralizado que
        // solo admin administra (ver EnsureUserCanViewCursos), así que todos
        // los empleados deben poder verlos y usarlos al emitir certificados.
        static::saved(fn () => Cache::forget('home_cursos_destacados'));
        static::deleted(fn () => Cache::forget('home_cursos_destacados'));

        static::saving(function ($curso) {
            if (empty($curso->slug)) {
                $base = Str::slug($curso->nombre);
                $slug = $base;
                $i    = 1;

                while (
                    static::where('slug', $slug)
                        ->when($curso->exists, fn ($q) => $q->where('id', '!=', $curso->id))
                        ->exists()
                ) {
                    $slug = "{$base}-{$i}";
                    $i++;
                }

                $curso->slug = $slug;
            }
        });
    }

    /** Categoría a la que pertenece el curso. */
    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    /** Empleado (admin/instructor) propietario de este curso. */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Certificados emitidos para este curso. */
    public function certificados(): HasMany
    {
        return $this->hasMany(Certificado::class);
    }

    /** Capacitados que han recibido certificado de este curso (vía certificados). */
    public function capacitados(): BelongsToMany
    {
        return $this->belongsToMany(Capacitado::class, 'certificados')
                    ->withPivot('codigo_unico', 'fecha_emision', 'intensidad_horaria', 'activo')
                    ->withTimestamps();
    }

    /** Módulos del aula virtual de este curso. */
    public function modulos(): HasMany
    {
        return $this->hasMany(Modulo::class)->orderBy('orden');
    }

    /** Matrículas (capacitados asignados) de este curso. */
    public function matriculas(): HasMany
    {
        return $this->hasMany(Matricula::class);
    }

    /** Quiz de validación de este curso (aula virtual). */
    public function quiz(): HasOne
    {
        return $this->hasOne(Quiz::class);
    }

    /** Cursos activos. */
    public function scopeActivos(Builder $query): Builder
    {
        return $query->where('activo', true);
    }

    /** Cursos activos marcados como destacados (se muestran en el home). */
    public function scopeDestacados(Builder $query): Builder
    {
        return $query->where('destacado', true)->where('activo', true);
    }

    /** URL de la imagen del curso; fallback a imagen por defecto si no tiene. */
    public function getImagenUrlAttribute(): string
    {
        if (!$this->imagen || !str_contains($this->imagen, '/')) {
            return asset('img/curso-default.svg');
        }

        [$type, $filename] = explode('/', $this->imagen, 2);

        return route('uploads.serve', compact('type', 'filename'));
    }
}
