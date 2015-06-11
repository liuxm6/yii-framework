<?php echo "<?php echo \$this->renderPartial('partial/nav-index', array('model'=>\$model)); ?>\n"; ?>
<?php echo "<?php"; ?> $this->widget('GridView', array(
    'id'=>'<?php echo $this->class2id($this->modelClass); ?>-grid',
    'selectableRows'=> 2,
    'dataProvider'=> $model->search(),
<?php if ($this->hasSearch):?>
    'filter'=> $model,
<?php endif;?>
    'columns'=> array(
<?php if ($this->hasCheck):?>array(
            'class'=>'CCheckBoxColumn',
         ),
<?php endif;?>
<?php
$count=0;
$relationNames = $this->table->relationNames();
foreach($this->tableSchema->columns as $column)
{
    if ($column->autoIncrement) continue;
    if(++$count==8)
        echo "        /*\n";
    if (array_key_exists($column->name, $relationNames)) {
?>
        array(
            'name'=>'<?php echo $column->name?>',
            'value'=>'$data-><?php echo $relationNames[$column->name][0]?>-><?php echo $relationNames[$column->name][1]?>',
        ),
<?php
    }
    else
        echo "        '".$column->name."',\n";
}
if($count>=8)
    echo "        */\n";
?>
        array(
            'header'=>'操作',
            'class'=>'ButtonColumn',
            'template'=>'<?php if ($this->hasView):?>{view}<?php endif;?> <?php if ($this->hasEdit):?>{update}<?php endif;?> <?php if ($this->hasDel):?>{delete}<?php endif;?>'
        ),
    ),
    'pager'=>array(
        'header'=>'',
        'firstPageLabel'=>'首页',
        'lastPageLabel'=>'末页',
        'prevPageLabel'=>'上一页',
        'nextPageLabel'=>'下一页',
    )
)); ?>

<?php /* echo "<?php /*?>\n";?>
<a id="id-btn-delall" class="easyui-linkbutton">批量删除</a>
<a id="id-btn-export" class="easyui-linkbutton">导出</a>
<script>
$(function(){
    $("#id-btn-delall").click(function(){
        var ids = [];
        $(".select-on-check").each(function(){
            if ($(this).attr("checked"))
                ids.push($(this).val());
        });
        if (ids.length <= 0) {
            alert("请选择数据");
        }
        else {
            if(!confirm('确定删除选择的数据吗?')) return false;
            var th=this;
            var afterDelete=function(){};
            $.fn.yiiGridView.update('<?php echo $this->class2id($this->modelClass); ?>-grid', {
                type:'POST',
                url:"<?php echo "<?php";?> echo $this->createUrl('delall');?>",
                data:{ids:ids},
                success:function(data) {
                    $.fn.yiiGridView.update('<?php echo $this->class2id($this->modelClass); ?>-grid');
                    afterDelete(th,true,data);
                },
                error:function(XHR) {
                    return afterDelete(th,false,XHR);
                }
            });
            return false;
        }
        return false;
    });
    $("#id-btn-export").click(function(){
        surl = $.fn.yiiGridView.getUrl('<?php echo $this->class2id($this->modelClass); ?>-grid');
        epurl = surl.replace("<?php echo "<?php";?> echo Yii::app()->request->getUrl()?>", "<?php echo "<?php";?> echo $this->createUrl('export')?>");
        location.href = epurl;
    });
});


</script>
<?php echo "<?php";?> */?>