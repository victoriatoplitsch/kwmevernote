<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Note extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'register_id'];

    public function register(): BelongsTo {
        return $this->belongsTo(Register::class);
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function todos(): HasMany {
        return $this->hasMany(Todo::class);
    }
    public function tags(): BelongsToMany {
        return $this->belongsToMany(Tag::class);
    }

    public function images(): HasMany {
        return $this->hasMany(Image::class);
    }
}
