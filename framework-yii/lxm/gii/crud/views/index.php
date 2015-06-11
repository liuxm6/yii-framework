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
<h1>Local Crud Generator</h1>

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
        <div class="tooltip">模块名
        </div>
        <?php echo $form->error($model,'desc'); ?>
    </div>
    <div class="row">
        Index<?php echo $form->checkBox($model,'hasIndex'); ?>&nbsp;
        Add<?php echo $form->checkBox($model,'hasAdd'); ?>&nbsp;
        Edit<?php echo $form->checkBox($model,'hasEdit'); ?>&nbsp;
        View<?php echo $form->checkBox($model,'hasView'); ?>&nbsp;
        Del<?php echo $form->checkBox($model,'hasDel'); ?>&nbsp;
        Delall<?php echo $form->checkBox($model,'hasDelall'); ?>&nbsp;
        Export<?php echo $form->checkBox($model,'hasExport'); ?>&nbsp;
        Import<?php echo $form->checkBox($model,'hasImport'); ?>&nbsp;
        Search<?php echo $form->checkBox($model,'hasSearch'); ?>&nbsp;
        checkbox<?php echo $form->checkBox($model,'hasCheck'); ?>&nbsp;
        <div class="tooltip">自动生成的action
        </div>
        <?php echo $form->error($model,'desc'); ?>
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
