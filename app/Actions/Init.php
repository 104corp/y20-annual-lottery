<?php

namespace App\Actions;

use App\Actions\Traits\HandleCsv;
use App\Model\Award;
use App\Model\Candidate;
use Lorisleiva\Actions\Action;

class Init extends Action
{
    use HandleCsv;

    protected static $commandSignature = 'program:init';

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
     * @return void
     */
    public function handle()
    {
        if ((Award::all())->isNotEmpty() || (Candidate::all())->isNotEmpty()) {
            return;
        }
        $insertAwardData = $this->handleCsvData('awards');
        Award::insert($insertAwardData);
        $insertCandidateData = $this->handleCsvData('candidates');
        Candidate::insert($insertCandidateData);
    }
}
