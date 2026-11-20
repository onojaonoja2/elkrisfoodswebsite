<?php

namespace App\Models;

use Database\Factories\DistributorLeadFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DistributorLead extends Model
{
    /** @use HasFactory<DistributorLeadFactory> */
    use HasFactory;

    protected $fillable = [
        'reference',
        'name',
        'company',
        'phone',
        'email',
        'state',
        'lga',
        'business_type',
        'monthly_order',
        'locations',
        'status',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $lead): void {
            if (! $lead->reference) {
                $lead->reference = static::generateReference();
            }
        });
    }

    /**
     * Generate a unique applicant reference, e.g. DST-K4Q7M2.
     */
    public static function generateReference(): string
    {
        do {
            $reference = 'DST-'.strtoupper(Str::random(6));
        } while (static::query()->where('reference', $reference)->exists());

        return $reference;
    }

    /**
     * Customer-facing labels for each pipeline status.
     *
     * @return array<string, string>
     */
    public static function statusLabels(): array
    {
        return [
            'new' => 'Application received',
            'in_progress' => 'Under review',
            'approved' => 'Approved',
            'rejected' => 'Not approved',
            'archived' => 'Archived',
        ];
    }

    public function statusLabel(): string
    {
        return self::statusLabels()[$this->status] ?? ucfirst($this->status);
    }
}
