<?php

$cdn = get_local().'/hm';

?>
<!doctype html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>hoo-modal</title>
    <link href="<?php echo $cdn?>/layui/layui-v2.9.20/css/layui.css" rel="stylesheet">
    <link href="<?php echo $cdn?>/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $cdn?>/icons-1.11.3/font/bootstrap-icons.css">
</head>
<body>
<script src="<?php echo $cdn?>/js/jquery.min.js"></script>
<script src="<?php echo $cdn?>/js/jquery.form.min.js"></script>

<script src="<?php echo $cdn?>/js/bootstrap.bundle.min.js"></script>

<script src="<?php echo $cdn?>/layui/layui-v2.9.20/layui.js"></script>
<script src="<?php echo $cdn?>/js/overall.js"></script>
<script src="<?php echo $cdn?>/js/base64js.min.js"></script>
<script src="<?php echo $cdn?>/js/sm4js.js"></script>
<script src="<?php echo $cdn?>/js/main.js"></script>
<script>
    var jump_link = function (url) {
        return '{{jump_link("")}}' + url;
    }
</script>
<style>
    .star::after{
        content:" *";
        color:red
    }
    .table{
        font-size: .875rem;
    }
    .table td{
        line-height: 1.5;
    }
</style>
<div class="container">
    <?php echo $content ?>
</div>

</body>
</html>
