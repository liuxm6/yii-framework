<?php echo "<?php\n"; ?>

    $id = Yii::app()->request->getParam("id");
    $model=<?php echo $this->modelClass; ?>::model()->findByPk($id);
    if($model===null)
        throw new CHttpException(404,'页面没有找到');
    $this->render('view',array(
        'model'=>$model,
        'backurl'=>$this->createUrl('index'),
    ));