<!DOCTYPE html>
<html lang="zh-cn" xml:lang="zh-cn">
<head>
<title><?php echo trim($article['article_title']); ?>-{$article['keys_name']}-{$GLOBALS['site']['site_title']}</title>
<meta name="keywords" content="{$article['keys_name']},{$article['article_tags']}" />
<meta name="description" content="<?php echo htmlspecialchars_decode($article['article_des']); ?>" />
<meta name="viewport" content="width=device-width,initial-scale=1, maximum-scale=1, user-scalable=no">
<meta name="apple-mobile-web-app-capable" content="yes">
<link rel="stylesheet" type="text/css" href="/skin/{$GLOBALS['temp']}/style.css">
</head>
<body>

{include file="default/public/header" /}

<div class="box">
	
	<div id="article" class="box_left">
		<h1><?php echo $article['article_title']; ?></h1>
		<div id="content"><?php echo htmlspecialchars_decode($article['article_body']); ?><p>本文最终地址：{$GLOBALS['host_url']}/article/{$article['article_id']}.html</p><p>本文标题：{$article['article_title']}</p><p>相关推荐：{volist name="keys_rand_list" id="vo"}<a href="{$GLOBALS['host_url']}/keys/{$vo.keys_id}.html" target="_blank">{$vo.keys_name}</a>　{/volist}</p></div>
	</div>
	
	
	<?php 
	
	
	/*
	<p>本文标签：<a href="{$GLOBALS['host_url']}/keys_list/{$article['keys_id']}.html" target="_blank">{$article['keys_name']}</a>  {volist name="$article['article_tags_array']" id="vo"}<a href="{$GLOBALS['host_url']}/tags/{$vo}.html" target="_blank">{$vo}</a>  {/volist}</p>
	*/
	
	?>
	
	<div class="box_right">
		<div class="box_title">
			<h3>推荐文章</h3>
			<ul>
				{volist name="article_rand_1" id="vo"}<a href="{$GLOBALS['host_url']}/article/{$vo.article_id}.html" target="_blank"><?php echo htmlspecialchars_decode($vo['article_title']); ?></a>{/volist}
			</ul>
		</div>
		<div class="box_title">
			<h3>热门文章</h3>
			<ul>
			    
				{volist name="article_rand_2" id="vo"}<a href="{$GLOBALS['host_url']}/article/{$vo.article_id}.html" target="_blank"><?php echo htmlspecialchars_decode($vo['article_title']); ?></a>{/volist}
			</ul>
		</div>
		<div class="box_title">
			<h3>随机文章</h3>
			<ul>
				{volist name="article_rand_3" id="vo"}<a href="{$GLOBALS['host_url']}/article/{$vo.article_id}.html" target="_blank"><?php echo htmlspecialchars_decode($vo['article_title']); ?></a>{/volist}
			</ul>
		</div>
	</div>
</div>

{include file="default/public/footer" /}

</body>
</html>