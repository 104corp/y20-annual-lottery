<?php

namespace App\Actions;

use Illuminate\Console\Command;
use Lorisleiva\Actions\Action;

class GetDataHash extends Action
{
    protected static $commandSignature = 'program:get-data-hash';

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
        $file = fopen(storage_path('app/candidates.csv'), 'r');
        $contents = fread($file, filesize(storage_path('app/candidates.csv')));
        return hash('sha256', $contents);
    }

    /**
     * @param string $hash
     * @param Command $command
     *
     * @return void
     */
    public function consoleOutput($hash, Command $command)
    {
        $command->comment("The current data hash is {$hash}");
    }
}
