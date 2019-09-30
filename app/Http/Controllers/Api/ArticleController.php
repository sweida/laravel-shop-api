<?php

namespace App\Http\Controllers\Api;

use App\Models\Article;
use App\Models\User;
use App\Models\ArticleLike;
use App\Models\Good;
use Illuminate\Http\Request;
use App\Http\Requests\ArticleRequest;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\DB;

class ArticleController extends Controller
{
    // 添加文章
    public function add(ArticleRequest $request){
        $article = Article::create($request->all());
        return $this->message('文章添加成功！');
    }

    // 获取文章所有分类
    public function classify(){
        $classifys = Article::groupBy('classify')->pluck('classify');
        return $this->success($classifys);
    }

    //返回文章列表 20篇为一页
    public function list(Request $request){
        $classifys = Article::groupBy('classify')->pluck('classify');
        
        // 需要显示的字段
        $data = ['id', 'title', 'img', 'classify', 'clicks', 'like', 'created_at', 'deleted_at'];

        // 获取所有，包括软删除
        if($request->all)
            $articles = Article::withTrashed()->orderBy('created_at', 'desc')->paginate(20, $data);
        else if ($request->classify)
            $articles = Article::whereClassify($request->classify)->orderBy('created_at', 'desc')->paginate(20, $data);
        else
            $articles = Article::orderBy('created_at', 'desc')->paginate(20, $data);

        // 拿回文章的标签和评论总数
        foreach($articles as $item){
            // $item->view_count = visits($item)->count();
            $item->like_count = ArticleLike::where('article_id', $item->id)->count();
        }

        return $this->success($articles);
    }

    //  查看文章详情
    public function detail(ArticleRequest $request){
        $id = $request->get('id');
        if ($request->get('all'))
            // 包括下架文章
            $article = Article::withTrashed()->find($id);
        else
            $article = Article::find($id);

        // 文章浏览量
        $article->clicks +=1;
        $article->save();

        // 商品详情
        $article->good = Good::find($article->good_id);

        $islike = ArticleLike::where(['article_id' => $id, 'user_id'=> $request->user_id])->first();
        $article['islike'] = $islike ? true : false; 

        // // 访问统计
        // visits($article)->increment();

        return $this->success($article);   
    }

    // 文章点赞列表
    public function likelist(Request $request) {
        $likes = ArticleLike::where('article_id', $request->article_id)->get();
        foreach($likes as $item) {
            $user = User::where('openid', $item->user_id)->first();
            $item['avatarUrl'] = $user->avatarUrl;
        }
        return $this->success($likes);   
    }

    // 修改文章
    public function edit(ArticleRequest $request){
        $article = Article::findOrFail($request->id);
        $article->update($request->all());

        return $this->message('文章修改成功！');
    }


    // 下架文章
    public function delete(ArticleRequest $request){
        Article::findOrFail($request->id)->delete();
        return $this->message('文章下架成功');
    }

    // 恢复下架文章
    public function restored(ArticleRequest $request){
        Article::withTrashed()->findOrFail($request->id)->restore();
        return $this->message('文章恢复成功');
    }

    // 真删除文章
    public function reallyDelete(ArticleRequest $request){
        Article::findOrFail($request->id)->forceDelete();
        return $this->success('文章删除成功');
    }



}

