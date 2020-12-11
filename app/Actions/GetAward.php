<?php

namespace App\Actions;

use App\Model\Award;
use App\Exceptions\Model\ResourceErrorException;
use App\Http\Resources\Award as ResourcesAward;
use App\Http\Resources\Winner;
use Lorisleiva\Actions\Action;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

/**
 * 列出指定獎項的得獎者
 *
 * @OA\Get(
 *     path = "/api/award",
 *     summary = "列出指定獎項的得獎者",
 *     description = "列出指定獎項的得獎者",
 *     tags = {"獎品"},
 *     @OA\Parameter(
 *         name = "awardName",
 *         in = "query",
 *         description = "獎項名稱",
 *         required = true,
 *         example = "一獎"
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
 *          description = "獎項未有得獎者",
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
 *                  example = "目前此獎項尚未有中獎者！",
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
class GetAward extends Action
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
        ];
    }

    /**
     * Execute the action and return a result.
     *
     * @param string $awardName
     *
     * @return Award
     * @throws ResourceErrorException|ResourceNotFoundException
     */
    public function handle(string $awardName)
    {
        $award = Award::where('name', $awardName)->with('candidates')->first();
        if (is_null($award)) {
            throw new ResourceNotFoundException('找不到對應的獎項！');
        }
        if ($award->candidates->isEmpty()) {
            throw new ResourceErrorException('目前此獎項尚未有中獎者！');
        }
        return $award;
    }

    /**
     * response 控制
     *
     * @param Award $award
     *
     * @return ResourcesAward
     */
    public function response(Award $award)
    {
        return new ResourcesAward($award);
    }
}
