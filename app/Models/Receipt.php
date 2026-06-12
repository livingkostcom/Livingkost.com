<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Receipt extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'invoice_id',
        'receipt_number',
        'pdf_path',
        'issued_at',
        'created_by',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

