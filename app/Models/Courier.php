<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Courier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'level',
        'is_active',
        'registered_at',
    ];

    protected function casts(): array
    {
        return [
            'level' => 'integer',
            'is_active' => 'boolean',
            'registered_at' => 'datetime',
        ];
    }

    /**
     * Search couriers by name using multi-keyword matching.
     * Splits query terms so keywords like "budi agung" match full names
     * such as "Budiono Hadi Agung" regardless of intervening words.
     */
    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if (empty($search)) {
            return $query;
        }

        $keywords = preg_split('/\s+/', trim($search), -1, PREG_SPLIT_NO_EMPTY);

        return $query->where(function (Builder $q) use ($keywords) {
            foreach ($keywords as $word) {
                $q->where('name', 'like', "%{ $word }%");
            }
        });
    }

    /**
     * Filter couriers by their experience level (1-5).
     * Accepts comma-separated values (e.g. "2,3") or array inputs,
     * sanitizing entries against non-numeric and out-of-range values.
     */
    public function scopeFilterByLevel(Builder $query, mixed $levels): Builder
    {
        if (empty($levels)) {
            return $query;
        }

        if (is_string($levels)) {
            $levels = explode(',', $levels);
        }

        $levels = array_values(array_filter(
            array_map('trim', (array) $levels),
            fn ($val) => is_numeric($val) && (int) $val >= 1 && (int) $val <= 5
        ));

        if (! empty($levels)) {
            $query->whereIn('level', $levels);
        }

        return $query;
    }
}
