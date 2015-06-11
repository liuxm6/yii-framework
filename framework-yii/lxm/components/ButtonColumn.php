<?php
Yii::import('zii.widgets.grid.CButtonColumn');
class ButtonColumn extends CButtonColumn
{
    public $headerHtmlOptions = array('style'=>'width:200px');
    public $viewButtonUrl = 'Yii::app()->controller->createUrl("view",array("id"=>$data->primaryKey))';
    public $updateButtonUrl = 'Yii::app()->controller->createUrl("edit",array("id"=>$data->primaryKey))';
    public $deleteButtonUrl = 'Yii::app()->controller->createUrl("del",array("id"=>$data->primaryKey))';
    public $addButtonUrl = 'Yii::app()->controller->createUrl("add")';
    public $addButtonImageUrl;

    public function init()
    {
        if (!$this->viewButtonImageUrl) $this->viewButtonImageUrl = Yii::app()->publishCommon().'/css/gridview/view.png';
        if (!$this->updateButtonImageUrl) $this->updateButtonImageUrl = Yii::app()->publishCommon().'/css/gridview/update.png';
        if (!$this->deleteButtonImageUrl) $this->deleteButtonImageUrl = Yii::app()->publishCommon().'/css/gridview/delete.png';
        if (!$this->addButtonImageUrl) $this->addButtonImageUrl = Yii::app()->publishCommon().'/css/gridview/add.png';
        parent::init();
        $this->buttons['add'] = array(
            'label'=>'增加',
            'url'=>$this->addButtonUrl,
            'imageUrl'=> $this->addButtonImageUrl,
        );
    }
    protected function renderFilterCellContent()
    {
        echo CHtml::submitButton('搜索', array('id'=>'id-btn-search'));
        Yii::app()->clientScript->registerScript('search', "

        $('#id-btn-search').click(function(){
            $.fn.yiiGridView.update('user-grid', {
                data: $(this).serialize()
            });
            return false;
        });
        ");
    }
}