<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\facade\Route;

Route::get('/','index/index');

Route::get('/types/:id','Types/index')->ext("html");

Route::get('/article/:id','Article/index')->ext("html");

Route::get('/tags/:name','Tags/index')->ext("html");

Route::get('/keys/:id','Keys/index')->ext("html");

Route::get('/test','Pages/index');

Route::get('/sitemap','Sitemapall/index')->ext("xml");

Route::miss("error/miss");