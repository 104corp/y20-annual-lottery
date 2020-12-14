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
    public const CSV_TYPE_REAL = 'real';

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
        $type = $command->option('type')[0] ?? Init::CSV_TYPE_REAL;
        return [
            'type' => $type,
        ];
    }

    /**
     * Execute the action and return a result.
     *
     * @return boolean
     */
    public function handle(): array
    {
        $response = [
            'valid' => true,
            'succeeded' => true,
        ];
        if ((Award::all())->isNotEmpty() || (Candidate::all())->isNotEmpty()) {
            $response['succeeded'] = false;
            return $response;
        }
        $insertCandidateData = $this->handleCsvData('candidates', $this->type);
        if (
            $this->type != self::CSV_TYPE_TEST &&
            $this->isGivenDataNotValid($insertCandidateData)
        ) {
            $response['valid'] = false;
            return $response;
        }
        Candidate::insert($insertCandidateData);

        $insertAwardData = $this->handleCsvData('awards', $this->type);
        Award::insert($insertAwardData);
        return $response;
    }

    /**
     * @param array $response
     * @param Command $command
     *
     * @return void
     */
    public function consoleOutput($response, Command $command)
    {
        if (!$response['succeeded']) {
            $command->comment('The data is already initialized!');
            return;
        }
        if (!$response['valid']) {
            // kill the entire starting process
            dd('The givern data is not correct!');
        }
        $command->comment('The data has been initialized sucessfully!');
    }

    public function isGivenDataNotValid(array $data)
    {
        $hashKey = env('HASH_KEY');
        return $hashKey !== hash('sha256', json_encode($data));
    }
}
