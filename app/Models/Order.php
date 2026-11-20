<?php

namespace App\Models;

use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    /** @use HasFactory<OrderFactory> */
    use HasFactory;

    public const STATUS_PENDING = 'pending';

    public const STATUS_CONFIRMED = 'confirmed';

    public const STATUS_READY = 'ready';

    public const STATUS_DELIVERED = 'delivered';

    public const STATUS_CANCELLED = 'cancelled';

    /**
     * Customer-facing labels for each order status.
     *
     * @var array<string, string>
     */
    public const STATUS_LABELS = [
        self::STATUS_PENDING => 'Awaiting confirmation',
        self::STATUS_CONFIRMED => 'Confirmed',
        self::STATUS_READY => 'Ready for dispatch',
        self::STATUS_DELIVERED => 'Delivered',
        self::STATUS_CANCELLED => 'Cancelled',
    ];

    protected $fillable = [
        'number',
        'user_id',
        'name',
        'phone',
        'email',
        'notes',
        'subtotal',
        'status',
        'whatsapp_sent_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'integer',
            'whatsapp_sent_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'number';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Generate a unique order reference, e.g. ELK-482913.
     */
    public static function generateNumber(): string
    {
        do {
            $number = 'ELK-'.random_int(100000, 999999);
        } while (static::query()->where('number', $number)->exists());

        return $number;
    }

    public static function statuses(): array
    {
        return array_keys(self::STATUS_LABELS);
    }

    public function statusLabel(): string
    {
        return self::STATUS_LABELS[$this->status] ?? ucfirst($this->status);
    }

    /**
     * Customer-facing order status steps (chronological, excluding cancelled).
     *
     * @return array<string, string>
     */
    public function statusSteps(): array
    {
        return self::STATUS_LABELS;
    }

    /**
     * Compose the itemised order message for the prefilled WhatsApp chat.
     */
    public function waMessage(): string
    {
        $lines = ["Hello Elkris Foods, I would like to place order {$this->number}:"];

        foreach ($this->items as $item) {
            $lines[] = sprintf(
                '- %dx %s (%s %s)',
                $item->quantity,
                $item->product_name,
                $item->size_label,
                $item->pack_type,
            );
        }

        $lines[] = "Name: {$this->name}";
        $lines[] = "Phone: {$this->phone}";

        if ($this->notes) {
            $lines[] = "Notes: {$this->notes}";
        }

        return implode("\n", $lines);
    }

    /**
     * Build the wa.me deep link for this order.
     */
    public function waUrl(): ?string
    {
        $number = Setting::get('contact.whatsapp');

        if (! $number) {
            return null;
        }

        $digits = preg_replace('/[^\d]/', '', $number) ?? '';
        $withCountry = str_starts_with($digits, '234')
            ? $digits
            : '234'.preg_replace('/^0/', '', $digits);

        return 'https://wa.me/'.$withCountry.'?text='.urlencode($this->waMessage());
    }
}
