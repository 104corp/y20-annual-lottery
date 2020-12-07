<?php

namespace App\Actions;

use App\Model\Award;
use App\Model\Candidate;
use App\Exceptions\Model\ResourceErrorException;
use App\Http\Resources\Candidate as ResourcesCandidate;
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
 *                 property = "awardName",
 *                 type = "string",
 *                 description = "獎項名稱",
 *                 example = "一獎"
 *             ),
 *             @OA\Property(
 *                 property = "candidateNumber",
 *                 type = "integer",
 *                 description = "抽幾個人",
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
 *                     type = "array",
 *                     @OA\Items(
 *                         ref = "#/components/schemas/Candidate.Candidate",
 *                     ),
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
            'awardName' => 'required',
            'candidateNumber' => 'nullable|gte:1',
        ];
    }

    /**
     * Execute the action and return a result.
     * @param string $awardName
     * @param int $candidateNumber
     *
     * @return Collection 贏家
     * @throws ResourceNotFoundException|ResourceErrorException
     */
    public function handle($awardName, $candidateNumber): Collection
    {
        $candidateNumber = $candidateNumber ?? 1;
        $award = $this->handleAward($awardName, $candidateNumber);

        $candidates = Candidate::notWinners()->get();
        $shuffledCandidates = $candidates->shuffle();

        // 除了基本 shuffle 一次，再隨機 shuffle 1-4 次
        for ($i = 0; $i < rand(1, 4); $i++) {
            $shuffledCandidates = $shuffledCandidates->shuffle();
        }

        // 在 shuffle 過後的名單內再 random 挑出需要數量的獲獎者
        $winners = $shuffledCandidates->random($candidateNumber);

        $this->updateCandidatesAsWinners($winners, $award);
        $this->udateAwardNumber($award, $candidateNumber);

        return $winners;
    }

    /**
     * response 控制
     * @param Collection $winners
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function response(Collection $winners)
    {
        return ResourcesCandidate::collection($winners);
    }

    /**
     * @param string $awardName
     * @param int $candidateNumber
     *
     * @return Award
     * @throws ResourceNotFoundException|ResourceErrorException
     */
    private function handleAward(string $awardName, int $candidateNumber): Award
    {
        $award = Award::where('name', $awardName)->first();
        if (is_null($award)) {
            throw new ResourceNotFoundException('找不到對應的獎項！');
        }
        if ($award->number <= 0 || $award->number < $candidateNumber) {
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
        $award->decrement('number', $candidateNumber);
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
