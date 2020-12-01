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
 *         property = "number",
 *         type = "integer",
 *         description = "獎品剩餘數量",
 *         example = 10,
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
            'name' => $this->name,
            'money' => (int) $this->amount_of_money,
            'number' => (int) $this->number,
        ];
    }
}
