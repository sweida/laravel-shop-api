<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });


// 版本号
Route::get('/version', function() {
    return ["name" => "laravel-shop-api", "author" => "sweida", "version" => "v1"];
});
Route::namespace('Api')->prefix('v1')->group(function () {
    Route::post('/onlogin','UserController@onLogin')->name('users.onLogin');

    // 新增地址
    Route::post('/address/add','AddressController@add')->name('address.add');
    Route::post('/address/setactive','AddressController@setActive')->name('address.setActive');
    Route::post('/address/edit','AddressController@edit')->name('address.edit');
    Route::post('/address/delete','AddressController@delete')->name('address.delete');
    Route::post('/address/list','AddressController@list')->name('address.list');

    // 商品模块 
    Route::post('/goods/add','goodsController@addGood')->name('goods.add');
    Route::post('/goods/edit','goodsController@editGood')->name('goods.edit');
    Route::post('/goods/detail','goodsController@detail')->name('goods.detail');
    Route::post('/goods/list','goodsController@goodList')->name('goods.list');
    Route::get('/goods/classify','goodsController@classify')->name('goods.classify');
    Route::post('/goods/likeGood','collectionController@likeGood')->name('goods.likeGood');
    // Route::post('/good/unlike','collectionController@unlikeGood')->name('good.unlikeGood');
    // 收藏列表
    Route::post('/user/likesGoodList','collectionController@likesGoodList')->name('good.likesGoodList');
    // 收藏数量
    // Route::post('/user/likesGoodCount','collectionController@likesGoodCount')->name('good.likesGoodCount');

    Route::post('/goodbanner/add','goodbannerController@add')->name('good.add');
    Route::post('/goodbanner/edit','goodbannerController@edit')->name('good.edit');
    Route::post('/goodbanner/delete','goodbannerController@delete')->name('good.delete');
    Route::post('/goodbanner/list','goodbannerController@list')->name('good.list');


    // 文章
    Route::post('/article/add', 'ArticleController@add')->name('article.add');
    Route::post('/article/edit', 'ArticleController@edit')->name('article.edit');
    Route::post('/article/detail', 'ArticleController@detail')->name('article.detail');
    Route::post('/article/deleteOrRestored','ArticleController@deleteOrRestored')->name('article.deleteOrRestored');
    Route::post('/article/reallydelete','ArticleController@reallyDelete')->name('article.reallyDelete');
    Route::post('/article/like','ArticleLikeController@like')->name('article.like');
    Route::post('/article/likelist','ArticleController@likelist')->name('article.likelist');
    Route::get('/article/classifys','ArticleController@classifys')->name('article.classifys');



    Route::post('/good/buy','goodController@buy')->name('good.buy');
    Route::post('cart/checkStock','StockController@checkStock')->name('cart.checkStock');

    Route::post('order/create', 'orderController@createOrder')->name('order.create');
    Route::post('order/personalList', 'orderController@personalList')->name('order.personalList');
    Route::post('order/list', 'orderController@allList')->name('order.allList');
    Route::post('order/payOrder', 'orderController@payOrder')->name('order.payOrder');
    Route::post('order/DeliverGoods', 'orderController@DeliverGoods')->name('order.DeliverGoods');
    Route::post('order/submitOrder', 'orderController@submitOrder')->name('order.submitOrder');
    Route::post('order/cancel', 'orderController@cancelOrder')->name('order.cancel');


});



