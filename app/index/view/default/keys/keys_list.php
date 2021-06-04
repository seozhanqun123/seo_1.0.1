<!DOCTYPE html>
<html lang="zh-cn" xml:lang="zh-cn">
<head>
<title>{$keys['keys_name']}-{$GLOBALS['site']['site_title']}</title>
<meta name="keywords" content="{$keys['keys_name']}" />
<meta name="viewport" content="width=device-width,initial-scale=1, maximum-scale=1, user-scalable=no">
<meta name="apple-mobile-web-app-capable" content="yes">
<link rel="stylesheet" type="text/css" href="/skin/{$GLOBALS['temp']}/style.css">
</head>
<body>

{include file="default/public/header" /}

<div class="box">
	<div class="box_left" style="background:#fff;">
		<div class="list">
		    {volist name="keys_list" id="vo"}<a href="{$GLOBALS['host_url']}/keys/{$vo.keys_id}.html" target="_blank">{$vo.keys_name}</a>{/volist}
		    {volist name="keys_rand_list" id="vo"}<a href="{$GLOBALS['host_url']}/keys/{$vo.keys_id}.html" target="_blank">{$vo.keys_name}</a>{/volist}
		    {volist name="article_list" id="vo"}
    		<li>
    			<h3><a href="{$GLOBALS['host_url']}/article/{$vo.article_id}.html" target="_blank">{$vo.article_title}</a></h3>
    			<p><?php echo htmlspecialchars_decode($vo['article_des']); ?></p>
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

<style>
.list a{
    margin: 8px;
    display: inline-block;
}
</style>

{include file="default/public/footer" /}

</body>
</html>