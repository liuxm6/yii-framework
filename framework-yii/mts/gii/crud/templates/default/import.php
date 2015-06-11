<div class="form">
<div class="body-nav">

</div>
<?php echo "<?php \$form=\$this->beginWidget('ActiveForm', array(
    'id'=>'import-form',
    'enableAjaxValidation'=>false,
    'htmlOptions'=>array('enctype'=>'multipart/form-data'),
));\n?>\n"; ?>
    <div class="row">
        <?php echo "<?php echo \$form->labelEx(\$model,'filename'); ?>\n"; ?>
        <?php echo "<?php echo \$form->fileField(\$model,'filename'); ?>\n"; ?>
        <?php echo "<?php echo \$form->error(\$model,'filename'); ?>\n"; ?>
    </div>

    <div class="row buttons">
        <?php echo "<?php echo CHtml::submitButton('提交'); ?>\n"; ?>
    </div>

<?php echo '<?php $this->endWidget(); ?>';?>

</div>