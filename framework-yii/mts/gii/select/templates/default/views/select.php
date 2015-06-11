<?php
    $selectGuid = substr(md5($this->controllerString),0,8);
    $varname = '_select_'.str_replace('/','_', $this->controllerString);
?>
<<?php echo '?';?>php
    if (!$dialogTitle) $dialogTitle='数据选择';
    $id = md5(serialize(debug_backtrace()));
    if (!isset($htmlOpions['id']))
        $htmlOpions['id'] = $id.'-show';
    $model = <?php echo $this->model;?>::model()->findByAttributes(array(
        '<?php echo $this->idColumn;?>'=>$value,
    ));
    if ($model) {
        $textval = $model-><?php echo $this->nameColumn;?>;
    }

    echo CHtml::hiddenField($name,$value,array('id'=>$id));
    echo CHtml::textField(null,$textval,$htmlOpions);
global $<?php echo $varname;?>;
if (!$<?php echo $varname;?>) {
<?php echo '?';?>>

<div class="modal fade" id="<?php echo $selectGuid.'-dialog';?>" tabindex="-1" role="dialog" aria-labelledby=id="<?php echo $selectGuid.'-dialog-body';?>" aria-hidden="true">
    <div class="modal-dialog" style="width:800px" >
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title" id="<?php echo $selectGuid.'-dialog-title';?>"><<?php echo '?';?>php echo $dialogTitle;<?php echo '?';?>></h4>
            </div>
            <div class="modal-body" id="<?php echo $selectGuid.'-dialog-body';?>"  style="max-height:640px;height:540px">
                <div class="form-inline" style="margin-bottom:8px;">
<?php foreach ($this->listData as $one):?>
                    <input type="text" class="form-control" id="<?php echo $selectGuid.'-search-'.$one[0];?>" placeholder="<?php echo isset($one[3])?$one[3]:'请输入'.$one[1];?>">
<?php endforeach;?>
                    <button class="btn btn-default" style="width:70px;height:34px;padding:0" id="submit" onclick="<?php echo 'f'.$selectGuid.'load('.$selectGuid.'_currpage)';?>">搜索</button>
                </div>
                <table class="table table-custom">
                    <thead>
                      <tr>
                         <th width=20></th>
<?php foreach ($this->listData as $one):?>
                         <th><?php echo $one[1];?></th>
<?php endforeach;?>
                      </tr>
                    </thead>
                    <tbody id="<?php echo $selectGuid.'-table-body';?>">

                    </tbody>
                </table>

                <div class="row">
                    <div class="col-xs-6">
                        <div class="info">
                            <span>
                                总记录<span id="<?php echo $selectGuid."-page-total";?>"></span>条 页次<span id="<?php echo $selectGuid."-page-current";?>"></span>/<span id="<?php echo $selectGuid."-page-count";?>"></span>
                            </span>
                        </div>
                    </div>
                    <div class="col-xs-6 text-right">
                        <ul class="pagination" style="margin:0">
                            <li><a href="#" onclick="<?php echo 'f'.$selectGuid.'load('.$selectGuid.'_currpage-1)';?>">上一页</a></li>
                            <li><a href="#" onclick="<?php echo 'f'.$selectGuid.'load('.$selectGuid.'_currpage+1)';?>">下一页</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" id="<?php echo $selectGuid.'-dialog-confirm';?>">确定</button>
                <button type="button" class="btn btn-default" data-dismiss="modal" id="<?php echo $selectGuid.'-dialog-cancel';?>">取消</button>
            </div>
        </div>
    </div>
</div>
<script>
    var <?php echo $selectGuid;?>_currpage;
    var <?php echo $selectGuid;?>_select_id;
    $("#<?php echo $selectGuid.'-dialog-confirm';?>").click(function(){
        var radio = $("input[name=<?php echo $selectGuid."-field-radio";?>]:checked");
        if (radio) {
            $("#"+<?php echo $selectGuid;?>_select_id).val(radio.val());
            $("#"+<?php echo $selectGuid;?>_select_id+"-show").val(radio.attr('show'));
        }
        return true;
    });
    function <?php echo 'f'.$selectGuid.'load(page,callback)';?>{
        $.ajax({
            type:'POST',
            dataType:'json',
            url:'<<?php echo '?'; ?>php echo $this->createUrl("/<?php echo $this->controllerString;?>/json");<?php echo '?';?>>',
            data:{
               'page':page,
               'pageSize':8,
<?php foreach ($this->listData as $one):?>
                '<?php echo $one[0];?>':$("#<?php echo $selectGuid.'-search-'.$one[0];?>").val(),
<?php endforeach;?>
            },
            success:function(data){
                <?php echo $selectGuid;?>_currpage = data.page;
                $("#<?php echo $selectGuid."-page-total";?>").html(data.total);
                $("#<?php echo $selectGuid."-page-count";?>").html(data.pageCount);
                $("#<?php echo $selectGuid."-page-current";?>").html(data.page);
                var html = '';
                for(var n in data.rows) {
                    html += '<tr>';
                    html += '<td><input type="radio" name="<?php echo $selectGuid."-field-radio";?>" show="'+data.rows[n].<?php echo $this->nameColumn;?>+'" value="'+data.rows[n].<?php echo $this->idColumn;?>+'" style="margin:0"></td>';
<?php foreach ($this->listData as $one):?>
                    html += '<td>'+data.rows[n].<?php echo $one[0];?>+'</td>';
<?php endforeach;?>
                    html += '</tr>';
                }
                $("#<?php echo $selectGuid.'-table-body';?>").html(html);
                if (callback) callback.call(data);
            }
        });
    }
</script>
<<?php echo '?';?>php
}
$<?php echo $varname;?> ++;
<?php echo '?';?>>
<script>
    $("#<<?php echo '?';?>php echo $id.'-show';<?php echo '?';?>>").click(function(){
        <?php echo $selectGuid;?>_select_id = '<<?php echo '?';?>php echo $id;<?php echo '?';?>>';
        <?php echo 'f'.$selectGuid;?>load(1,function(){
            $("#<?php echo $selectGuid.'-dialog';?>").modal({show: true,backdrop:false});
        });
    });
</script>
