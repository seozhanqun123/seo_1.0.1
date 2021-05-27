<a class="layui-btn layui-btn-xs" href="/types/add">添加分类</a>


<table class="layui-hide" id="test" lay-filter="test"></table>
 
<script type="text/html" id="toolbarDemo">
  <div class="layui-btn-container">
    <button class="layui-btn layui-btn-sm" lay-event="getCheckData">获取选中行数据</button>
    <button class="layui-btn layui-btn-sm" lay-event="getCheckLength">获取选中数目</button>
    <button class="layui-btn layui-btn-sm" lay-event="isAll">验证是否全选</button>
  </div>
</script>
 
<script type="text/html" id="barDemo">
  <a class="layui-btn layui-btn-xs" lay-event="edit">编辑</a>
  <a class="layui-btn layui-btn-xs" lay-event="add_keys">添加关键词</a>
</script>
 
<script>
layui.use('table', function(){
  var table = layui.table;
  
  table.render({
    elem: '#test'
    ,url:'#'
    ,method:"post"
    //,toolbar: '#toolbarDemo' //开启头部工具栏，并为其绑定左侧模板
    ,defaultToolbar: ['filter', 'exports', 'print', { //自定义头部工具栏右侧图标。如无需自定义，去除该参数即可
      title: '提示'
      ,layEvent: 'LAYTABLE_TIPS'
      ,icon: 'layui-icon-tips'
    }]
    ,title: '用户数据表'
    ,cols: [[
      {field:'keyst_id', title:'ID', width:80}
      ,{field:'keyst_title', title:'分类标题'}
      ,{field:'keyst_status', title:'状态',templet: function(d){
        return d.keyst_status ? '正常':'关闭'
      }, width:80}
      ,{fixed: 'right', title:'操作', toolbar: '#barDemo', width:200}
    ]]
    ,page: true
  });

  //监听行工具事件
  table.on('tool(test)', function(obj){
    var data = obj.data;
    //console.log(obj)
    if(obj.event === 'del'){
      layer.confirm('真的删除行么', function(index){
          $.post("/site/deletes/",{
              site_id:data.site_id
          },function(res){
              console.log(res);
          });
        layer.close(index);
      });
    } else if(obj.event === 'edit'){
      location.href="/types/edit/?id="+data.keyst_id
    }else if(obj.event === 'add_keys'){
      location.href="/keys/add/?type_id="+data.keyst_id
    }
    
  });
});
</script>