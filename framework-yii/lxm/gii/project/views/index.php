<h1>项目</h1>

<?php $form=$this->beginWidget('CCodeForm', array('model'=>$model)); ?>
    <div class="row sticky">
        <?php echo $form->labelEx($model,'defaultController'); ?>
        <?php echo $form->textField($model,'defaultController', array('size'=>40)); ?>
        <div class="tooltip">
        默认的控制器名
        </div>
        <?php echo $form->error($model,'defaultController'); ?>
    </div>
    <div class="row sticky">
        <?php echo $form->labelEx($model,'layout'); ?>
        <?php echo $form->textField($model,'layout', array('size'=>40)); ?>
        <div class="tooltip">
        布局默认文件名
        </div>
        <?php echo $form->error($model,'layout'); ?>
    </div>
    <div class="row ">
        <?php echo $form->labelEx($model,'charset'); ?>
        <?php echo $form->dropDownList($model,'charset', array('UTF-8'=>'UTF-8','GBK'=>'GB','BIG5'=>'BIG5','ISO-8859-1'=>'ANSI'), array('style'=>"width:275px")); ?>
        <div class="tooltip">
        字符编码集
        </div>
        <?php echo $form->error($model,'charset'); ?>
    </div>
    <div class="row ">
        <?php echo $form->labelEx($model,'language'); ?>
        <?php echo $form->dropDownList($model,'language', array('zh_cn'=>'中文','zh_tw'=>'繁體','en_us'=>'English'), array('style'=>"width:275px")); ?>
        <div class="tooltip">
        默认语言
        </div>
        <?php echo $form->error($model,'language'); ?>
    </div>
    <div class="row ">
        <?php echo $form->labelEx($model,'components_db_connectionString'); ?>
        <?php echo $form->textField($model,'components[db][connectionString]', array('size'=>40)); ?>
        <div class="tooltip">
        数据库连接
        <br><code>sqlite:path/to/dbfile</code>
        <br><code>sqlsrv:Server=localhost;Database=testdb</code>
        </div>
        <?php echo $form->error($model,'language'); ?>
    </div>
    <div class="row ">
        <?php echo $form->labelEx($model,'components_db_username'); ?>
        <?php echo $form->textField($model,'components[db][username]', array('size'=>40)); ?>
        <div class="tooltip">
        数据库用户
        </div>
        <?php echo $form->error($model,'language'); ?>
    </div>
    <div class="row ">
        <?php echo $form->labelEx($model,'components_db_password'); ?>
        <?php echo $form->textField($model,'components[db][password]', array('size'=>40)); ?>
        <div class="tooltip">
        数据库密码
        </div>
        <?php echo $form->error($model,'language'); ?>
    </div>
    <div class="row ">
        <?php echo $form->labelEx($model,'modules'); ?>
        <?php echo $form->textField($model,'modules', array('size'=>40)); ?>
        <div class="tooltip">
        模块
        </div>
        <?php echo $form->error($model,'language'); ?>
    </div>
<?php $this->endWidget(); ?>

