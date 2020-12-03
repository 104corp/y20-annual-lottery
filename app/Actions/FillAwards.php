<?php

namespace App\Actions;

use App\Actions\Traits\HandleCsv;
use App\Http\Resources\Award as ResourcesAward;
use App\Model\Award;
use Illuminate\Support\Collection;
use Lorisleiva\Actions\Action;

/**
 * 列出所有獎項
 *
 * @OA\Get(
 *     path = "/api/award/all",
 *     summary = "列出所有參加者",
 *     description = "列出所有參加者",
 *     tags = {"獎品"},
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
 *                         ref = "#/components/schemas/Award.Award",
 *                     ),
 *                 ),
 *             ),
 *         ),
 *     ),
 * )
 */
class FillAwards extends Action
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
     * @return Collection
     */
    public function handle()
    {
        $awards = Award::all();
        return $awards;
    }

    /**
     * response 控制
     * @param Collection $awards
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function response($awards)
    {
        return ResourcesAward::collection($awards);
    }
}
