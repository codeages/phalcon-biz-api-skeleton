# Phalcon Biz API Skeleton

基于 Phalcon、Biz Framework 的 API 项目脚手架。

## 特性

* 使用Phalcon作为接口接入层框架，BizFramework作为业务层框架。
* 通过 Composer 初始化项目。
* 通过注解的方式配置路由。
* 定义了标准的接口响应格式、通用错误码。
* 集成了接口鉴权机制。
* 集成频率控制。[TODO]
* 提供了接口样例代码。
* 集成[Codeception](http://codeception.com/)的单元测试、接口测试。
* 集成 Gitlab CI。
* 集成 APIBlueprint 标准的API文档工具。[TODO]

## 安装

```
composer create-project codeages/phalcon-biz-api-skeleton my_api_project
```

## 开发

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

也可配置Nginx参见文档的部署部分。

## 自动化测试

**启动接口服务：**
```bash
IN_TESTING=true php -S localhost:8000 -t public .htrouter.php
```

**运行测试：**
```
bin/codecept run # 运行所有测试
bin/codecept run unit # 只运行单元测试
bin/codecept run api # 只运行接口测试
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