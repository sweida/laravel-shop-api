<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class SwaggerController extends Controller
{
    /**
     * @OA\Get(
     *     path="/doc",
     *     tags={"第一个文档"},
     *     summary="swagger版本号",
     *     description="获取时间接口",
     *     operationId="TimeShow",
     *     deprecated=false,
     *     @OA\Parameter(
     *         name="access_token",
     *         in="query",
     *         description="版本号",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="操作成功返回"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="发生错误"
     *     )
     * )
     */
    public function doc()
    {
        return response()->json(['version' => 'swagger 3.0']);
    }

    /**
     * @OA\Get(
     *     path="/hello",
     *     tags={"第二个文档"},
     *     summary="说你好接口",
     *     description="说你好接口",
     *     operationId="SayHello",
     *     deprecated=false,
     *     @OA\Parameter(
     *         name="access_token",
     *         in="query",
     *         description="用户授权",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="操作成功返回"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="发生错误"
     *     )
     * )
     */
    public function hello()
    {
        echo "hello";
    }




}