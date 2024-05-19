<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Register extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'created_at', 'is_public', 'user_id'];




    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function notes() : HasMany{
        return $this->HasMany(Note::class);
    }


}
