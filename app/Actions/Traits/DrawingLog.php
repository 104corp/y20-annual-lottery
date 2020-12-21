<?php

namespace App\Actions\Traits;

use App\Model\Award;
use App\Model\Candidate;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

trait DrawingLog
{
    /**
     * @param Award $award
     * @param Collection $candidates
     *
     * @return void
     */
    public function logDrawing(Award $award, Collection $candidates): void
    {
        if (env('APP_NAME') === 'Testing') {
            return;
        }
        $logData = $this->handleLogData($award, $candidates);
        Log::channel('drawing')->info('抽獎', $logData);

        // 紀錄新的 logHash
        $this->putPermanentEnv('LOG_KEY', $this->getNewLogHash());
    }

    /**
     * @param Award $award
     * @param Candidate $candidate
     *
     * @return void
     */
    public function logWithdrawing(Award $award, Candidate $candidate): void
    {
        if (env('APP_NAME') === 'Testing') {
            return;
        }
        $logData = $this->handleLogData($award, $candidate);
        Log::channel('drawing')->info('放棄獎項', $logData);

        // 紀錄新的 logHash
        $this->putPermanentEnv('LOG_KEY', $this->getNewLogHash());
    }

    public function logAwardCreating(string $name, int $money, int $limit): void
    {
        if (env('APP_NAME') === 'Testing') {
            return;
        }
        Log::channel('drawing')
            ->info(
                '新增獎項',
                ['name' => $name, 'money' => $money, 'limit' => $limit,]
            );

        // 紀錄新的 logHash
        $this->putPermanentEnv('LOG_KEY', $this->getNewLogHash());
    }

    /**
     * @param Award $award
     * @param mixed $candidates
     *
     * @return array
     */
    private function handleLogData(Award $award, $candidates): array
    {
        $logData = [];
        $this->addAwardInfo($logData, $award);
        $this->addCandidateInfo($logData, $candidates);

        return $logData;
    }

    /**
     * @param array $logData
     * @param Award $award
     *
     * @return void
     */
    private function addAwardInfo(array &$logData, Award $award): void
    {
        $logData['award'] = $award->name;
    }

    /**
     * @param array $logData
     * @param mixed $candidateData
     *
     * @return void
     */
    private function addCandidateInfo(array &$logData, $candidateData): void
    {
        if ($candidateData instanceof Candidate) {
            $logData['winners'][] = Arr::only(
                $candidateData->toArray(),
                ['staff_code', 'staff_name']
            );
            return;
        }

        foreach ($candidateData as $candidate) {
            $logData['winners'][] = Arr::only(
                $candidate->toArray(),
                ['staff_code', 'staff_name']
            );
        }
    }

    /**
     * 取得Log的Hash
     *
     * @return string
     */
    private function getNewLogHash(): string
    {
        $file = fopen(storage_path('logs/drawing.log'), 'r');
        $contents = fread($file, filesize(storage_path('logs/drawing.log')));
        return hash('sha256', $contents);
    }

    /**
     * 修改 env 的內容
     *
     * @param string $key
     * @param string $value
     *
     * @return void
     */
    private function putPermanentEnv(string $key, string $value): void
    {
        $envFile = app()->environmentFilePath();
        $str = file_get_contents($envFile);

        $str .= "\n"; // In case the searched variable is in the last line without \n
        $keyPosition = strpos($str, "{$key}=");
        $endOfLinePosition = strpos($str, PHP_EOL, $keyPosition);
        $oldLine = substr($str, $keyPosition, $endOfLinePosition - $keyPosition);
        $str = str_replace($oldLine, "{$key}={$value}", $str);
        $str = substr($str, 0, -1);

        $fp = fopen($envFile, 'w');
        fwrite($fp, $str);
        fclose($fp);
    }
}
