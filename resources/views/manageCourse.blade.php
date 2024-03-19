<?php
$connect = mysqli_connect("localhost", "root", "1773@qwer", "pkuq");
$sql = "SELECT * FROM r_courses";
$result = mysqli_query($connect, $sql);
$activitys = [];
while ($row = mysqli_fetch_assoc($result)) {
  $activitys[] = $row;
}
// print_r($activitys);
?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <title>
    PkRq-管理展示
  </title>
  <meta name="renderer" content="webkit">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <meta name="apple-mobile-web-app-status-bar-style" content="black">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="format-detection" content="telephone=no">
  <link rel="stylesheet" href="../../layui/css/layui.css" media="all">
  <style>
    .layui-input input{
      width: 100%;
    }
    .layui-upload-img {
      width: 320px;
    }
    .layui-upload-img1 {
      width: 100px;
    }
  </style>
  <script>
    var password = localStorage.getItem('admin_password') || prompt('请输入管理密码：');
    if(password != '123123'){
      history.back();
    }else{
      localStorage.setItem('admin_password', '123123');
    }
  </script>
</head>

<body>
  <header class="layui-bg-cyan">
    <h1>管理展示</h1>
  </header>
  <table>
    <caption>活动展示</caption>
    <tr><th>标题</th><th>内容</th><th>操作</th></tr>
    <?php foreach ($activitys as $activity){?>
    <tr>
      <td><?php echo $activity['title']?></td>
      <td><?php echo $activity['text']?></td>
      <td>
        <button id="delbutton" onclick="actDel(<?php echo $activity['rcid']?>)"  class="layui-btn layui-btn-danger layui-btn-xs">删除</a>
      </td>
    </tr>
    <?php }?>
  </table>
  <script src="https://cdn.bootcdn.net/ajax/libs/jquery/3.7.1/jquery.js"></script>
  <script>
    function actDel(id) {
      $.ajax({
        url: "http://127.0.0.1:8000/" + 'api/pub/delCourse',
        method: "post",
        data: { "rid": id},
        dataType: 'JSON',
        success: function(res) {
            alert(res.msg);
            window.location.reload();
        },
        error: function(jqXHR, textStatus, errorThrown) {
            /*弹出jqXHR对象的信息*/
            alert(jqXHR.responseText);
            alert(jqXHR.status);
            alert(jqXHR.readyState);
            alert(jqXHR.statusText);
            /*弹出其他两个参数的信息*/
            alert(textStatus);
            alert(errorThrown);
        }
      });
    }
  </script>
</body>

</html>