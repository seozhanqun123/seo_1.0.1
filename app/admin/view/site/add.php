<form class="layui-form" action="" method="post">
    <div class="layui-form-item">
        <label class="layui-form-label">站点域名</label>
        <div class="layui-input-block">
            <input type="text" name="site_domain" lay-verify="required" lay-reqtext="站点域名必填" placeholder="站点域名" autocomplete="off" class="layui-input">
        </div>
    </div>
    
    <div class="layui-form-item">
        <label class="layui-form-label">站点名称</label>
        <div class="layui-input-block">
            <input type="text" name="site_name" lay-verify="required" autocomplete="off" lay-reqtext="站点名称必填" placeholder="站点名称" class="layui-input">
        </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">SEO标题</label>
        <div class="layui-input-block">
            <input type="text" name="site_title" lay-verify="required" lay-reqtext="SEO标题必填" placeholder="SEO标题" autocomplete="off" class="layui-input">
        </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">网站描述</label>
        <div class="layui-input-block">
            <input type="text" name="site_des" lay-verify="required" lay-reqtext="网站描述必填" placeholder="网站描述" autocomplete="off" class="layui-input">
        </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">网站关键词</label>
        <div class="layui-input-block">
            <input type="text" name="site_keys" lay-verify="required" lay-reqtext="网站关键词必填" placeholder="网站关键词" autocomplete="off" class="layui-input">
        </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">站点模板</label>
        <div class="layui-input-block">
            <select name="site_template" lay-filter="aihao">
                <option value="1">默认</option>
            </select>
        </div>
    </div>

    <div class="layui-form-item">
        <div class="layui-input-block">
            <button type="submit" class="layui-btn" lay-submit="" lay-filter="demo1">立即提交</button>
        </div>
    </div>
</form>