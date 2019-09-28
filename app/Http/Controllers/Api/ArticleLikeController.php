<?php

namespace App\Http\Controllers\Api;


use App\Models\ArticleLike;
use Illuminate\Http\Request;


class ArticleLikeController extends Controller
{
    // 点赞文章
    public function like(Request $request) {
        if ($request->get('active')==true) {
            $collection = ArticleLike::where(
                    ['user_id' => $request->user_id, 'article_id' => $request->article_id]
                )->first();
            if ($collection) {
                $collection->delete();
            }
            return $this->message('已取消点赞');
        } else {
            ArticleLike::updateOrCreate(
                ['user_id' => $request->user_id, 'article_id' => $request->article_id], 
                $request->all()
            );
            return $this->message('点赞成功');
        }
    }
}
