<?php

namespace App\Actions;

use App\Actions\Traits\DrawingLog;
use App\Exceptions\Model\ResourceErrorException;
use App\Model\Award;
use Lorisleiva\Actions\Action;

/**
 * 新增加碼獎項
 *
 * @OA\Post(
 *     path = "/api/award/create",
 *     summary = "新增加碼獎項（今年暫不使用，route 關閉中）",
 *     description = "新增加碼獎項",
 *     tags = {"獎品"},
 *     @OA\RequestBody(
 *         description = "API傳入的內容",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property = "name",
 *                 type = "string",
 *                 description = "獎項名稱",
 *                 example = "一獎"
 *             ),
 *             @OA\Property(
 *                 property = "money",
 *                 type = "integer",
 *                 description = "金額",
 *                 example = 3000
 *             ),
 *             @OA\Property(
 *                 property = "limit",
 *                 type = "integer",
 *                 description = "獎項數量，非必填（default 10 個）",
 *                 default = 10,
 *                 example = 5
 *             ),
 *         ),
 *     ),
 *     @OA\Response(
 *         response = "200",
 *         description = "正常回傳",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property = "data",
 *                 type = "boolean",
 *                 description = "新增後結果",
 *                 example = true
 *             ),
 *         ),
 *     ),
 *     @OA\Response(
 *          response = "400",
 *          description = "獎項已存在",
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
 *                  example = "該獎項已存在！",
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
class CreateAward extends Action
{
    use DrawingLog;

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
            'name' => 'required',
            'money' => 'required|gte:1000',
            'limit' => 'nullable|integer',
        ];
    }

    /**
     * Execute the action and return a result.
     * @param string $name
     * @param int $money
     * @param int|null $limit
     *
     * @return void
     */
    public function handle(string $name, int $money, $limit = null)
    {
        $limit = $limit ?? 10;
        $oldAward = Award::where('name', $name)->first();
        if (!is_null($oldAward)) {
            throw new ResourceErrorException('該獎項已存在！');
        }

        Award::insert([
            'name' => $name,
            'money' => $money,
            'number' => $limit,
        ]);
        $this->logAwardCreating($name, $money, $limit);
    }

    /**
     * response 控制
     *
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function response()
    {
        return response(['data' => true]);
    }
}
