<<?php echo '?';?>php
    $params = array_merge($_GET, $_POST);
    $model=new <?php echo $this->model; ?>('search');
    $model->unsetAttributes();
    $criteria=new CDbCriteria;
    $criteria->with = array(
<?php
foreach ($this->with as $with) {
    echo "        '".$with."',\n";
}
?>
    );
<?php
foreach ($this->listData as $one) {
    if (!empty($this->with)) {
        if (strpos($one[2],'.') !== false) {
            $rname = $one[2];
        }
        else {
            $rname = 't.'.$one[2];
        }
    }
    else {
        $rname = $one[2];
    }
    echo "    \$criteria->compare('$rname',\$params['".$one[0]."'],true);\n";
}
?>
    $page = isset($params['page']) && $params['page']>0?$params['page']-1:0;
    $pageSize = isset($params['pageSize']) && $params['pageSize']>0?$params['pageSize']:10;
    $dp = new CActiveDataProvider($model, array(
        'criteria'=>$criteria,
    ));
    $dp->getPagination()->setPageSize($pageSize);
    $dp->getPagination()->setCurrentPage($page);
    $data = $dp->getData();
    $total = $dp->getTotalItemCount();
    $rows = array();
    foreach ($data as $k=>$v) {
        $row = array();
        $row['<?php echo $this->idColumn;?>'] = $v-><?php echo $this->idColumn;?>;
        $row['<?php echo $this->nameColumn;?>'] = $v-><?php echo $this->nameColumn;?>;
<?php
foreach ($this->listData as $one) {
    echo "        \$row['".$one[0]."']=\$v->value('".$one[2]."');\n";
}
?>
        $rows[] = $row;
    }
    $pageCount = ceil($total/$pageSize);
    if ($page>$pageCount-1) $page = $pageCount-1;
    echo json_encode(array('total'=>$total, 'page'=>$page+1,'pageSize'=>$pageSize,'pageCount'=>$pageCount,'rows'=>$rows));