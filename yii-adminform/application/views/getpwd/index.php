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
    <div class="login-box">
        <div class="login-logo">
            <a href="###">忘记密码</a>
        </div><!-- /.login-logo -->
        <div class="login-box-body">
            <p class="login-box-msg">
            重置密码邮件已经发送到您的邮箱，请查收
            </p>
             <?php 
                $form=$this->beginWidget('ActiveForm', array(
                    'id'=>'getpwd-form',
                    'enableClientValidation'=>true,
                    'enableAjaxValidation'=>false,
                    'clientOptions'=>array(
                        'validateOnSubmit'=>true,
                    ),
                )); 
            ?>
            <div style="padding-top:40px;">

                <div class="form-group has-feedback<?php echo $model->hasError('email')?' has-error':'';?>">
                    <?php echo $form->textField($model,'email',array('placeholder'=>'用户邮箱','class'=>'form-control'));?>
                    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                     <?php if ($model->getError('email')):?>
                        <span class="help-inline">
                            <i class="fa fa-times-circle-o"></i>
                            <?php echo $model->getError('email');?>
                        </span>
                    <?php endif; ?>
                </div>

                <div class="form-group text-center btns-wrapper">
                        <button type="submit" class="btn btn-primary btn-flat">确认</button>
                        <a href="<?php echo $this->createUrl('/index')?>" class="btn btn-default btn-flat">取消</a>
                </div>
            </div>
            <?php $this->endWidget(); ?>
        </div><!-- /.login-box-body -->
    </div>
</body>
</html>
