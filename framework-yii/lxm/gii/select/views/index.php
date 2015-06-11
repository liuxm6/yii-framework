<h1>Select Generator</h1>

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
        <?php echo $form->labelEx($model,'controllerString'); ?>
        <?php echo $form->textField($model,'controllerString',array('size'=>65)); ?>
        <div class="tooltip">选择控制器
        </div>
        <?php echo $form->error($model,'controllerString'); ?>
    </div>
    <div class="row">
        <?php echo $form->labelEx($model,'idColumn'); ?>
        <?php echo $form->textField($model,'idColumn',array('size'=>65)); ?>
        <div class="tooltip">数据字段
        </div>
        <?php echo $form->error($model,'idColumn'); ?>
    </div>
    <div class="row">
        <?php echo $form->labelEx($model,'nameColumn'); ?>
        <?php echo $form->textField($model,'nameColumn',array('size'=>65)); ?>
        <div class="tooltip">属性显示字段
        </div>
        <?php echo $form->error($model,'nameColumn'); ?>
    </div>
    <div class="row">
        <?php echo $form->labelEx($model,'listAttributes'); ?>
        <?php echo $form->textArea($model,'listAttributes',array('cols'=>67,'rows'=>6)); ?>
        <div class="tooltip">字段格式<br>
            <code>显示属性,显示名称,关联模型字段,搜索提示信息</code>
            显示属性禁止page,pageSize,及id和name所提示的字段<br>
            范例<br><code>
            memberName,会员名,member.Name,请输入会员名
            memberLogin,登录名,member.ShortName,请输入会员名
            </code>
        </div>
        <?php echo $form->error($model,'listAttributes'); ?>
    </div>
<?php $this->endWidget(); ?>
