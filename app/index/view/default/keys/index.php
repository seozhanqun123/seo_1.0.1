<!DOCTYPE html>
<html>
<head>
<title>{$keys['keys_name']}所有的文章 - {$GLOBALS['site']['site_title']}</title>
<meta name="keywords" content="{$GLOBALS['site']['site_keys']}" />
<meta name="viewport" content="width=device-width,initial-scale=1, maximum-scale=1, user-scalable=no">
<meta name="apple-mobile-web-app-capable" content="yes">
<link rel="stylesheet" type="text/css" href="/skin/{$GLOBALS['temp']}/style.css">
</head>
<body>

{include file="default/public/header" /}

<div class="box">
	<div class="box_left">
		<div class="list">
		    {volist name="article_list" id="vo"}
    		<li>
    			<h3><a href="{$GLOBALS['host_url']}/article/{$vo.article_id}.html" target="_blank">{$vo.article_title}</a></h3>
    			<p>{$vo.article_des}</p>
    		</li>
    		{/volist}
		</div>
	</div>
	<div class="box_right">
		<div class="box_title">
			<h3>推荐文章</h3>
			<ul>
				{volist name="article_rand_1" id="vo"}<a href="{$GLOBALS['host_url']}/article/{$vo.article_id}.html" target="_blank">{$vo.article_title}</a>{/volist}
			</ul>
		</div>
		<div class="box_title">
			<h3>热门文章</h3>
			<ul>
				{volist name="article_rand_2" id="vo"}<a href="{$GLOBALS['host_url']}/article/{$vo.article_id}.html" target="_blank">{$vo.article_title}</a>{/volist}
			</ul>
		</div>
		<div class="box_title">
			<h3>随机文章</h3>
			<ul>
				{volist name="article_rand_3" id="vo"}<a href="{$GLOBALS['host_url']}/article/{$vo.article_id}.html" target="_blank">{$vo.article_title}</a>{/volist}
			</ul>
		</div>
	</div>
</div>

{include file="default/public/footer" /}

</body>
</html>