<?php

namespace App\Actions;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Lorisleiva\Actions\Action;

class PrintResult extends Action
{
    protected static $commandSignature = 'program:print-result';

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
        $winnerList = [];
        $succeeded = true;
        try {
            $awards = (new GetAllWinners())->run();
            $winnerList = $this->handleWinnerList($awards);

            // 寫出得獎名單
            $this->writeCsv($winnerList);
        } catch (\Exception $e) {
            $succeeded = false;
        }

        return [
            'succeeded' => $succeeded,
            'winnerList' => $winnerList,
        ];
    }

    /**
     * @param Collection $awards
     *
     * @return array
     */
    private function handleWinnerList(Collection $awards): array
    {
        $winnerList = [];
        foreach ($awards as $award) {
            foreach ($award->candidates as $candidate) {
                $winnerData = [];
                $winnerData[] = $candidate->staff_code;
                $winnerData[] = $candidate->staff_name;
                $winnerData[] = $candidate->department;
                $winnerData[] = $candidate->on_board_date;
                $winnerData[] = $award->name;
                $winnerData[] = $award->amount_of_money;
                $winnerList[] = $winnerData;
            }
        }
        return $winnerList;
    }

    /**
     * 寫出得獎名單
     * @param array $winnerList
     *
     * @return void
     */
    private function writeCsv(array $winnerList)
    {
        $fp = fopen(storage_path('app/winnerList.csv'), 'w');
        fputcsv($fp, [
            '員工編號',
            '員工姓名',
            '部門名稱',
            '到職日期',
            '獎項',
            '金額'
        ]);
        foreach ($winnerList as $fields) {
            fputcsv($fp, $fields);
        }
        fclose($fp);
    }

    /**
     * @param array $result
     * @param Command $command
     *
     * @return void
     */
    public function consoleOutput($result, Command $command)
    {
        if ($result['succeeded']) {
            $totalWinners = count($result['winnerList']);
            $command->comment("Result has been updated! Total winners: $totalWinners people.");
            return;
        }
        $command->comment("There are no winners so far.");
    }
}
