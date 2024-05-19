<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Todo extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'due_date',
        'note_id',
        'user_id'

    ];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function note(): BelongsTo {
        return $this->belongsTo(Note::class);
    }

    public function tags(): BelongsToMany {
        return $this->belongsToMany(Tag::class);
    }

    public function images(): HasMany {
        return $this->hasMany(Image::class);
    }
}
