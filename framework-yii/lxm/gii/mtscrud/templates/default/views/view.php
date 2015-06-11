<!-- Content Header (Page header) -->
<section class="content-header content-header-custom">
    <h1>
        <?php echo $this->getMessage("view-title");?>
    </h1>
     <ol class="breadcrumb">
<?php $fa=true; foreach ((array)$this->getMessage("view-breadcrumb") as $one):?>
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
        <li class="active"><?php echo $this->getMessage("view-title");?></li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">
                <?php echo $this->getMessage("view-box-title");?>
            </h3>
        </div>
        <div class="box-body">
            <div class="">
                <div class="form form-horizontal form-custom">
<?php
$idx = 0;
$relationNames = $this->table->relationNames();
foreach($this->tableSchema->columns as $column)
{
?>
<div class="form-group">
    <label for="" class="col-xs-4 control-label"><?php echo '<?php echo CHtml::encode($model->getAttributeLabel(\''.$column->name.'\')); ?>:'?></label>
    <div class="col-xs-8">
        <p class="form-control-static">

<?php if (array_key_exists($column->name, $relationNames)):?>
        <span class="class-rar"><?php echo '<?php echo CHtml::encode($model->'.$relationNames[$column->name][0].'->'.$relationNames[$column->name][1].'); ?>'?></span>
<?php else:?>
        <span class="class-rar"><?php echo '<?php echo CHtml::encode($model->'.$column->name.'); ?>'?></span>
<?php endif;?>
        </p>
    </div>
</div>
<?php
}
?>
                    <div class="form-group">
                        <label class="col-xs-4 control-label"></label>
                        <div class="col-xs-8">
                            <div class="btns-wrapper">
                                <a href="<?php echo '<?php';?> echo $this->createUrl('<?php echo '/'.$this->module.'/'.$this->controller.'/index';?>')?>" class="btn btn-default"><?php echo $this->getMessage("view-return-text");?></a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section><!-- /.content -->