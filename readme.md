
### 开发前工作

```
# 复制配置文件
cp .env.example .env

# 修改时区 config > app.php文件
'timezone' => 'PRC'

# 生成key
php artisan key:generate

# 生成数据库表
php artisan migrate

# 填充数据
php artisan db:seed

# 清空数据库重新生成表
php artisan migrate:refresh

# 清空数据库重新生成表并生成数据
php artisan migrate:refresh --seed

# postman请求头设置herders (错误时才会返回json格式)
X-Requested-With => XMLHttpRequest
```

### 安装jwt-auth
```
composer require tymon/jwt-auth

# 修改 config/app.php
'providers' => [
    ...
    Tymon\JWTAuth\Providers\LaravelServiceProvider::class,
]

# 发布配置文件
php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"

# 生成key
php artisan jwt:secret
```

### 安装 swagger
```
# 安装
composer require "darkaonline/l5-swagger:5.8.*"

# 发布配置文件
php artisan vendor:publish --provider "L5Swagger\L5SwaggerServiceProvider"

# 如果需要支持 @SWG (SWAGGER annotations) ，需要安装这个
composer require 'zircote/swagger-php:2.*'

# .env文件 ，自动更新文档
SWAGGER_VERSION=3.0
L5_SWAGGER_GENERATE_ALWAYS=true
L5_SWAGGER_BASE_PATH="http://localhost:8080"
```

### 安装 telescope
```
composer require laravel/telescope
php artisan telescope:install
php artisan migrate

# 访问地址
http://localhost:8080/telescope
```

### 安装Laravel Horizon
```
composer require laravel/horizon

修改.env
QUEUE_CONNECTION=redis

# 发布配置文件
php artisan vendor:publish --provider="Laravel\Horizon\HorizonServiceProvider"

# 使用redis队列 需要引入 predis/predis 包

composer require predis/predis

php artisan horizon 即可启动所有的队列

### 注意：每次修改job代码都需要重启horizon

# 访问地址
http://localhost:8080/horizon/dashboard
```

### 安装邮件模版
```
composer require qoraiche/laravel-mail-editor

# 发布配置文件
php artisan vendor:publish --provider="qoraiche\mailEclipse\mailEclipseServiceProvider"

php artisan migrate

# 访问地址
http://localhost:8080/maileclipse
```

# 手动生成markdown邮件
```
php artisan make:mail NweUser --markdown=mails.newsuer

# router 文件
new App\Mails\NewUser()
```


### 图片上传又拍云
```
composer require "jellybool/flysystem-upyun"

# config/app.php 添加
'providers' => [
    // Other service providers...
    JellyBool\Flysystem\Upyun\UpyunServiceProvider::class,
],

# config/filesystems.php 的 disks 中添加下面的配置：
return [
    //...
      'upyun' => [
            'driver'        => 'upyun', 
            'bucket'        => 'your-bucket-name',// 服务名字
            'operator'      => 'oparator-name', // 操作员的名字
            'password'      => 'operator-password', // 操作员的密码
            'domain'        => 'xxxxx.b0.upaiyun.com', // 服务分配的域名
            'protocol'     => 'https', // 服务使用的协议，如需使用 http，在此配置 http
        ],
    //...
];
```


```
# 添加model
php artisan make:model Models/Article -m

# 添加控制器
php artisan make:controller Api/ArticleController

# 添加request
php artisan make:request ArticleRequest
```

### 安装浏览统计插件
```
composer require awssat/laravel-visits

# 添加配置文件
php artisan vendor:publish --provider="awssat\Visits\VisitsServiceProvider"

# 修改.env文件
CACHE_DRIVER=file 改成 CACHE_DRIVER=array

# 在Postman里要设置 headers->User-Agent
```

#### 关联模型要写在model里，不能写在controller里
```
public function user() {
    return $this->belongsTo('App\Models\User');
}
```

#### 数据填充
```
# 生成User模型的工厂
php artisan make:factory UserFactory --model=Models/User

# 生成User的数据填充
php artisan make:seeder UsersTableSeeder

# 数据填充
php artisan db:seed

# 填充指定模型
php artisan db:seed --class=UsersTableSeeder

# 重新生成数据库表并填充数据
php artisan migrate:refresh --seed

# 进入数据填充测试
php artisan tinker

# 生成20个用户模型
namespace App\Models;
factory(User::class, 20)->create();
```


### redis启动
```
cd /usr/local/etc
redis-server & ./redis.conf

redis-cli
```

跨域medz/cors
pdf功能
支付功能


### 生成随机头像
https://avatars.dicebear.com/v1/identicon/:seed.svg
![https://avatars.dicebear.com/v1/identicon/:seed.svg](https://avatars.dicebear.com/v1/identicon/1.svg)
