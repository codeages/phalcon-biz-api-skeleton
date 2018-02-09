# CHANGELOG

## [0.5.0] - 2018-02-09

* src 目录的命名空间调整为 `App\\`，tests 目录的命名空间调整为 `App\\Tests`，向 Symfony4 看齐。

## [0.4.2] - 2018-01-24

* 支持 jsonp。
* 修正错误码。

## [0.4.1] - 2018-01-17

* `config/web.php` 增加 `route_discovery` 配置。从之前版本升级需添加： (thanks to @IlhamTahir)
  ```php
    'route_discovery' => [
        'Controller' => dirname(__DIR__).'/src/Controller'
    ]
  ```

## [0.4.0] - 2018-01-16

* 增加了路由表的 Cache。(thanks to @tangyue)
  * 当环境变量`'DEBUG'=> false` 时，会在 `var/cache` 下生产路由表缓存文件，一旦生成，路由就只会读取缓存文件。所以线上每次代码发布后，需执行清除缓存的操作： `rm -rf var/cache/*`。
  * 当环境变量`'DEBUG'=> true` 时，不会生成缓存文件，适用于本地开发。
* Exception 记录日志。
* 接收 `Content-Type: application/json` 数据时，自动为`$_POST`赋值。
* PhalconBiz 剥离到 https://github.com/codeages/phalcon-biz-library ，已有项目需要做以下修改：
  * 修改你的项目的 `composer.json`，`require`部分， 增加 `"codeages/phalcon-biz-library": "^0.1.0"` ，干掉已经存在于`codeages/phalcon-biz-library` 项目 `composer.json` 中的部分。
  * 删除 `src/PhalconBiz`, `tests/UnitTest/PhalconBiz` 目录。
  * 执行 `composer update`。
