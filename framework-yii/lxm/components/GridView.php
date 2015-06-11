<?php
Yii::import('zii.widgets.grid.CGridView');
class GridView extends CGridView
{
    public $template="{items}\n<table width=100%><tr><td style=\"float:left\">{summary}</td><td>{pager}</td></tr></table>";
    public $summaryText = '总数 {count} 显示{start}-{end} ';
    public $emptyText = "没有数据";
    public function init()
    {
        parent::init();
    }
}