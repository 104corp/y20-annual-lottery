<?php

namespace App\Actions;

use App\Model\Award;
use App\Exceptions\Model\ResourceErrorException;
use App\Http\Resources\Winner;
use Illuminate\Support\Collection;
use Lorisleiva\Actions\Action;

/**
 * 列出所有得獎者
 *
 * @OA\Get(
 *     path = "/api/winner/all",
 *     summary = "列出所有得獎者",
 *     description = "列出所有得獎者",
 *     tags = {"得獎者"},
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
 *                         ref = "#/components/schemas/Candidate.Winner",
 *                     ),
 *                 ),
 *             ),
 *         ),
 *     ),
 *     @OA\Response(
 *          response = "400",
 *          description = "目前沒有得獎者",
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
 *                  example = "目前沒有得獎者！",
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
class GetAllWinners extends Action
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
     * @throws ResourceErrorException
     */
    public function handle()
    {
        $awards = Award::with('candidates')->get();
        $awards = $awards->filter(function ($award) {
            return $award->candidates->isNotEmpty();
        });

        if ($awards->isEmpty()) {
            throw new ResourceErrorException('目前沒有得獎者！');
        }

        return $awards;
    }

    /**
     * response 控制
     *
     * @param Collection $awards
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function response(Collection $awards)
    {
        return Winner::collection($awards);
    }
}
