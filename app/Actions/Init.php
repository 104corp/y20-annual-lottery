<?php

namespace App\Actions;

use App\Actions\Traits\HandleCsv;
use App\Exceptions\Model\ResourceErrorException;
use App\Model\Award;
use App\Model\Candidate;
use Lorisleiva\Actions\Action;

/**
 * 把 CSV 資料放進 sqlite 裡
 *
 * @OA\Post(
 *     path = "/api/init",
 *     summary = "把 CSV 資料放進 sqlite 裡",
 *     description = "把 CSV 資料放進 sqlite 裡",
 *     tags = {"開始"},
 *     @OA\Response(
 *         response = "200",
 *         description = "正常回傳",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property = "data",
 *                 type = "boolean",
 *                 description = "新增後結果",
 *                 example = true
 *             ),
 *         ),
 *     ),
 * )
 */
class Init extends Action
{
    use HandleCsv;

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
        Award::insert($insertCandidateData);
    }

    public function response()
    {
        return response(['data' => true]);
    }
}
