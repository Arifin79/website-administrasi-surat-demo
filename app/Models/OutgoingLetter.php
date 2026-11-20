<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OutgoingLetter extends Model
{
    protected $fillable = [
        'letter_number', 'letter_date', 'recipient', 'subject', 'creation_type',
        'template_id', 'letter_body_html', 'status', 'file_path', 'user_id',
    ];

    protected $casts = ['letter_date' => 'date'];

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(LetterTemplate::class, 'template_id');
    }

    public static function nextLetterNumber(): string
    {
        $year  = (int) date('Y');
        $month = (int) date('m');
        $count = self::whereYear('letter_date', $year)->count() + 1;
        $roman = ['', 'I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'];
        return str_pad($count, 3, '0', STR_PAD_LEFT) . '/DRB/' . $roman[$month] . '/' . $year;
    }

    public static function existsForYear(string $number, int $year, ?int $excludeId = null): bool
    {
        return self::where('letter_number', $number)
            ->whereYear('letter_date', $year)
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
            ->exists();
    }
}
