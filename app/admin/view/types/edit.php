<form class="layui-form" action="" method="post">
    <div class="layui-form-item">
        <label class="layui-form-label">分类标题</label>
        <div class="layui-input-block">
            <input type="text" name="keyst_title" lay-verify="required" value="{$type.keyst_title}" lay-reqtext="分类标题必填" placeholder="分类标题" autocomplete="off" class="layui-input">
        </div>
    </div>

    <div class="layui-form-item layui-form-text">
        <label class="layui-form-label">必须关键词</label>
        <div class="layui-input-block">
            <textarea name="keyst_list_required" placeholder="请输入内容" value="{$type.keyst_list_required}" class="layui-textarea" style="height:300px;">{$type.keyst_list_required}</textarea>
        </div>
    </div>
    
    <div class="layui-form-item layui-form-text">
        <label class="layui-form-label">过滤关键词</label>
        <div class="layui-input-block">
            <textarea name="keyst_list_filter" placeholder="请输入内容" value="{$type.keyst_list_filter}" class="layui-textarea" style="height:300px;">{$type.keyst_list_filter}</textarea>
        </div>
    </div>

    <div class="layui-form-item">
          <label class="layui-form-label">分类开关</label>
          <div class="layui-input-inline">
            <input type="checkbox" name="keyst_status" lay-skin="switch" value="1" <?php echo $type['keyst_status'] ? 'checked' : '' ?> lay-text="开启|关闭">
          </div>
          <div class="layui-form-mid layui-word-aux">只能限制于此分类还是否采集，以前的文章还会正常存在</div>
        </div>

    <div class="layui-form-item">
        <div class="layui-input-block">
            <button type="submit" class="layui-btn" lay-submit="" lay-filter="demo1">立即提交</button>
        </div>
    </div>
</form>