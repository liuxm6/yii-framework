<?php echo "<?php\n"; ?>

    if(Yii::app()->request->isPostRequest)
    {
        $id = Yii::app()->request->getParam("id");
        $model=<?php echo $this->modelClass; ?>::model()->findByPk($id);
        if($model===null)
            throw new CHttpException(404,'页面没有找到');
        $model->delete();
        if(!isset($_GET['ajax']))
            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
    }
    else
        throw new CHttpException(400,'错误请求');