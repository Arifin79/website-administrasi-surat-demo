<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LetterTemplate extends Model
{
    protected $fillable = ['template_name', 'template_code', 'default_subject', 'body_template', 'created_by'];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function outgoingLetters(): HasMany
    {
        return $this->hasMany(OutgoingLetter::class, 'template_id');
    }
}
