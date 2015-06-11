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
</head>
<body class="login-page">
    <nav class="navbar navbar-inverse outer-header">
            <div class="navbar-header">
                <a class="navbar-brand" href="#">
                    <img alt="Brand" src="<?php echo $this->mts->rootUrl;?>img/logo.png">
                </a>
            </div>
    </nav>
    <div class="login-box">
        <div class="login-logo">
            <a href="###"><b>MTS</b> Admin</a>
        </div><!-- /.login-logo -->
        <div class="login-box-body">
            <p class="login-box-msg">
            请输入管理员的帐号和密码进行登录
            </p>
                <?php 
                    $form=$this->beginWidget('ActiveForm', array(
                        'id'=>'login-form',
                        'enableClientValidation'=>true,
                        'enableAjaxValidation'=>false,
                        'clientOptions'=>array(
                            'validateOnSubmit'=>true,
                        ),
                    )); 
                ?>
                <div class="form-group has-feedback<?php echo $model->hasError('username')?' has-error':'';?>">
                        <?php echo $form->textField($model,'username', array('class'=>'form-control','placeholder'=>'用户名'));?>
                        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                        <?php if ($model->getError('username')):?>
                            <span class="help-inline">
                                <i class="fa fa-times-circle-o"></i>
                                <?php echo $model->getError('username');?>
                            </span>
                        <?php endif; ?>
                </div> 
                <div class="form-group has-feedback<?php echo $model->hasError('password')?' has-error':'';?>">
                        <?php echo $form->passwordField($model,'password', array('class'=>'form-control','placeholder'=>'密码'));?>
                        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                        <?php if ($model->getError('password')):?>
                        <span class="help-inline">
                            <i class="fa fa-times-circle-o"></i>
                            <?php echo $model->getError('password');?>
                        </span>
                        <?php endif; ?>
                </div> 
                <div class="form-group text-center btns-wrapper">
                        <button type="submit" class="btn btn-primary btn-flat">登录</button>
                        <button type="reset" class="btn btn-default btn-flat">重置</button>
                </div>
                <div class="text-right">
                    <p><a href="<?php echo $this->createUrl('/getpwd')?>">忘记密码？</a></p>
                </div>
            <?php $this->endWidget(); ?>
        </div>
    </div>
</body>
</html>
