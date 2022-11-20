<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Player extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['team_id', 'name', 'height', 'weight', 'position', 'back_number'];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function goalScorers(): HasMany
    {
        return $this->hasMany(GoalScorer::class);
    }
}
