<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     type="object",
 *     schema="Candidate.Candidate",
 *     title="Candidate.Candidate",
 *     description="參加者",
 *     @OA\Property(
 *         property = "staffCode",
 *         type = "string",
 *         description = "員編",
 *         example = "0001",
 *     ),
 *     @OA\Property(
 *         property = "staffName",
 *         type = "string",
 *         description = "員工姓名",
 *         example = "Obama",
 *     ),
 *     @OA\Property(
 *         property = "departmentCode",
 *         type = "string",
 *         description = "部門編號",
 *         example = "AAA1",
 *     ),
 *     @OA\Property(
 *         property = "department",
 *         type = "string",
 *         description = "部門名稱",
 *         example = "工程處 求才工程部",
 *     ),
 *     @OA\Property(
 *         property = "onBoardDate",
 *         type = "string",
 *         description = "到職日",
 *         example = "2000/01/01",
 *     ),
 * )
 */
class Candidate extends JsonResource
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
            'staffCode' => $this->staff_code,
            'staffName' => $this->staff_name,
            'departmentCode' => $this->department_code,
            'department' => $this->department,
            'onBoardDate' => $this->on_board_date,
        ];
    }
}
