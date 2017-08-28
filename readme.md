## 为微型社区 LaraVue 提供后端 API 接口，前端项目请前往 https://github.com/zmecust/laravue-frontend

## 运行环境

- Nginx 1.8+
- PHP 7.0+
- Mysql 5.6+
- Redis 3.0+

## 安装

- git clone https://github.com/zmecust/laravue-backend.git
- composer install
- cp .env.example .env
- 配置 sendloud 邮件服务
- php artisan migrate

## 扩展包描述

| 扩展包 | 一句话描述 | 在本项目中的使用案例 |  
| --- | --- | --- |   
| [barryvdh/laravel-cors](https://packagist.org/packages/barryvdh/laravel-cors) | Laravel 解决跨域问题 | 前后端分离需要使用此扩展包。 |  
| [naux/sendcloud](https://github.com/naux/sendcloud) | 好用的 sendloud 邮件服务插件 | 用户注册时，验证邮箱使用此扩展包。 |
| [tymon/jwt-auth](https://github.com/tymon/jwt-auth) | 轻量级的授权和身份认证 | 前后端分离验证用户信息需要用到此扩展包。 |
| [zircote/swagger-php](https://github.com/zircote/swagger-php) | API 文档生成器 | API 文档生成需要用到此扩展包。 |
| [overtrue/socialite](https://github.com/overtrue/socialite) | 社会化登录组件 | GitHub 登录逻辑使用了此扩展包。 |
| [zizaco/entrust](https://github.com/Zizaco/entrust.git) | 用户组权限系统 | 整站的权限系统基于此扩展包开发。 |

## License

The open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).
