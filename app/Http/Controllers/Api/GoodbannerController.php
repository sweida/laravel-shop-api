<?php

namespace App\Http\Controllers\Api;

use App\Models\Goodbanner;
use Illuminate\Http\Request;
use App\Http\Requests\GoodbannerRequest;


class GoodbannerController extends Controller
{
    // 添加banner
    public function addBanner(GoodbannerRequest $request) {
        $banner = Goodbanner::create($request->all());

        return $this->success($banner);
    }
}
