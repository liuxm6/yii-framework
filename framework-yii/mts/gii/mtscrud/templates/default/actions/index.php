<<?php echo "?"?>php

    $searchModel = new <?php echo ucfirst($this->controller); ?>SearchModel;
    $listModel = new <?php echo ucfirst($this->controller); ?>ListModel;

    if ($_GET['<?php echo ucfirst($this->controller); ?>SearchModel']) {
        $searchModel->attributes = $_GET['<?php echo ucfirst($this->controller); ?>SearchModel'];
    }
    $criteria = $searchModel->getCriteria();
    $linkUrl = Yii::app()->request->requestUri;
    $pageParams = array(
        'linkUrl'=>$linkUrl,
        'pageVar'=>'page',
        'pageSize'=>15,
        'navNum'=>5,
    );
    $pageContent = $listModel->render($this, $criteria, 'page', $pageParams, true);

    $params = array(
        'searchModel' => $searchModel,
        'listModel'   => $listModel,
        'pageContent' => $pageContent,
        'linkUrl'     => $linkUrl,
    );

    $this->render('index', $params);