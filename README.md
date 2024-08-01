
# QHPHP 青航框架 打造轻量型程序

![GitHub Downloads (all assets, all releases)](https://img.shields.io/github/downloads/qhthem/qhphp/total)
![AUR Version](https://img.shields.io/aur/version/qhthem)
[![License](https://poser.pugx.org/topthink/framework/license)]
[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.0-8892BF.svg)](http://www.php.net/)


## 主要特性

- 基于 PHP`8.0+`重构
- 使用灵活 不限制开发

> qhphp框架的运行环境要求 PHP8.0+

## 官网

[Astro主题](https://www.astrocms.cn/)


## 快捷安装

```
composer create-project qhthem/framework
```
## 宝塔安装

1.新建一个网站，在目录下新建一个composer.json文件，然后在文件里面配置如下：

```
{
    "require": {
        "qhthem/qhphp": "dev-main"
    },
    "autoload": {
        "psr-4": {
            "app\\": "app",
            "extend\\": "extend"
        }
    }
    
}
```
2.然后回到网站列表，点击设置->其他设置->Composer->点击执行

3.回到网站目录下已经安装完成了


## 参与开发

直接提交 PR 或者 Issue 即可

## 版权信息

QHPHP 遵循 Apache2 开源协议发布，并提供免费使用。

本项目包含的第三方源码和二进制文件之版权信息另行标注。

版权所有 Copyright © 2024-2035 by 	QHPHP (https://www.astrocms.cn/) All rights reserved。

