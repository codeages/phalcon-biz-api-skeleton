# Phalcon Biz API Skeleton

基于 Phalcon、Biz Framework 的 API 项目脚手架。

## 特性

* 使用 Phalcon 作为接口接入层框架，Biz Framework 作为业务层框架。
* 通过 Composer 初始化项目。
* 通过注解的方式配置路由。
* 定义了标准的接口响应格式、通用错误码。
* 集成了接口鉴权机制。
* 集成频率控制。[TODO]
* 提供了接口样例代码。
* 集成 Gitlab CI。
* 集成 API Blueprint 标准的API文档工具。[TODO]

## 安装

**从脚手架创建项目**
```
composer create-project codeages/phalcon-biz-api-skeleton my_api_project
```

## 开发

**修改系统环境配置：**

```
cp env.php.example env.php
```

修改`env.php`系统环境配置文件，数据库等相关配置。

**创建 var 目录：**

```
mkdir -p var/{cache,tmp,run,log}; chmod 777 var/{cache,tmp,run,log}
```

**创建数据库：**

```shell
CREATE DATABASE `my_api_db`;
```

**执行数据库变更脚本：**

```shell
bin/phpmig migrate
```

**启动接口服务：**
```shell
php -S localhost:8000 -t public .htrouter.php
```

也可配置 Nginx 参见文档的部署部分。

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

## 部署

```
server {
    listen        80;
    server_name   phalcon-biz-api-skeleton.local.cg-dev.cn;

    root /var/www/phalcon-biz-api-skeleton/public;
    index index.php index.html index.htm;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?_url=$uri&$args;
    }

    location ~ \.php$ {
        try_files $uri =404;
        fastcgi_pass  127.0.0.1:9000;
        fastcgi_index /index.php;
        include fastcgi_params;
        fastcgi_split_path_info       ^(.+\.php)(/.+)$;
        fastcgi_param PATH_INFO       $fastcgi_path_info;
        fastcgi_param PATH_TRANSLATED $document_root$fastcgi_path_info;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }

    location ~ /\.ht {
        deny all;
    }

    location ~* \.(js|css|png|jpg|jpeg|gif|ico)$ {
        expires       max;
        log_not_found off;
        access_log    off;
    }

    access_log /var/log/nginx/phalcon-biz-api-skeleton.access.log;
    error_log /var/log/nginx/phalcon-biz-api-skeleton.error.log;
}
```

## CHANGELOG

参见 [CHANGELOG.md](CHANGELOG.md)。
