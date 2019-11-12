# Phalcon Biz API Skeleton

基于 Phalcon、Biz Framework 的 API 项目脚手架。

## 特性

* 使用 Phalcon 作为接口接入层框架，Biz Framework 作为业务层框架；
* 通过 Composer 初始化项目；
* 通过注解的方式配置路由；
* 定义了标准的接口响应格式、通用错误码；
* 集成了接口鉴权机制；
* 提供了接口样例代码；
* 集成 Gitlab CI 配置；
* 集成了 Plumber2 消息队列消费者进程；
* 集成了 Console；
* 集成频率控制。[TODO]

## 安装

**从脚手架创建项目**
```
composer create-project codeages/phalcon-biz-api-skeleton api-example
```

## 开发

**修改系统环境配置：**

```
cp env.php.example env.php
```

修改`env.php`系统环境配置文件，数据库等相关配置。开发环境下请配置 `debug` 配置为 `true`，否则将无法显示程序错误信息。

**创建 var 目录：**

```
mkdir -p var/{cache,tmp,run,log}
chmod 777 var/{cache,tmp,run,log}
```

**创建数据库：**

```shell
CREATE DATABASE `api_example`;
```

**执行数据库变更脚本：**

```shell
bin/phpmig migrate
```

**配置 Nginx：**

```nginx
server {
    listen        80;
    server_name   api-example.local;
    root /var/www/api-example/public;
    
    location = /jsonrpc {
        try_files $uri /jsonrpc.php$is_args$args;
    }

    location / {
        try_files $uri /index.php?_url=$uri&$args;
    }

    location ~ ^/(index|jsonrpc)\.php(/|$) {
        fastcgi_pass  127.0.0.1:9000;
        # fastcgi_pass unix:/var/run/php/php7.2-fpm.sock;
        fastcgi_split_path_info       ^(.+\.php)(/.+)$;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        fastcgi_param DOCUMENT_ROOT $realpath_root;
        internal;
    }

    location ~ \.php$ {
        return 404;
    }

    access_log /var/log/nginx/api-example.access.log;
    error_log /var/log/nginx/api-example.error.log;
}
```

## 自动化测试

**创建测试环境配置**

```
cp env.php.example env.testing.php
```

修改`env.testing.php`系统环境配置文件，数据库等相关配置。

**运行测试：**

```
phpunit
```

## CHANGELOG

参见 [CHANGELOG.md](CHANGELOG.md)。
