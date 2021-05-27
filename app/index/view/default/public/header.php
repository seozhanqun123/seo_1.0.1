<div id="header">
	<div class="box" id="header_box">
		<a href="{$GLOBALS['host_url']}/" id="logo">{$GLOBALS['site']['site_name']}</a>
		<ul>
			{volist name="$GLOBALS['header_link']" id="vo"}<a href="{$GLOBALS['host_url']}/types/{$vo.keyst_id}.html" target="_blank">{$vo.keyst_title}</a>{/volist}
		</ul>
	</div>
</div>