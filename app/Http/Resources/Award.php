<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     type="object",
 *     schema="Award.Award",
 *     title="Award.Award",
 *     description="獎項",
 *     @OA\Property(
 *         property = "name",
 *         type = "string",
 *         description = "獎項名",
 *         example = "一獎",
 *     ),
 *     @OA\Property(
 *         property = "money",
 *         type = "integer",
 *         description = "金額",
 *         example = 100000,
 *     ),
 *     @OA\Property(
 *         property = "limit",
 *         type = "integer",
 *         description = "獎品剩餘數量",
 *         example = 10,
 *     ),
 *     @OA\Property(
 *         property = "memberList",
 *         type = "array",
 *         description = "得獎人們",
 *         @OA\Items(
 *             ref = "#/components/schemas/Candidate.Candidate",
 *         ),
 *     ),
 * )
 */
class Award extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'awardName' => $this->name,
            'money' => $this->money,
            'limit' => (int) $this->number,
            'memberList' => Candidate::collection($this->candidates),
        ];
    }
}
