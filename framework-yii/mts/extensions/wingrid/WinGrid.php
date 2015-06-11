<?php

class WinGrid extends CInputWidget
{
    const ASSETS_NAME='/wingrid';
    public $json_url = '/json/grid';
    public $className;
    public $condition;
    public $idField='id';
    public $textField='name';
    public $title;
    public $url;
    public $toolbar;
    public $single=true;
    public $columns = array();
    public $callback;
    public $script;
    public $width = 600;
    public $search=true;

    public function init()
    {
        $this->initWinGrid();
        return parent::init();
    }
    public function run()
    {
        list($name,$id)=$this->resolveNameID();
        if(isset($this->htmlOptions['id']))
            $id=$this->htmlOptions['id'];
        if(isset($this->htmlOptions['name']))
            $name=$this->htmlOptions['name'];
        $showid = $id."_show";
        $htmlOpions = $this->htmlOptions;
        $htmlOpions['id'] = $showid;
        $htmlOpions['name'] = null;

        if($this->hasModel()) {
            $value = CHtml::resolveValue($this->model, $this->attribute);
        }
        else {
            $value = $this->value;
        }

        $table = CActiveRecord::model($this->className);
        $this->idField = $table->getMetaData()->tableSchema->primaryKey;
        if (is_array($value)) {
            $textvals = $table->findColumnsByAttributes($this->textField, array($this->idField=>$value));
            $textval = implode(",", $textvals);
            $value = implode(",", $value);
        }
        else {
            $textval = $table->findColumnByAttributes($this->textField, array($this->idField=>$value));
        }
        echo CHtml::textField(null,$textval,$htmlOpions);
        echo CHtml::hiddenField($name,$value,array('id'=>$id));
        $this->renderScript($id, $showid);

    }
    public function renderScript($id, $showid)
    {
        $cs = Yii::app()->getClientScript();
        $cs->registerCoreScript('jquery');
        Yii::app()->registerJEasyUI();
        $assets = Yii::app()->getAssetManager()->publish(dirname(__FILE__).DIRECTORY_SEPARATOR.'assets');
        $cs->registerCssFile($assets.'/icon.css');
        $cs->registerCssFile($assets.self::ASSETS_NAME.'.css');
        $cs->registerScriptFile($assets.self::ASSETS_NAME.'.js',CClientScript::POS_END);
        $list = array('title','toolbar','single','url','idField','columns', 'callback','width', 'search');
        $options = array();
        foreach ($list as $k) {
            if (!empty($this->$k)) {
                $options[$k] = $this->$k;
            }
        }
        if (empty($options['callback'])) {
            $options['callback'] = "js:function(rows){
                    var keys = [];
                    var vals = [];
                    for (var i=0;i<rows.length;i++) {
                        if (rows[i]['{$this->textField}']) {
                            keys.push(rows[i]['{$this->idField}']);
                            vals.push(rows[i]['{$this->textField}']);
                        }
                    }
                    $('#{$showid}').val(vals.join(','));
                    $('#{$id}').val(keys.join(','));
            }";
        }
        else {
            $options['callback'] = 'js:'.$options['callback'];
        }
        $options['id'] = $id;
        $options['showid'] = $showid;
        $options=CJavaScript::encode($options);
        $js = "jQuery('#{$showid}').wingrid($options);";
        $cs->registerScript(__CLASS__.'#'.$id, $js);
        if ($this->script) {
            $cs->registerScript(__CLASS__.'#'.$id."_script", $this->script);
        }

    }
    public function initWinGrid()
    {
        $this->validate();
        if (empty($this->url)) {
            $params = array('table'=>$this->className);
            if ($this->condition)
                $params['condition'] = $this->condition;
            $this->url = Yii::app()->request->baseUrl.$this->json_url."?".http_build_query($params);
        }
        $table = CActiveRecord::model($this->className);
        if (empty($this->columns)) {
            $count = 0;
            $columns = $table->tableSchema->columns;
            foreach ($columns as $column) {
                if ($column->autoIncrement ||$column->type == 'integer') continue;
                if (++$count > 5) break;
                $this->columns[] = array(
                    'field'=>$column->name,
                    'title'=>$table->getAttributeLabel($column->name)
                );
            }
        }
        else {
            $columns = $this->columns;
            $this->columns = array();
            foreach ($columns as $k=>$v) {
                if (is_string($v)) {
                    if ($table->hasAttribute($v)) {
                        $this->columns[] = array(
                            'field'=>$v,
                            'title'=>$table->getAttributeLabel($v)
                        );
                    }
                    else if ($table->hasAttribute($k)) {
                        $this->columns[] = array(
                            'field'=>$k,
                            'title'=>$table->getAttributeLabel($k)
                        );
                    }
                }
                else if (is_array($v)) {
                    $columnrow = array();
                    $keys = array('field','title','width','rowspan','colspan','align','sortable','checkbox');
                    foreach ($keys as $i=>$key) {
                        if (isset($v[$key])) {
                            $columnrow[$key] = $v[$key];
                        }
                        else if (isset($v[$i])){
                            $columnrow[$key] = $v[$i];
                        }
                    }
                    if ($columnrow['field'] && empty($columnrow['title']) && $table->hasAttribute($columnrow['field'])) {
                        $columnrow['title'] = $table->getAttributeLabel($columnrow['field']);
                    }
                    $this->columns[] = $columnrow;
                }
            }
        }
    }
    public function validate()
    {
        if (empty($this->className))
            throw new CException('className required');
        $table = CActiveRecord::model($this->className);
        if (!$this->idField || !$table->hasAttribute($this->idField)) {
            $key = $table->getPrimaryKey();
            if (is_array($key)) $key = current($key);
            $this->idField = $key;
        }
        if (!$this->textField || !$table->hasAttribute($this->textField)) {
            $this->textField = $this->idField;
        }
    }
}
