<section class="content-header content-header-custom">
    <h1 class="no-border" style="padding:0;">
        <?php echo $this->getMessage("add-title");?>

    </h1>
    <ol class="breadcrumb">
<?php $fa=true; foreach ((array)$this->getMessage("add-breadcrumb") as $one):?>
<?php
    if (is_array($one)) {
        $url = key($one);$urlname = current($one);
    }
    else  {
        $url = $one;$urlname = $one;
    }

?>
        <li><a href="<<?php echo "?";?>php echo $this->createUrl('<?php echo $url;?>');<?php echo "?";?>>"><?php if ($fa):?><i class="fa fa-dashboard"></i><?php $fa=false;endif;?><?php echo $urlname;?></a></li>

<?php endforeach;?>
        <li class="active"><?php echo $this->getMessage("add-title");?></li>
    </ol>
</section>
<section class="content">
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">
                <?php echo $this->getMessage("add-box-title");?>

            </h3>
        </div>
        <div class="box-body">
            <div class="">
                <div class="form form-horizontal form-custom">
                        <?php echo '<?php'?> 
                            $form = $this->beginWidget('ActiveForm', array(
                                'id'=>'<?php echo $this->class2id($this->modelClass)?>-form',
                                'enableAjaxValidation'=>false,
                            ));
                        ?>
                        <?php foreach($this->addData as $column=>$one):?>
                        <?php
                            $hasR = '';
                            if($one[4]){
                                $ar = explode('|',$one[4]);
                                 foreach($ar as $s){
                                   eval('$c='.$s.';');
                                   if($c[1]=='required'){
                                        $hasR = 'has-required';
                                   }
                                }
                            }
                        ?>

                        <div class="form-group <?php echo $hasR;?> <?php echo '<?php';?> echo $model->getError('<?php echo $one[0]?>')?' has-error':'';?>">
                           <label for="" class="col-xs-4 control-label">
                                <?php echo $one[1];?>

                           </label>
                           <div class="col-xs-8">
                                <?php echo '<?php';?> echo $form-><?php echo $one[2]?$one[2]:'textField';?>($model,'<?php echo $one[0]?>'<?php echo $one[3]?','.$one[3]:",array('class'=>'form-control')";?>); ?>
                                <span class="help-inline">
                                    <?php echo '<?php';?> echo $model->getError('<?php echo $one[0];?>');?>
                                </span>
                            </div>
                        </div>
                        <?php endforeach;?>

                        <div class="form-group">
                            <label class="col-xs-4 control-label"></label>
                            <div class="col-xs-8">
                                <div class="btns-wrapper">
                                    <button class="btn btn-primary" type="submit"><?php echo $this->getMessage("add-submit-text");?></button>
                                    <a href="<?php echo '<?php';?> echo $this->createUrl('<?php echo '/'.$this->module.'/'.$this->controller.'/index';?>')?>" class="btn btn-default"><?php echo $this->getMessage("add-return-text");?>

                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php echo '<?php $this->endWidget(); ?>';?>

                </div>
            </div>
        </div>
    </div>
</section>