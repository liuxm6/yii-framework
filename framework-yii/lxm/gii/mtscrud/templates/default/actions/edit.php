<?php echo "<?php\n"; ?>

    $id = Yii::app()->request->getParam("id");
    $dbmodel=<?php echo $this->modelClass; ?>::model()->findByPk($id);
    $model = new <?php echo ucfirst($this->controller); ?>EditModel();
    $model->attributes = $dbmodel->attributes;
    if($model===null)
        throw new CHttpException(404,'页面没有找到');
    $view = 'edit';
    $data = array();
    if(isset($_POST['<?php echo ucfirst($this->controller); ?>EditModel']))
    {
        $model->attributes=$_POST['<?php echo ucfirst($this->controller); ?>EditModel'];
        if($model->validate(null,false)) {
            $dbmodel->attributes = $model->attributes;
            if ($dbmodel->save()) {
                $view = 'edit-succ';
                $data['msg'] = '<?php echo $this->getMessage("edit-succ-message")?>';
            }
            else {
                $view = 'edit-fail';
                $data['msg'] = '<?php echo $this->getMessage("edit-fail-message")?>';
            }
        }
    }
    $data['model'] = $model;
    $data['backurl'] = $this->createUrl('<?php echo '/'.$this->module.'/'.$this->controller.'/index';?>');
    $this->render($view, $data);