<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>SEO站群管理系统</title>
    <link rel="stylesheet" href="/layui/css/layui.css">
    <script src="/layui/layui.js"></script>
</head>

<body>
    <div class="layui-layout layui-layout-admin">
        <div class="layui-header">
            <div class="layui-logo layui-hide-xs layui-bg-black">layout demo</div>
            <!-- 头部区域（可配合layui 已有的水平导航） -->
            <ul class="layui-nav layui-layout-left">
                <!-- 移动端显示 -->
                <li class="layui-nav-item layui-show-xs-inline-block layui-hide-sm" lay-header-event="menuLeft"> <i class="layui-icon layui-icon-spread-left"></i>
                </li>
                <li class="layui-nav-item layui-hide-xs"><a href="">nav 1</a>
                </li>
                <li class="layui-nav-item layui-hide-xs"><a href="">nav 2</a>
                </li>
                <li class="layui-nav-item layui-hide-xs"><a href="">nav 3</a>
                </li>
                <li class="layui-nav-item"> <a href="javascript:;">nav groups</a>
                    <dl class="layui-nav-child">
                        <dd><a href="">menu 11</a>
                        </dd>
                        <dd><a href="">menu 22</a>
                        </dd>
                        <dd><a href="">menu 33</a>
                        </dd>
                    </dl>
                </li>
            </ul>
            <!-- <ul class="layui-nav layui-layout-right">
                <li class="layui-nav-item layui-hide layui-show-md-inline-block">
                    <a href="javascript:;">
                        <img src="//tva1.sinaimg.cn/crop.0.0.118.118.180/5db11ff4gw1e77d3nqrv8j203b03cweg.jpg" class="layui-nav-img">tester</a>
                    <dl class="layui-nav-child">
                        <dd><a href="">Your Profile</a>
                        </dd>
                        <dd><a href="">Settings</a>
                        </dd>
                        <dd><a href="">Sign out</a>
                        </dd>
                    </dl>
                </li>
            </ul> -->
        </div>
        <div class="layui-side layui-bg-black">
            <div class="layui-side-scroll">
                <!-- 左侧导航区域（可配合layui已有的垂直导航） -->
                <ul class="layui-nav layui-nav-tree" lay-filter="test">
                    <li class="layui-nav-item layui-nav-itemed"> <a class="" href="javascript:;">内容管理</a>
                        <dl class="layui-nav-child">
                            <dd><a href="/types/">分类</a></dd>
                            <dd><a href="/keys/">关键词</a></dd>
                            <dd><a href="/article/">文章</a></dd>
                            <dd><a href="">the links</a></dd>
                        </dl>
                    </li>
                    <li class="layui-nav-item"> <a href="javascript:;">menu group 2</a>
                        <dl class="layui-nav-child">
                            <dd><a href="javascript:;">list 1</a></dd>
                            <dd><a href="javascript:;">list 2</a></dd>
                            <dd><a href="">超链接</a></dd>
                        </dl>
                    </li>
                    <li class="layui-nav-item"> <a href="javascript:;">站点管理</a>
                        <dl class="layui-nav-child">
                            <dd><a href="/site/">所有站点</a></dd>
                            <dd><a href="/site/add">添加站点</a></dd>
                        </dl>
                    </li>
                </ul>
            </div>
        </div>
        <div class="layui-body">
            <!-- 内容主体区域 -->
            <div style="padding: 15px;">