<?php

namespace App\Model;

use App\Actions\PrintResult;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    public $timestamps = false;
    protected $guarded = [];

    /**
     * Scope a query to candidates who have no awards yet.
     *
     * @param  Builder $query
     * @return Builder
     */
    public function scopeNotWinners($query): Builder
    {
        return $query->whereNull('award_id');
    }

    /**
     * Scope a query to candidates who have awards.
     *
     * @param  Builder $query
     * @return Builder
     */
    public function scopeWinners($query): Builder
    {
        return $query->whereNotNull('award_id');
    }

    public function award()
    {
        return $this->belongsTo(Award::class);
    }
}
