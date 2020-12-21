<?php

namespace App\Actions;

use Illuminate\Console\Command;
use Lorisleiva\Actions\Action;

class CheckData extends Action
{
    protected static $commandSignature = 'program:check-data {--type=* : test or real}';

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
        $this->response = [
            'valid' => true,
            'logValid' => true,
            'succeeded' => true,
        ];
    }

    /**
     * Execute the action and return a result.
     *
     * @return mixed
     */
    public function handle()
    {
        // 如果是從 Init 那裡委任過來的， $this->type 會是 array
        if (is_array($this->type)) {
            $this->type = $this->type[0];
        }

        $valid = true;
        if ($this->type != Init::CSV_TYPE_TEST) {
            $file = fopen(storage_path('app/candidates.csv'), 'r');
            $contents = fread($file, filesize(storage_path('app/candidates.csv')));
            $valid = env('HASH_KEY') == hash('sha256', $contents);
        }
        return $valid;
    }

    /**
     * @param array $response
     * @param Command $command
     *
     * @return void
     */
    public function consoleOutput($valid, Command $command)
    {
        if (!$valid) {
            // kill the entire starting process
            dd('The given data is not correct!');
        }
        $command->comment('The given data is OK!');
    }
}
