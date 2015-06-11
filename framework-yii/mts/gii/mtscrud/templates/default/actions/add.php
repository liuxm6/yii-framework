<?php echo "<?php\n"; ?>

        $model = new <?php echo ucfirst($this->controller); ?>AddModel();
        $view = 'add';
        $data = array();
        if(isset($_POST['<?php echo ucfirst($this->controller); ?>AddModel']))
        {
            $model->attributes=$_POST['<?php echo ucfirst($this->controller); ?>AddModel'];
            if($model->validate(null,false)) {
                $dbmodel = new <?php echo ucfirst($this->modelClass); ?>();
                $dbmodel->attributes=$model->attributes;
                if ($dbmodel->save()) {
                    $view = 'add-succ';
                    $data['msg'] = '<?php echo $this->getMessage("add-succ-message")?>';
                }
                else {
                    $view = 'add-fail';
                    $data['msg'] = '<?php echo $this->getMessage("add-fail-message")?>';
                }
            }
        }
        $data['model'] = $model;
        $data['backurl'] = $this->createUrl('<?php echo '/'.$this->module.'/'.$this->controller.'/index';?>');
        $this->render($view, $data);