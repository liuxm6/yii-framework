<div class="body-nav">
    <div class="float-right">
        <?php if ($this->hasAdd):?>
        <a href="<?php echo "<?php echo \$this->createUrl('add')?>"; ?>" class="easyui-linkbutton" iconCls="icon-add" plain="true" style="font-size:15px" >增加</a>
        <?php endif;?>
        <?php if ($this->hasExport):?>
        <a id="id-btn-export" href="#" class="easyui-linkbutton" iconCls="icon-export" plain="true" style="font-size:15px" >导出</a>
        <?php endif;?>
        <?php if ($this->hasImport):?>
        <a href="<?php echo "<?php echo \$this->createUrl('import')?>"; ?>" class="easyui-linkbutton" iconCls="icon-import" plain="true" style="font-size:15px" >导入</a>
        <?php endif;?>
        <?php if ($this->hasDelall):?>
        <a id="id-btn-delall" href="#" class="easyui-linkbutton" iconCls="icon-cancel" plain="true" style="font-size:15px" >批量删除</a>
        <?php endif;?>
    </div>
</div>

<script>
$(function(){
    <?php if ($this->hasDelall):?>
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
    <?php endif;?>
    <?php if ($this->hasExport):?>
    $("#id-btn-export").click(function(){
        surl = $.fn.yiiGridView.getUrl('<?php echo $this->class2id($this->modelClass); ?>-grid');
        epurl = surl.replace("<?php echo "<?php";?> echo Yii::app()->request->getUrl()?>", "<?php echo "<?php";?> echo $this->createUrl('export')?>");
        location.href = epurl;
        return false;
    });
    <?php endif;?>
});


</script>