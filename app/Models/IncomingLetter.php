<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IncomingLetter extends Model
{
    protected $fillable = [
        'agenda_number', 'reference_number', 'letter_date', 'received_date',
        'sender', 'subject', 'status', 'file_path', 'user_id',
    ];

    protected $casts = ['letter_date' => 'date', 'received_date' => 'date'];

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function nextAgendaNumber(): string
    {
        $year  = date('Y');
        $count = self::whereYear('created_at', $year)->count() + 1;
        return 'AG-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}
