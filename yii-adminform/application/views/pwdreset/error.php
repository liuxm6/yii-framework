<?php
    $cs=Yii::app()->clientScript;
    $cs->coreScriptPosition=CClientScript::POS_HEAD;

    $cs->registerScriptFile($this->mts->rootUrl.'plugins/jQuery/jquery.js');
    $cs->registerScriptFile($this->mts->rootUrl.'bootstrap/js/bootstrap.min.js');
    $cs->registerScriptFile($this->mts->rootUrl.'js/mts.js');
    $cs->registerScriptFile($this->mts->rootUrl.'js/app.min.js',CClientScript::POS_END);
    $cs->registerScriptFile($this->mts->rootUrl.'js/custom.js',CClientScript::POS_END);

    $cs->registerCssFile($this->mts->rootUrl.'bootstrap/css/bootstrap.min.css');
    $cs->registerCssFile($this->mts->rootUrl.'css/AdminLTE.min.css');
    $cs->registerCssFile($this->mts->rootUrl.'css/skins/_all-skins.min.css');
    $cs->registerCssFile($this->mts->rootUrl.'css/font-awesome.min.css');
    $cs->registerCssFile($this->mts->rootUrl.'css/ionicons.min.css');
    $cs->registerCssFile($this->mts->rootUrl.'css/admin-custom.css');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>智测</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
</head>
<body class="login-page">
    <nav class="navbar navbar-inverse outer-header">
        <div class="navbar-header">
            <a class="navbar-brand" href="#">
                <img alt="Brand" src="<?php echo $this->mts->rootUrl;?>img/logo.png">
            </a>
        </div>
    </nav>


    <div class="login-box" style="width:600px">
        <div class="login-logo">
            <a href="javascript:void(0)">重置密码</a>
        </div><!-- /.login-logo -->
        <div class="login-box-body">
            重置密码失败
            <div class="form form-horizontal form-custom form-add-user">
                <div class="form-group">
                    <label class="col-xs-3 control-label">
                        &nbsp;
                    </label>
                    <div class="col-xs-8 btns-wrapper" style="margin-top:0;">
                        <a href="<?php echo $this->createUrl('/index')?>" class="btn btn-primary btn-flat">确认</a>
                    </div>
                </div>
            </div>
        </div><!-- /.login-box-body -->
    </div>
</body>
</html>
