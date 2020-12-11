<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     type="object",
 *     schema="Candidate.Winner",
 *     title="Candidate.Winner",
 *     description="得獎者",
 *     @OA\Property(
 *         property = "awardName",
 *         type = "string",
 *         description = "獎項名",
 *         example = "一獎",
 *     ),
 *     @OA\Property(
 *         property = "winners",
 *         type = "array",
 *         description = "得獎人們",
 *         @OA\Items(
 *             ref = "#/components/schemas/Candidate.Candidate",
 *         ),
 *     ),
 * )
 */
class Winner extends JsonResource
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
