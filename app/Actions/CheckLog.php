<?php

namespace App\Actions;

use App\Actions\Traits\DrawingLog;
use Illuminate\Console\Command;
use Lorisleiva\Actions\Action;

class CheckLog extends Action
{
    use DrawingLog;

    protected static $commandSignature = 'program:check-log {--type=* : test or real}';

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
        $logValid = true;

        if ($this->type == Init::CSV_TYPE_TEST) {
            return $logValid;
        }
        // 如果沒有 LOG_KEY 的紀錄就 early return
        if (env('LOG_KEY') === '') {
            return $logValid;
        }

        try {
            $file = fopen(storage_path('logs/drawing.log'), 'r');
            $contents = fread($file, filesize(storage_path('logs/drawing.log')));
            $logValid = env('LOG_KEY') == hash('sha256', $contents);
        } catch (\Exception $e) {
            dd("LOG_KEY exists, but failed to find the log file.");
        }
        return $logValid;
    }

    /**
     * @param array $logValid
     * @param Command $command
     *
     * @return void
     */
    public function consoleOutput($logValid, Command $command)
    {
        if (!$logValid) {
            // kill the entire starting process
            dd('The Log has been tampered with!');
        }
        $command->comment('The Log is OK!');
    }
}
