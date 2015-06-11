<?php
$class=get_class($model);
Yii::app()->clientScript->registerScript('gii.crud',"
$('#{$class}_controller').change(function(){
    $(this).data('changed',$(this).val()!='');
});
$('#{$class}_model').bind('keyup change', function(){
    var controller=$('#{$class}_controller');
    if(!controller.data('changed')) {
        var id=new String($(this).val().match(/\\w*$/));
        if(id.length>0) id=id.toLowerCase();
            //id=id.substring(0,1).toLowerCase()+id.substring(1);

        controller.val(id);
    }
});
");

?>
<h1>Crud Generator</h1>

<?php $form=$this->beginWidget('CCodeForm', array('model'=>$model)); ?>
    <div class="row">
        <?php echo $form->labelEx($model,'model'); ?>
        <?php echo $form->textField($model,'model',array('size'=>65)); ?>
        <div class="tooltip">
            Model class is case-sensitive. It can be either a class name (e.g. <code>Post</code>)
            or the path alias of the class file (e.g. <code>application.models.Post</code>).
            Note that if the former, the class must be auto-loadable.
        </div>
        <?php echo $form->error($model,'model'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'controller'); ?>
        <?php echo $form->textField($model,'controller',array('size'=>65)); ?>
        <div class="tooltip">
            Controller ID is case-sensitive. CRUD controllers are often named after
            the model class name that they are dealing with. Below are some examples:
            <ul>
                <li><code>post</code> generates <code>PostController.php</code></li>
                <li><code>postTag</code> generates <code>PostTagController.php</code></li>
                <li><code>admin/user</code> generates <code>admin/UserController.php</code>.
                    If the application has an <code>admin</code> module enabled,
                    it will generate <code>UserController</code> (and other CRUD code)
                    within the module instead.
                </li>
            </ul>
        </div>
        <?php echo $form->error($model,'controller');  ?>
    </div>
    <div class="row">
        <?php echo $form->labelEx($model,'module'); ?>
        <?php echo $form->textField($model,'module',array('size'=>65)); ?>
        <div class="tooltip">Module name
        </div>
        <?php echo $form->error($model,'module'); ?>
    </div>
    <div class="row">
        <?php echo $form->labelEx($model,'message'); ?>
        <?php echo $form->textArea($model,'message',array('cols'=>67,'rows'=>6)); ?>
        <div class="tooltip">list,add,edit各个页面的提醒文字
        </div>
        <?php echo $form->error($model,'message'); ?>
    </div>
    <div class="row">
        <?php echo $form->labelEx($model,'searchAttributes'); ?>
        <?php echo $form->textArea($model,'searchAttributes',array('cols'=>67,'rows'=>6)); ?>
        <div class="tooltip">格式：<br>
            <code>字段名:字段标签:关联字段:HTML参数</code>
        </div>
        <?php echo $form->error($model,'searchAttributes'); ?>
    </div>
    <div class="row">
        <?php echo $form->labelEx($model,'searchData'); ?>
        <?php if (!empty($model->searchData)) echo implode(", ", array_keys($model->searchData));?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'listAttributes'); ?>
        <?php echo $form->textArea($model,'listAttributes',array('cols'=>67,'rows'=>6)); ?>
        <div class="tooltip">list filed list
        </div>
        <?php echo $form->error($model,'listAttributes'); ?>
    </div>
    <div class="row">
        <?php echo $form->labelEx($model,'listData'); ?>
        <div style="word-break:break-word">
        <?php if (!empty($model->listData)) echo implode(", ", array_keys($model->listData));?>
        </div>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'addAttributes'); ?>
        <?php echo $form->textArea($model,'addAttributes',array('cols'=>67,'rows'=>6)); ?>
        <div class="tooltip">add filed list
        </div>
        <?php echo $form->error($model,'addAttributes'); ?>
    </div>
    <div class="row">
        <?php echo $form->labelEx($model,'addData'); ?>
        <div style="word-wrap:break-word">
        <?php if (!empty($model->addData)) echo implode(", ", array_keys($model->addData));?>
        </div>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'editAttributes'); ?>
        <?php echo $form->textArea($model,'editAttributes',array('cols'=>67,'rows'=>6)); ?>
        <div class="tooltip">
            Name:名称:Name:array('class'=>'form-control'):array('Name', 'required')|array('Name', 'length','max'=>40)
        </div>
        <?php echo $form->error($model,'editAttributes'); ?>
    </div>
    <div class="row">
        <?php echo $form->labelEx($model,'editData'); ?>
        <div style="word-wrap:break-word">
        <?php if (!empty($model->editData)) echo implode(", ", array_keys($model->editData));?>
        </div>
    </div>

    <div class="row sticky">
        <?php echo $form->labelEx($model,'baseControllerClass'); ?>
        <?php echo $form->textField($model,'baseControllerClass',array('size'=>65)); ?>
        <div class="tooltip">
            This is the class that the new CRUD controller class will extend from.
            Please make sure the class exists and can be autoloaded.
        </div>
        <?php echo $form->error($model,'baseControllerClass'); ?>
    </div>

<?php $this->endWidget(); ?>
