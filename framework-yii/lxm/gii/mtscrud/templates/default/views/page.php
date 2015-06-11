            <table class="table table-custom table-subject-custom">
                <thead>
                    <tr>
<?php foreach ($this->listData as $column=>$one):?>
                        <th><<?php echo '?';?>php echo $model->getAttributeLabel('<?php echo $column;?>');<?php echo '?';?>></th>
<?php endforeach;?>
                        <th><?php echo $this->getMessage("index-list-action");?></th>
                    </tr>
                </thead>
                <tbody>
<<?php echo '?';?>php foreach ($rowset as $row):<?php echo '?';?>>
                    <tr>
<?php foreach ($this->listData as $column=>$one):?>
<?php
   $express = str_replace("$", "\\$", $one[2]);
   $express = str_replace('"', '\\"', $one[2]);
   $express = str_replace('.', '->', $express);
?>
                        <td><<?php echo '?';?>php echo <?php echo '$row->'.$express; ?>;<?php echo '?';?>></td>
<?php endforeach;?>
                        <td>
                            <a href="<<?php echo "?";?>php echo url_replace_param($this->createUrl('edit'), array('id'=>$row-><?php echo $this->table->tableSchema->primaryKey;?>));<?php echo "?";?>>"><?php echo $this->getMessage("index-list-action-edit");?></a>
                            <a href="<<?php echo "?";?>php echo url_replace_param($this->createUrl('view'), array('id'=>$row-><?php echo $this->table->tableSchema->primaryKey;?>));<?php echo "?";?>>"><?php echo $this->getMessage("index-list-action-view");?></a>
                            <a class="cls-list-del" href="<<?php echo "?";?>php echo url_replace_param($this->createUrl('del'), array('id'=>$row-><?php echo $this->table->tableSchema->primaryKey;?>));<?php echo "?";?>>"><?php echo $this->getMessage("index-list-action-del");?></a>
                        </td>
                    </tr>
<<?php echo '?';?>php endforeach;<?php echo '?';?>>
                </tbody>
            </table>
<<?php echo '?';?>php
if ($pages->isShowPage()):
<?php echo '?';?>>

                    <div class="row pagination-custom">
                        <div class="col-xs-6">
                            <div class="info">
                                <span>
                                    共<<?php echo '?';?>php echo $pages->getPageCount();<?php echo '?';?>>页
                                </span>
                                <span>
                                    <input type="text" class="form-control input-code">
                                    <input type="button" value="Go" class="btn btn-default btn-sm" onclick="location.href='<<?php echo '?';?>php echo $url;<?php echo '?';?>>'+$(this).prev().val()">
                                </span>
                            </div>
                        </div>
                        <div class="col-xs-6 text-right">
                            <ul class="pagination">
<<?php echo '?';?>php if ($pages->getPreNavUrl()):<?php echo '?';?>>
                                <li><a href="<<?php echo '?';?>php echo $pages->getPreNavUrl();<?php echo '?';?>>" aria-label="Previous"><span aria-hidden="true">«</span></a></li>
<<?php echo '?';?>php else:<?php echo '?';?>>
                                <li class="disabled"><a href="javascript:void(0)" aria-label="Previous"><span aria-hidden="true">«</span></a></li>
<<?php echo '?';?>php endif;<?php echo '?';?>>
<<?php echo '?';?>php foreach ($list as $k=>$v):<?php echo '?';?>>
<<?php echo '?';?>php if ($k == $currPage): <?php echo '?';?>>
                                <li class="active"><a href="<<?php echo '?';?>php echo $v;<?php echo '?';?>>"><<?php echo '?';?>php echo $k; <?php echo '?';?>> <span class="sr-only">(current)</span></a></li>
<<?php echo '?';?>php else:<?php echo '?';?>>
                                <li><a href="<<?php echo '?';?>php echo $v;<?php echo '?';?>>"><<?php echo '?';?>php echo $k; <?php echo '?';?>></a></li>
<<?php echo '?';?>php endif;<?php echo '?';?>>
<<?php echo '?';?>php endforeach;<?php echo '?';?>>
<<?php echo '?';?>php if ($pages->getNextNavUrl()):<?php echo '?';?>>
                                <li><a href="<<?php echo '?';?>php echo $pages->getNextNavUrl();<?php echo '?';?>>" aria-label="Next"><span aria-hidden="true">»</span></a></li>
<<?php echo '?';?>php else:<?php echo '?';?>>
                                <li class="disabled"><a href="javascript:void(0)" aria-label="Next"><span aria-hidden="true">»</span></a></li>
<<?php echo '?';?>php endif;<?php echo '?';?>>
                            </ul>

                        </div>
                    </div>

<<?php echo '?';?>php
endif;
?>
<div class="modal fade" id="myModalTips" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title" id="myModalLabel">删除提示</h4>
            </div>
            <div class="modal-body">
                <p id="index-del-title">请确认是否删除。</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
            </div>
        </div>
    </div>
</div>
<script>
$(function(){
    $(".cls-list-del").click(function(){
        $.ajax({
            type:'POST',
            dataType:'json',
            url:$(this).attr('href'),
            success:function(data){
            }
        });
        return false;
    });
});
</script>