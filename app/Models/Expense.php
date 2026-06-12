<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use HasFactory, SoftDeletes, \App\Models\Concerns\BelongsToOwner;

    protected $fillable = [
        'owner_id',
        'property_id',
        'title',
        'description',
        'amount',
        'expense_date',
        'category',
        'receipt_image',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function getCategoryLabel(string $category): string
    {
        return match ($category) {
            'maintenance' => 'Perbaikan',
            'utility' => 'Utilitas (Listrik/Air)',
            'cleaning' => 'Kebersihan',
            'supplies' => 'Perlengkapan',
            'salary' => 'Gaji',
            'tax' => 'Pajak',
            'insurance' => 'Asuransi',
            'other' => 'Lainnya',
            default => $category,
        };
    }
}
