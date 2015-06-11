<div class="form">
<?php echo "<?php \$form=\$this->beginWidget('ActiveForm', array(
    'id'=>'".$this->class2id($this->modelClass)."-form',
    'enableAjaxValidation'=>false,
));\n?>\n"; ?>
<table class="edit-view">
<?php
$idx = 0;
foreach($this->tableSchema->columns as $column)
{
    if($column->autoIncrement)
        continue;
?>
        <tr class="<?php echo $idx++%2==0?"odd":"even"?>">
            <th><?php echo "<?php echo ".$this->generateActiveLabel($this->modelClass,$column)."; ?>"; ?></th>
            <td><?php echo "<?php echo ".$this->generateActiveField($this->modelClass,$column)."; ?>"; ?><?php echo "<?php echo \$form->error(\$model,'{$column->name}'); ?>"; ?></td>
        </tr>
<?php
}
?>
    <tr>
        <td><a href="<?php echo '<?php';?> echo $this->createUrl('index')?>" class="easyui-linkbutton" plain="true" iconcls="icon-back"style="margin-left:10%;padding-left:8px" >返回列表</a></td>
        <td><?php echo "<?php echo CHtml::linkButton(\$model->isNewRecord ? '添加' : '修改',array('class'=>'easyui-linkbutton','iconcls'=>'icon-add','plain'=>'true')); ?>"; ?></td>
    </tr>
    </table>
<?php echo '<?php $this->endWidget(); ?>';?>

</div>