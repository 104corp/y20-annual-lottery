<?php

namespace App\Actions;

use App\Actions\Traits\DrawingLog;
use App\Model\Award;
use App\Model\Candidate;
use App\Exceptions\Model\ResourceErrorException;
use App\Http\Resources\Award as ResourcesAward;
use Illuminate\Support\Collection;
use Lorisleiva\Actions\Action;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

/**
 * 抽獎
 *
 * @OA\Post(
 *     path = "/api/draw",
 *     summary = "抽獎",
 *     description = "抽獎",
 *     tags = {"抽獎"},
 *     @OA\RequestBody(
 *         description = "API傳入的內容",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property = "name",
 *                 type = "string",
 *                 description = "獎項名稱",
 *                 example = "一獎"
 *             ),
 *             @OA\Property(
 *                 property = "number",
 *                 type = "integer",
 *                 description = "抽幾個人，可不傳（不傳就是該獎項一次全抽）",
 *                 example = 1
 *             ),
 *         ),
 *     ),
 *     @OA\Response(
 *         response = "200",
 *         description = "正常回傳",
 *         @OA\MediaType(
 *             mediaType = "application/json",
 *             @OA\Schema(
 *                 @OA\Property(
 *                     property = "data",
 *                     ref = "#/components/schemas/Award.Award",
 *                 ),
 *             ),
 *         ),
 *     ),
 *     @OA\Response(
 *          response = "400",
 *          description = "此獎項剩餘數量不足",
 *          @OA\JsonContent(
 *              @OA\Property(
 *                  property = "status",
 *                  type = "integer",
 *                  description = "status code",
 *                  default = 500,
 *                  example = 400,
 *              ),
 *              @OA\Property(
 *                  property = "message",
 *                  type = "string",
 *                  description = "錯誤訊息",
 *                  default = "",
 *                  example = "此獎項剩餘數量不足，抽不出那麼多人！",
 *             ),
 *             @OA\Property(
 *                  property = "details",
 *                  description = "額外錯誤資訊",
 *                  example = {},
 *             ),
 *         ),
 *     ),
 *     @OA\Response(
 *          response = "500",
 *          description = "找不到獎項",
 *          @OA\JsonContent(
 *              @OA\Property(
 *                  property = "status",
 *                  type = "integer",
 *                  description = "status code",
 *                  default = 500,
 *                  example = 500,
 *              ),
 *              @OA\Property(
 *                  property = "message",
 *                  type = "string",
 *                  description = "錯誤訊息",
 *                  default = "",
 *                  example = "找不到對應的獎項！",
 *             ),
 *             @OA\Property(
 *                  property = "details",
 *                  description = "額外錯誤資訊",
 *                  example = {},
 *             ),
 *         ),
 *     ),
 * )
 */
class Draw extends Action
{
    use DrawingLog;

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
        return [
            'name' => 'required',
            'number' => 'nullable|gte:1',
        ];
    }

    /**
     * Execute the action and return a result.
     * @param string $name
     * @param int $number
     *
     * @return Award 獎
     * @throws ResourceNotFoundException|ResourceErrorException
     */
    public function handle($name, $number): Award
    {
        $award = $this->handleAward($name, $number);
        $number = $number ?? $award->number;

        $candidates = Candidate::notWinners()->get();

        if ($candidates->isEmpty()) {
            throw new ResourceErrorException('參加者都已有獎項！');
        }

        $shuffledCandidates = $candidates->shuffle();

        // 除了基本 shuffle 一次，再隨機 shuffle 1-4 次
        for ($i = 0; $i < rand(1, 4); $i++) {
            $shuffledCandidates = $shuffledCandidates->shuffle();
        }

        // 在 shuffle 過後的名單內再 random 挑出需要數量的獲獎者
        $winners = $shuffledCandidates->random($number);

        $this->updateCandidatesAsWinners($winners, $award);
        $this->udateAwardNumber($award, $number);

        // 塞給 model 這次的贏家
        $award->winners = $winners;

        // 寫 log
        $this->logDrawing($award, $winners);

        return $award;
    }

    /**
     * response 控制
     * @param Award $award
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function response(Award $award)
    {
        return new ResourcesAward($award);
    }

    /**
     * @param string $awardName
     * @param int|null $candidateNumber 如果為 null 就不判斷
     *
     * @return Award
     * @throws ResourceNotFoundException|ResourceErrorException
     */
    private function handleAward(string $awardName, ?int $candidateNumber): Award
    {
        $award = Award::where('name', $awardName)->first();
        if (is_null($award)) {
            throw new ResourceNotFoundException('找不到對應的獎項！');
        }
        if (
            $award->number <= 0 ||
            $award->number < $candidateNumber
        ) {
            throw new ResourceErrorException('此獎項剩餘數量不足，抽不出那麼多人！');
        }
        return $award;
    }

    /**
     * @param Award $award
     *
     * @return void
     */
    private function udateAwardNumber(Award $award, $candidateNumber): void
    {
        $award->number -= $candidateNumber;
        $award->save();
    }

    /**
     * @param Collection $winners
     * @param Award $award
     *
     * @return void
     */
    private function updateCandidatesAsWinners(Collection $winners, Award $award): void
    {
        $winnerIds = $winners->pluck('id');
        Candidate::whereIn('id', $winnerIds)->update(['award_id' => $award->id]);
    }
}
