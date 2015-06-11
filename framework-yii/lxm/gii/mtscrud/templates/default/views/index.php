<section class="content-header content-header-custom">
    <h1>
        <?php echo $this->getMessage("index-list-title");?>

    </h1>
    <ol class="breadcrumb">
<?php $fa=true; foreach ((array)$this->getMessage("index-list-breadcrumb") as $one):?>
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
        <li class="active"><?php echo $this->getMessage("index-list-title");?></li>
    </ol>
</section>
<section class="content">
    <div class="box box-primary">
        <div class="box-header with-border">
            <div class="pull-right">
                <a class="btn btn-danger" href="<<?php echo "?";?>php echo $this->createUrl('add');<?php echo "?";?>>"><?php echo $this->getMessage("index-list-action-add");?></a>
            </div>
<?php if (!empty($this->searchData)):?>
<<?php echo '?';?>php
$form=$this->beginWidget('ActiveForm', array(
    'id'=>'search-form',
    'method' =>'get',
    'action' => url_replace_param($linkUrl,array(),array('<?php echo ucfirst($this->controller);?>SearchModel')),
));
<?php echo '?';?>>
            <div class="form-inline" style="margin-top:8px;">
<<?php echo '?';?>php
<?php foreach ($this->searchData as $one):?>
    echo $form->textField($searchModel, '<?php echo $one[0];?>',<?php echo !empty($one[3])?$one[3]:'array()';?>);
<?php endforeach;?>
<?php echo '?';?>>
                        <button class="btn btn-default btn-flat" id="submit"><?php echo $this->getMessage("index-list-action-search");?></button>
            </div>
<<?php echo '?';?>php $this->endWidget(); <?php echo '?';?>>
<?php endif;?>
        </div>
        <div class="box-body">
<<?php echo '?';?>php
        echo $pageContent;
<?php echo '?';?>>
        </div>
    </div>
</section>