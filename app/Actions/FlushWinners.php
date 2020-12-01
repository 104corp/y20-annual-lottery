<?php

namespace App\Actions;

use App\Model\Candidate;
use Lorisleiva\Actions\Action;

class FlushWinners extends Action
{
    /**
     * Determine if the user is authorized to make this action.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the action.
     *
     * @return array
     */
    public function rules()
    {
        return [];
    }

    /**
     * Execute the action and return a result.
     *
     * @return mixed
     */
    public function handle()
    {
        $winners = Candidate::winners()->with('award')->get();
        $winners->each(function ($winner) {
            $award = $winner->award;
            $award->increment('number');
        });

        Candidate::winners()->update(['award_id' => null]);
    }

    /**
     * 控制 response
     */
    public function response()
    {
        return response(['data' => true]);
    }
}
