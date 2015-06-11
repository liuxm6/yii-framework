<?php
/**
 * This is the template for generating a controller class file for CRUD feature.
 * The following variables are available in this template:
 * - $this: the CrudCode object
 */
?>
<?php echo "<?php\n"; ?>


class <?php echo $this->controllerClass; ?> extends <?php echo $this->baseControllerClass."\n"; ?>
{
<?php if ($this->hasIndex):?>
    /**
     * @desc <?php echo $this->desc?>列表
     */
    public function actionIndex()
    {
        $model=new <?php echo $this->modelClass; ?>('search');
        $model->unsetAttributes();
        if(isset($_GET['<?php echo $this->modelClass; ?>']))
            $model->attributes=$_GET['<?php echo $this->modelClass; ?>'];

        $this->render('index',array(
            'model'=>$model,
        ));
    }
<?php endif;?>
<?php if ($this->hasView):?>
    /**
     * @desc 查看<?php echo $this->desc."\n"?>
     */
    public function actionView($id)
    {
        $this->render('view',array(
            'model'=>$this->loadModel($id),
        ));
    }
<?php endif;?>
<?php if ($this->hasAdd):?>
    /**
     * @desc 增加<?php echo $this->desc."\n"?>
     */
    public function actionAdd()
    {
        $model=new <?php echo $this->modelClass; ?>;
        $view = 'add';
        if(isset($_POST['<?php echo $this->modelClass; ?>']))
        {
            $model->attributes=$_POST['<?php echo $this->modelClass; ?>'];
            if($model->validate()) {
                if ($model->save()) {
                    $view = 'add-success';
                }
                else {
                    $view = 'add-error';
                }
            }
        }
        $this->render($view, array(
            'model'=>$model,
        ));
    }
<?php endif;?>
<?php if ($this->hasEdit):?>
    /**
     * @desc 编辑<?php echo $this->desc."\n"?>
     */
    public function actionEdit($id)
    {
        $model=$this->loadModel($id);
        $view = 'edit';

        if(isset($_POST['<?php echo $this->modelClass; ?>']))
        {
            $model->attributes=$_POST['<?php echo $this->modelClass; ?>'];
            if($model->validate()) {
                if ($model->save()) {
                    $view = 'edit-success';
                }
                else {
                    $view = 'edit-error';
                }
            }
        }

        $this->render($view, array(
            'model'=>$model,
        ));
    }
<?php endif;?>
<?php if ($this->hasDel):?>
    /**
     * @desc 删除<?php echo $this->desc."\n"?>
     */
    public function actionDel($id)
    {
        if(Yii::app()->request->isPostRequest)
        {
            $this->loadModel($id)->delete();

            if(!isset($_GET['ajax']))
                $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
        }
        else
            throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
    }
<?php endif;?>
<?php if ($this->hasDelall):?>
    /**
     * @desc 全删除<?php echo $this->desc."\n"?>
     */
    public function actionDelall()
    {
        if(Yii::app()->request->isPostRequest)
        {
            $ids = (array)$_POST['ids'];
            if (!empty($ids)) {
                $idstr = 'id in ('.implode(',', $ids).')';
                <?php echo $this->modelClass; ?>::model()->deleteAll($idstr);
            }
        }
        else
            throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
    }
<?php endif;?>
<?php if ($this->hasExport):?>
    /**
     * @desc <?php echo $this->desc?>导出
     */
    public function actionExport()
    {
        $this->exportFile('<?php echo $this->modelClass; ?>', array(
        <?php foreach($this->tableSchema->columns as $column): ?>
        '<?php echo $column->name?>'=>'',
        <?php endforeach;?>
        ));
        Yii::app()->end();
    }
<?php endif;?>
<?php if ($this->hasImport):?>
    /**
     * @desc <?php echo $this->desc?>导入
     */
    public function actionImport()
    {
        $model=new ImportForm;
        if(isset($_POST['ajax']) && $_POST['ajax']==='import-form')
        {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
        $data = array();
        $ignores = array();
        $error = false;
        if(isset($_POST['ImportForm']))
        {
            $model->attributes=$_POST['ImportForm'];
            try {
                $data = $this->getImportExcelFileData($model, 'filename');
            }
            catch (Exception $e) {
                $error = $e->getMessage();
            }
            if ($error) {
                $this->render('import-error', array('error'=>$error));
            }
            else {
                foreach ($data as $row) {
                    //$savemodel = new <?php echo $this->modelClass?>();
                    //读取的$row数据，写入到模块中
                    //写入出错，计入忽略数组
                    //if (!$savemodel->save()) $ignores[] = $row;
                }
                $this->render('import-success', array('ignores'=>$ignores));
            }
        }
        else {
            $this->render('import', array('model'=>$model));
        }
    }
<?php endif;?>


    protected function loadModel($id)
    {
        $model=<?php echo $this->modelClass; ?>::model()->findByPk($id);
        if($model===null)
            throw new CHttpException(404,'The requested page does not exist.');
        return $model;
    }
    protected function performAjaxValidation($model)
    {
        if(isset($_POST['ajax']) && $_POST['ajax']==='<?php echo $this->class2id($this->modelClass); ?>-form')
        {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }
}
