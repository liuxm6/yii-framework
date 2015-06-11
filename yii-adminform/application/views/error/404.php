<?php if(!Yii::app()->user->isGuest):?>
    <?php $this->beginContent('//layouts/main'); ?>
<?php endif;?>
<section class="content-header content-header-custom">
   
</section>
<section class="content">
    <div class="error-page">
            <h2 class="headline text-yellow"> 404</h2>
            <div class="error-content">
              <h3>
              哎呀，您访问的页面不存在！
              </h3>
              <p>
              请打开其他页面试试！
              </p>
              <p class="btns-wrapper">
                <a href="<?php echo $this->createUrl('/dashboard/index') ?>" class="btn btn-defualt">返回首页</a>
              </p>
            </div><!-- /.error-content -->
          </div>
</section>
<?php if(!Yii::app()->user->isGuest):?>
    <?php $this->endContent(); ?>
<?php endif;?>