Route::namespace('Api')->prefix('v1')->group(function () {
    Route::post('/admin/signup','AdminController@signup')->name('admin.signup');
    Route::post('/admin/login', 'AdminController@login')->name('admin.login');
    Route::post('/admin/list', 'AdminController@list')->name('admin.list');
    Route::post('/admin/deleteOrRestored','AdminController@deleteOrRestored')->name('admin.deleteOrRestored');
    Route::post('/admin/reallyDelete','AdminController@reallyDelete')->name('admin.reallyDelete');
    Route::post('/admin/changePassword','AdminController@changePassword')->name('admin.changePassword');
    Route::post('/admin/resetPassword','AdminController@resetPassword')->name('admin.resetPassword');
    

    // 管理员登录
    Route::middleware('adminLogin')->group(function () {
    });
    //当前用户信息
    Route::middleware('api.refresh')->group(function () {
        Route::post('/logout', 'UserController@logout')->name('users.logout');
        Route::get('/user/info','UserController@info')->name('users.info');
        Route::post('/user','UserController@show')->name('users.show');
        Route::post('/user/resetpassword','UserController@resetpassword')->name('users.resetpassword');
    });
    Route::post('/user/send_email','CommonController@send_email')->name('users.send_email');
    Route::post('/user/check_captcha','CommonController@check_captcha')->name('users.check_captcha');
    
    Route::post('/user/list','UserController@list')->name('users.list');
    Route::middleware(['api.refresh', 'adminRole'])->group(function () {
    });

    // 图片上传又拍云
    Route::middleware(['api.refresh', 'adminRole'])->group(function () {
    });
    Route::post('/image/upload', 'ImageController@upload')->name('image.upload');
    Route::post('/image/delete', 'ImageController@delete')->name('image.delete');

    // 添加文章模块
    Route::post('/article/list', 'ArticleController@list')->name('article.list');
    Route::post('/tag/list', 'TagController@orderbytag')->name('tag.list');
    Route::get('/article/classify', 'ArticleController@classify')->name('article.classify');
    Route::post('/article','ArticleController@detail')->name('article.detail');


    // 评论模块
    Route::post('/comment/add', 'CommentController@add')->name('comment.add');
    Route::post('/comment/list', 'CommentController@list')->name('comment.list');
    Route::post('/comment/read','CommentController@read')->name('comment.read');
    Route::middleware('api.refresh')->group(function () {
        Route::post('/comment/edit', 'CommentController@edit')->name('comment.edit');
        Route::post('/comment/delete','CommentController@delete')->name('comment.delete');
        // 获取个人的所有评论
        Route::get('/comment/person','CommentController@person')->name('comment.person');
    });
    Route::middleware('adminLogin')->group(function () {
        Route::post('/comment/deletes','CommentController@deletes')->name('comment.deletes');
    });
    // 留言模块
    Route::post('/message/add', 'MessageController@add')->name('message.add');
    Route::post('/message/list', 'MessageController@list')->name('message.list');
    Route::middleware('api.refresh')->group(function () {
        Route::post('/message/edit', 'MessageController@edit')->name('message.edit');
        Route::post('/message/delete','MessageController@delete')->name('message.delete');
        // 个人留言
        Route::get('/message/person','messageController@person')->name('message.person');
    });
    Route::middleware('adminLogin')->group(function () {
        Route::post('/message/deletes','MessageController@deletes')->name('message.deletes');
    });

    // 友情链接模块
    Route::post('/link/list', 'LinkController@list')->name('link.list');
    Route::middleware(['api.refresh', 'adminRole'])->group(function () {
        Route::post('/link/add', 'LinkController@add')->name('link.add');
        Route::post('/link/edit', 'LinkController@edit')->name('link.edit');
        Route::post('/link/delete','LinkController@delete')->name('link.delete');
    });

    // 图片广告模块
    Route::post('/ad/list', 'AdController@list')->name('ad.list');
    Route::post('/ad', 'AdController@show')->name('ad.show');
    Route::post('/ad/add', 'AdController@add')->name('ad.add');
    Route::get('/ad/classifys', 'AdController@classifys')->name('ad.classifys');
    Route::post('/ad/edit', 'AdController@edit')->name('ad.edit');
    Route::post('/ad/delete','AdController@delete')->name('ad.delete');
    Route::post('/webinfo/set', 'WebinfoController@set')->name('webinfo.set');
    Route::middleware(['api.refresh', 'adminRole'])->group(function () {
    });

    // 网站信息模块
    Route::get('/webinfo/read', 'WebinfoController@read')->name('webinfo.read');

});

