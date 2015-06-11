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
            <a href="###">重置密码</a>
        </div><!-- /.login-logo -->
        <div class="login-box-body">
            <div class="form form-horizontal form-custom form-add-user">
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
                <div class="form-group has-feedback">
                    <label class="col-xs-3 control-label">
                        用户邮箱
                    </label>
                    <div class="col-xs-8">
                        <p class="form-control-static">
                            <?php echo $model->Email;?>
                        </p>
                    </div>
                </div>
                <div class="form-group has-feedback<?php echo $model->hasError('Password')?' has-error':'';?>">
                     <label class="col-xs-3 control-label">
                        新的密码
                    </label>
                    <div class="col-xs-8">
                        <?php echo $form->textField($model,'Password',array('placeholder'=>'','class'=>'form-control'));?>
                         <?php if ($model->getError('Password')):?>
                            <span class="help-inline">
                                <i class="fa fa-times-circle-o"></i>
                                <?php echo $model->getError('Password');?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="form-group has-feedback<?php echo $model->hasError('Again')?' has-error':'';?>">
                    <label class="col-xs-3 control-label">
                        再输入一次密码
                    </label>
                    <div class="col-xs-8">
                        <?php echo $form->textField($model,'Again',array('placeholder'=>'','class'=>'form-control'));?>
                         <?php if ($model->getError('Again')):?>
                            <span class="help-inline">
                                <i class="fa fa-times-circle-o"></i>
                                <?php echo $model->getError('Again');?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-3 control-label">
                        &nbsp;
                    </label>
                    <div class="col-xs-8 btns-wrapper" style="margin-top:0;">
                        <button type="submit" class="btn btn-primary btn-flat">确认</button>
                        <button type="reset" class="btn btn-default btn-flat">取消</button>
                    </div>
                </div>
                <?php $this->endWidget(); ?>
            </div>
            
            <hr>
            <div style="font-size:12px;margin:10px 0; ">
                <p>
                密码规则：
                </p>
                <ol style="list-style-type:upper-alpha;line-height:2">
                    <li>密码可用字符为字母（区分大小写）、数字、特殊符号（+=-@#~,.[]()!%^*$）；</li>
                    <li>密码最小长度为 8，最大长度为 30；</li>
                    <li>密码强度为强。密码字符串必须为以下任一一种组合
                    <ol>
                        <li>大写字母+小写字母+至少2个特殊字符；</li>
                        <li>大写字母+至少2个数字+至少2个特殊字符；</li>
                        <li>小写字母+至少2个数字+至少2个特殊字符；</li>
                        <li>大写字母+小写字母+数字+至少2个特殊字符。</li>
                    </ol>
                    </li>
                </ol>
            </div>
        </div><!-- /.login-box-body -->
    </div>
</body>
</html>
