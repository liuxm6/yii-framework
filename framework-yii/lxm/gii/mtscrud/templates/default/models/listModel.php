<?php echo "<?php\n"; ?>
/**
 * 列表模型
 */
class <?php echo ucfirst($this->controller); ?>ListModel extends FormModel
{
<?php
    foreach ($this->listData as $column=>$one):
?>
    public $<?php echo $column;?>;
<?php
    endforeach;
?>

    public function attributeLabels()
    {
        return array(
<?php
    foreach ($this->listData as $column=>$one):
?>
            '<?php echo $column;?>'=>'<?php echo $one[1];?>',
<?php
    endforeach;
?>
        );
    }

    public function render($controller, $criteria, $view, $params, $ret=false)
    {
        $model = <?php echo $this->model ?>::model();
        $count = $model->count($criteria);
        $linkUrl = isset($params['linkUrl'])?$params['linkUrl']:'';
        $pages = new Pagination($count);
        $pages->basePageUrl = $linkUrl;
        if (isset($params['pageVar'])) {
            $pages->pageVar = $params['pageVar'];
        }
        if (isset($params['pageSize'])) {
            $pages->pageSize = intval($params['pageSize']);
        }
        if (isset($params['navNum'])) {
            $pages->navNum = intval($params['navNum']);
        }
        $pages->applyLimit($criteria);
        $rowset = $model->findAll($criteria);
        $url = url_replace_param($linkUrl, array(), array($pages->pageVar));
        if (strstr($url, '?')) {
            if (substr($url,-1) != '&')
                $url .= '&'.$pages->pageVar.'=';
            else
                $url .= $pages->pageVar.'=';
        }
        else {
            $url .= '?'.$pages->pageVar.'=';
        }
        $list = $pages->getNavList();
        $currPage = $pages->getCurrentPage()+1;
        $data = array(
            'url'      => $url,
            'pages'    => $pages,
            'list'     => $list,
            'currPage' => $currPage,
            'rowset'   => $rowset,
            'model'    => $this,
        );
        if(empty($rowset))
            $view = 'index-empty';
        return $controller->renderPartial($view, $data, $ret);
    }
}