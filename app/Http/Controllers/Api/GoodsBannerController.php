<?php

namespace App\Http\Controllers\Api;

use App\Models\GoodsBanner;
use Illuminate\Http\Request;
use App\Http\Requests\GoodsBannerRequest;

class GoodsBannerController extends Controller
{
    // 添加banner
    public function addBanner(GoodsBannerRequest $request) {
        $banner = GoodsBanner::create($request->all());

        return $this->success($banner);
    }
}
