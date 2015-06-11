<?php echo "<?php\n"; ?>
Yii::app()->registerCssFile('detailview/styles.css');
?>
<div class="body-nav">
    <a href="<?php echo '<?php echo $this->createUrl(\'index\')?>';?>" class="easyui-linkbutton" plain="true" iconcls="icon-back">返回列表</a>
</div>
<table class="detail-view">
<?php
$idx = 0;
$relationNames = $this->table->relationNames();
foreach($this->tableSchema->columns as $column)
{
?>
    <tr class="<?php echo $idx++%2==0?"odd":"even"?>">
        <th><?php echo '<?php echo CHtml::encode($model->getAttributeLabel(\''.$column->name.'\')); ?>:'?></th>
<?php if (array_key_exists($column->name, $relationNames)):?>
        <td><?php echo '<?php echo CHtml::encode($model->'.$relationNames[$column->name][0].'->'.$relationNames[$column->name][1].'); ?>'?></td>
<?php else:?>
        <td><?php echo '<?php echo CHtml::encode($model->'.$column->name.'); ?>'?></td>
<?php endif;?>
    </tr>
<?php
}
?>
</table>
