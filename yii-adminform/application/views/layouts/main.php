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
  <body class="skin-yellow">
    <div class="wrapper">
      
      <header class="main-header">
        <!-- Logo -->
        <a href="<?php echo $this->createUrl('/index'); ?>" class="logo"><b>MTS</b>ADMIN</a>
        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-static-top" role="navigation">
          <!-- Sidebar toggle button-->
          <a href="javascript:void(0)" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
          </a>
          <!-- Navbar Right Menu -->
          <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
              <!-- User Account: style can be found in dropdown.less -->
              <li class="user user-menu">
                <a href="javascript:void(0)" class="">
                    <i class="fa fa-user"></i>
                  <span class="hidden-xs"><?php echo Yii::app()->user->name; ?></span>
                </a>
              </li>
              <li>
                <a class="btn btn-link" href="<?php echo $this->createUrl('/logout'); ?>">退出</a>
              </li>
            </ul>
          </div>
        </nav>
      </header>
      <!-- Left side column. contains the logo and sidebar -->
      <aside class="main-sidebar">
        <!-- sidebar: style can be found in sidebar.less -->
        <h5>后台管理菜单</h5>
        <section class="sidebar">
          <!-- sidebar menu: : style can be found in sidebar.less -->
          <ul class="sidebar-menu">
            <!-- <li class="header">MAIN NAVIGATION</li> -->
            <li class="treeview <?php echo $this->mts->active('dashboard',1); ?>">
              <a href="javascript:void(0)">
                <i class="fa fa-dashboard fa-fw"></i> <span>控制面板</span> 
                <i class="fa fa-angle-left pull-right"></i>
                <!-- <span class="label label-primary pull-right">4</span> -->
              </a>
              <ul class="treeview-menu">
                <li <?php echo $this->mts->active('dashboard',2); ?>>
                    <a href="<?php echo $this->createUrl('/dashboard/index'); ?>"><i class="fa fa-circle-o"></i> 我的主页</a>
                </li>
              </ul>
            </li>
            <li class="treeview <?php echo $this->mts->active('apply'); ?>">
              <a href="javascript:void(0)">
                <i class="fa fa-user-plus fa-fw"></i>
                <span>用户申请</span>
                <i class="fa fa-angle-left pull-right"></i>
                <!-- <span class="label label-primary pull-right">4</span> -->
              </a>
              <ul class="treeview-menu">

                <?php if($this->mts->checkAccess('MA')):?>
                <li <?php echo $this->mts->active('apply/member',2); ?>>
                    <a href="<?php echo $this->createUrl('/apply/member/index');?>"><i class="fa fa-circle-o"></i> 用户申请列表</a>
                </li>
                <?php endif;?>

                <?php if($this->mts->checkAccess('FA')):?>
                <li <?php echo $this->mts->active('apply/customization',2); ?>>
                    <a href="<?php echo $this->createUrl('/apply/customization/index'); ?>"><i class="fa fa-circle-o"></i> 制卷需求列表</a>
                </li>
                <?php endif;?>

                <li <?php echo $this->mts->active('apply/trial',2); ?>>
                    <a href="<?php echo $this->createUrl('/apply/trial/index'); ?>"><i class="fa fa-circle-o"></i> 试用情况查询</a>
                </li>

              </ul>
            </li>
            <?php if($this->mts->checkAccess('MM')):?>
            <li class="treeview <?php echo $this->mts->active('member'); ?>">
              <a href="javascript:void(0)">
                <i class="fa fa-users fa-fw"></i>
                <span>会员管理</span>
                <i class="fa fa-angle-left pull-right"></i>
              </a>
              <ul class="treeview-menu">

                <li <?php echo $this->mts->active('member/manage/add',2); ?>>
                    <a href="<?php echo $this->createUrl('/member/manage/add')?>"> <i class="fa fa-circle-o"></i> 开通会员</a>
                </li>

                <li <?php echo $this->mts->active('member/manage',2); ?>>
                    <a href="<?php echo $this->createUrl('/member/manage/search')?>"> <i class="fa fa-circle-o"></i> 会员管理</a>
                </li>

                <li <?php echo $this->mts->active('member/logs',2); ?>>
                    <a href="<?php echo $this->createUrl('/member/logs/search')?>"> <i class="fa fa-circle-o"></i> 操作日志</a>
                </li>

                <li <?php echo $this->mts->active('member/package/add',2); ?>>
                    <a href="<?php echo $this->createUrl('/member/package/add')?>"> <i class="fa fa-circle-o"></i> 新增套餐</a>
                </li>

                <li <?php echo $this->mts->active('member/package',2); ?>>
                    <a href="<?php echo $this->createUrl('/member/package/index')?>"> <i class="fa fa-circle-o"></i> 套餐管理</a>
                </li>

              </ul>
            </li>
            <?php endif;?>
            <?php if($this->mts->checkAccess('TM')):?>
            <li class="treeview <?php echo $this->mts->active('sessions'); ?>">
              <a href="javascript:void(0)">
                <i class="fa fa-file-text fa-fw"></i>
                <span>场次管理</span>
                <i class="fa fa-angle-left pull-right"></i>
              </a>
              <ul class="treeview-menu">
                <li <?php echo $this->mts->active('sessions/online',2); ?>>
                    <a href="<?php echo $this->createUrl('/sessions/online/search'); ?>"><i class="fa fa-circle-o"></i> 智测场次</a>
                </li>
                <li><a href="javascript:void(0)"><i class="fa fa-circle-o"></i> 智考场次</a></li>
              </ul>
            </li>
            <?php endif;?>
            <?php if($this->mts->checkAccess('SM')):?>
            <li class="treeview <?php echo $this->mts->active('scenes'); ?>">
              <a href="javascript:void(0)">
                <i class="fa fa-file-code-o fa-fw"></i> <span>模板管理</span>
                <i class="fa fa-angle-left pull-right"></i>
              </a>
              <ul class="treeview-menu">
                <li <?php echo $this->mts->active('scenes/online',2); ?>>
                    <a href="<?php echo $this->createUrl('/scenes/online/index'); ?>"><i class="fa fa-circle-o"></i> 智测模板</a>
                </li>
                <li><a href="javascript:void(0)" ><i class="fa fa-circle-o"></i> 智考模板</a></li>
              </ul>
            </li>
            <?php endif;?>
            <?php if($this->mts->checkAccess('FM')):?>
            <li class="treeview <?php echo $this->mts->active('forms'); ?>">
              <a href="javascript:void(0)">
                <i class="fa fa-file-code-o fa-fw"></i> <span>试卷管理</span>
                <i class="fa fa-angle-left pull-right"></i>
              </a>
              <ul class="treeview-menu">
                <li <?php echo $this->mts->active('forms/import',2); ?>>
                    <a href="<?php echo $this->createUrl('/forms/import/choose'); ?>"><i class="fa fa-circle-o"></i> 导入试卷</a>
                </li>
                <li <?php echo $this->mts->active('forms/testpaper',2); ?>>
                    <a href="<?php echo $this->createUrl('/forms/testpaper/search'); ?>"><i class="fa fa-circle-o"></i> 试卷查询</a>
                </li>
                <li <?php echo $this->mts->active('forms/code',2); ?>>
                    <a href="<?php echo $this->createUrl('/forms/code/search'); ?>" ><i class="fa fa-circle-o"></i> 试卷提取码查询</a>
                </li>
              </ul>
            </li>
            <?php endif;?>
            <?php if($this->mts->checkAccess('RM')):?>
            <li class="treeview <?php echo $this->mts->active('report'); ?>">
              <a href="javascript:void(0)">
                <i class="fa fa-file-code-o fa-fw"></i> <span>成绩报告</span>
                <i class="fa fa-angle-left pull-right"></i>
              </a>
              <ul class="treeview-menu">
                <li <?php echo $this->mts->active('report/template',2); ?>>
                    <a href="<?php echo $this->createUrl('/report/template/index')?>"><i class="fa fa-circle-o"></i> 成绩报告模版管理</a>
                </li>
                <li <?php echo $this->mts->active('report/reporttpl',2); ?>>
                    <a href="<?php echo $this->createUrl('/report/reporttpl/index')?>" ><i class="fa fa-circle-o"></i> 试卷绑定报告管理</a>
                </li>
              </ul>
            </li>
            <?php endif;?>
            <li>
              <a href="javascript:void(0)">
                <i class="fa fa-desktop fa-fw"></i> <span>在线制题管理</span> 
              </a>
            </li>
            <li>
              <a href="javascript:void(0)">
                <i class="fa fa-user-md fa-fw"></i> <span>人工阅卷管理</span> 
              </a>
            </li>
            <li class="treeview <?php echo $this->mts->active('settings'); ?>">
              <a href="javascript:void(0)">
                <i class="fa fa-cog fa-fw"></i> <span>设置</span>
                <i class="fa fa-angle-left pull-right"></i>
              </a>
              <ul class="treeview-menu">
                <?php if($this->mts->checkAccess('UM')):?>
                <li <?php echo $this->mts->active('settings/usermanage/add',2); ?>>
                    <a href="/settings/usermanage/add"><i class="fa fa-circle-o"></i> 新增用户</a>
                </li>
                <li <?php echo $this->mts->active('settings/usermanage',2); ?>>
                    <a href="<?php echo $this->createUrl('/settings/usermanage/index'); ?>"><i class="fa fa-circle-o"></i> 用户管理</a>
                </li>
                <?php endif;?>
                <li <?php echo $this->mts->active('settings/persion',2); ?>>
                    <a href="<?php echo $this->createUrl('/settings/persion/index'); ?>"><i class="fa fa-circle-o"></i> 个人资料</a>
                </li>
              </ul>
            </li>
          </ul>
        </section>
        <!-- /.sidebar -->
      </aside>

      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <?php echo $content;?>
      </div><!-- /.content-wrapper -->

      <footer class="main-footer">
        <div class="pull-right hidden-xs">
            Copyright &copy; 1999-2015 ATA Inc. All Rights Reserved.
        </div>
        &nbsp;
      </footer>

    </div><!-- ./wrapper -->
  </body>
</html>
