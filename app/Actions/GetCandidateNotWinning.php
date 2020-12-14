<?php

namespace App\Actions;

use App\Http\Resources\Candidate as ResourcesCandidate;
use App\Model\Candidate;
use Illuminate\Support\Collection;
use Lorisleiva\Actions\Action;

/**
 * 列出沒得獎的參加者
 *
 * @OA\Get(
 *     path = "/api/candidate/no-win",
 *     summary = "列出沒得獎的參加者",
 *     description = "列出沒得獎的參加者",
 *     tags = {"參加者"},
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
 *          response = "401",
 *          description = "apim 驗證沒過",
 *          @OA\JsonContent(
 *              @OA\Property(
 *                  property = "code",
 *                  type = "string",
 *                  description = "錯誤代碼，errorCode 為五碼的數字(string)",
 *                  default = "00000",
 *                  example = "00006",
 *              ),
 *              @OA\Property(
 *                  property = "message",
 *                  type = "string",
 *                  description = "錯誤訊息",
 *                  default = "",
 *                  example = "不允許的請求，只可透過 APIM2 呼叫此 API",
 *             ),
 *             @OA\Property(
 *                  property = "details",
 *                  type = "array",
 *                  description = "額外錯誤資訊",
 *                  @OA\Items(type = "object"),
 *             ),
 *         ),
 *     ),
 * )
 */
class GetCandidateNotWinning extends Action
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
        return [];
    }

    /**
     * Execute the action and return a result.
     *
     * @return Collection
     */
    public function handle()
    {
        return Candidate::notWinners()->get();
    }

    /**
     * response 控制
     * @param Collection $candidates
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function response(Collection $candidates)
    {
        return ResourcesCandidate::collection($candidates);
    }
}
