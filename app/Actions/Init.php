<?php

namespace App\Actions;

use App\Actions\Traits\HandleCsv;
use App\Model\Award;
use App\Model\Candidate;
use Illuminate\Console\Command;
use Lorisleiva\Actions\Action;

class Init extends Action
{
    use HandleCsv;

    public const CSV_TYPE_TEST = 'test';

    protected static $commandSignature = 'program:init {--type=* : test or real}';

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
    public function getAttributesFromCommand(Command $command): array
    {
        $type = $command->option('type')[0] ?? Init::CSV_TYPE_TEST;
        return [
            'type' => $type,
        ];
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
        $insertAwardData = $this->handleCsvData('awards', $this->type);
        Award::insert($insertAwardData);
        $insertCandidateData = $this->handleCsvData('candidates', $this->type);
        Candidate::insert($insertCandidateData);
    }

    public function consoleOutput($result, Command $command)
    {
        $command->comment('data has been initialized!');
    }
}
