<?php

namespace App\Actions;

use App\Actions\Traits\HandleCsv;
use App\Http\Resources\Candidate as ResourcesCandidate;
use App\Model\Candidate;
use Illuminate\Support\Collection;
use Lorisleiva\Actions\Action;

/**
 * 列出所有參加者
 *
 * @OA\Get(
 *     path = "/api/candidate/all",
 *     summary = "列出所有參加者",
 *     description = "列出所有參加者",
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
 * )
 */
class FillCandidates extends Action
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
        $candidates = Candidate::all();
        return $candidates;
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
