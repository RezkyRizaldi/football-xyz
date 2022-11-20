<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Game extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $casts = [
        'date' => 'datetime:Y-m-d H:i:s',
    ];

    protected $fillable = ['team_home_id', 'team_away_id', 'date', 'home_score', 'away_score'];

    public function teamHome(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team_home_id');
    }

    public function teamAway(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team_away_id');
    }

    public function goalScorers(): HasMany
    {
        return $this->hasMany(GoalScorer::class);
    }
}
