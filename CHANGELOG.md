# CHANGELOG

## [0.4.0] - 2018-01-16

* 增加了路由表的 Cache。
* Exception 记录日志
* 接收 `Content-Type: application/json` 数据时，自动为`$_POST`赋值。
* PhalconBiz 剥离到 https://github.com/codeages/phalcon-biz-library ，已有项目需要做以下修改：
  * 修改你的项目的 `composer.json`，`require`部分， 增加 `"codeages/phalcon-biz-library": "^0.1.0"` ，干掉已经存在于`codeages/phalcon-biz-library` 项目 `composer.json` 中的部分。
  * 删除 `src/PhalconBiz`, `tests/UnitTest/PhalconBiz` 目录。
  * 执行 `composer update`。

