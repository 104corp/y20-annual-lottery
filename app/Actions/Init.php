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
            'logValid' => true,
            'succeeded' => true,
        ];
        if ((Award::all())->isNotEmpty() || (Candidate::all())->isNotEmpty()) {
            $response['succeeded'] = false;
            return $response;
        }
        $insertCandidateData = $this->handleCsvData('candidates', $this->type);

        $response['valid'] = $this->delegateTo(CheckData::class);
        $response['logValid'] = $this->delegateTo(CheckLog::class);

        if ($this->validationFailed($response)) {
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
            dd('The given data is not correct!');
        }
        if (!$response['logValid']) {
            // kill the entire starting process
            dd('The Log has been tampered with!');
        }
        $command->comment('The data has been initialized sucessfully!');
    }

    /**
     * @param array $response
     *
     * @return bool
     */
    private function validationFailed(array $response): bool
    {
        return !$response['valid'] || !$response['logValid'];
    }
}
