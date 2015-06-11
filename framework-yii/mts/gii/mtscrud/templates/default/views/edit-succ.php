<section class="content-header content-header-custom">
    <h1>
        <?php echo $this->getMessage("edit-succ-title");?>

    </h1>
    <ol class="breadcrumb">
<?php $fa=true; foreach ((array)$this->getMessage("edit-succ-breadcrumb") as $one):?>
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
        <li class="active"><?php echo $this->getMessage("edit-succ-title");?></li>
    </ol>
</section>
<section class="content">
    <div class="box box-primary">
        <div class="box-body">
            <div class="callout callout-success">
                <p>
                    <?php echo '<?php';?> echo $msg;?>

                </p>
            </div>
            <div class="btns-wrapper">
                <a class="btn btn-default" href="<?php echo '<?php';?> echo $backurl;?>">
                    <?php echo $this->getMessage("edit-succ-return-text");?>

                </a>
            </div>
        </div>
    </div>
</section>