<?php
/**
 * mts 自动生成 c r u d
 */
class MtsCrudCode extends CCodeModel
{
    public $model;
    public $controller;
    public $module;
    public $message = "index-list-title:msg\nindex-empty-title:";
    public $messageData = array();
    public $searchAttributes;
    public $searchUseRelation=false;
    public $listAttributes;
    public $addAttributes;
    public $editAttributes;
    public $searchData=array();
    public $listData=array();
    public $addData=array();
    public $editData=array();

    public $baseControllerClass='Controller';

    private $_modelClass;
    private $_table;
    public $typeList = array(
        'textField'=> array('htmlOptions'),
        'textArea' => array('htmlOptions'),
        'passwordField'=> array('htmlOptions'),
        'radioButton'=> array('htmlOptions'),
        'checkBox'=> array('htmlOptions'),
        'radioButtonList'=> array('data','htmlOptions'),
        'listBox'=> array('data','htmlOptions'),
        'checkBoxList'=> array('data','htmlOptions'),
        'fckeditor'=> array('data','htmlOptions'),
   );


    public function rules()
    {
        return array_merge(parent::rules(), array(
            array('model, controller', 'filter', 'filter'=>'trim'),
            array('model, controller, baseControllerClass', 'required'),
            array('model', 'match', 'pattern'=>'/^\w+[\w+\\.]*$/', 'message'=>'{attribute} should only contain word characters and dots.'),
            array('controller', 'match', 'pattern'=>'/^\w+[\w+\\/]*$/', 'message'=>'{attribute} should only contain word characters and slashes.'),
            array('baseControllerClass', 'match', 'pattern'=>'/^[a-zA-Z_]\w*$/', 'message'=>'{attribute} should only contain word characters.'),
            array('baseControllerClass', 'validateReservedWord', 'skipOnError'=>true),
            array('model', 'validateModel'),
            array('baseControllerClass, module', 'sticky'),
            array('searchAttributes', 'validSearch'),
            array('listAttributes', 'validList'),
            array('addAttributes', 'validAdd'),
            array('editAttributes', 'validEdit'),
            array('message', 'validMessage'),
        ));
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), array(
            'model'=>'Model Class',
            'controller'=>'Controller ID',
            'baseControllerClass'=>'Base Controller Class',
            'module'=>'Module',
        ));
    }

    public function requiredTemplates()
    {
        return array(
            'controller.php',
        );
    }

    public function init()
    {
        parent::init();
    }

    public function successMessage()
    {
        $link=CHtml::link('try it now', Yii::app()->createUrl($this->controller), array('target'=>'_blank'));
        return "The controller has been generated successfully. You may $link.";
    }

    public function validateModel($attribute,$params)
    {
        if($this->hasErrors('model'))
            return;
        $class=Yii::import($this->model,true);
        if(!is_string($class) || !$this->classExists($class))
            $this->addError('model', "Class '{$this->model}' does not exist or has syntax error.");
        else if(!is_subclass_of($class,'CActiveRecord'))
            $this->addError('model', "'{$this->model}' must extend from CActiveRecord.");
        else
        {
            $table=CActiveRecord::model($class);
            $schema = $table->tableSchema;
            if($schema->primaryKey===null)
                $this->addError('model',"Table '{$schema->name}' does not have a primary key.");
            else if(is_array($schema->primaryKey))
                $this->addError('model',"Table '{$schema->name}' has a composite primary key which is not supported by crud generator.");
            else
            {
                $this->_modelClass=$class;
                $this->_table=$table;
            }
        }
    }
    public function validMessage($attribute,$params)
    {
        $list = explode("\n", $this->message);
        foreach ($list as $line) {
            $l = explode(":", trim($line));
            $c = count($l);
            if ($c > 1) {
                $msg = array_pop($l);
                $key = "['".implode("']['", $l)."']";
                eval("\$this->messageData".$key."='".$msg."';");
            }
        }
    }
    public function validSearch($attribute,$params)
    {
        if ($this->_table) {
            if (!empty($this->searchAttributes)) {
                $list = explode("\n", $this->searchAttributes);
                foreach ($list as $i=>$line) {
                    $line = trim($line);
                    if (empty($line)) continue;
                    list($column, $name, $showColumn,$htmlOptions) = explode(":", $line);
                    if (strpos($showColumn,'.') !== false)
                        $this->searchUseRelation = true;
                    if (empty($showColumn)) $showColumn = $column;
                    if (!isset($this->_table->tableSchema->columns[$column])) {
                        $this->addError('searchAttributes', 'line:'.($i+1).' column:'.$column.' is unkown');
                    }
                    if (empty($name)) {
                        list($name) = preg_split("/[;,\s]+/", trim($this->_table->tableSchema->columns[$column]->comment));
                        if (empty($name)) $name = strtoupper($column);
                    }
                    $this->searchData[$column] = array($column, $name, $showColumn, $htmlOptions);
                }
            }
        }
    }
    public function validList($attribute,$params)
    {
        if ($this->_table) {
            if (!empty($this->listAttributes)) {
                $list = explode("\n", $this->listAttributes);
                foreach ($list as $i=>$line) {
                    $line = trim($line);
                    if (empty($line)) continue;
                    list($column, $name, $showColumn, $htmlOptions) = explode(":", $line);
                    if (empty($showColumn)) $showColumn = $column;
                    if (empty($name)) {
                        list($name) = preg_split("/[;,\s]+/", trim($this->_table->tableSchema->columns[$column]->comment));
                        if (empty($name)) $name = strtoupper($column);
                    }
                    $this->listData[$column] = array($column, $name, $showColumn, $htmlOptions);
                }
            }
            else {
                $idKey = $this->_table->tableSchema->primaryKey;
                foreach ($this->_table->tableSchema->columns as $column=>$v) {
                    list($name) = preg_split("/[;,\s]+/", trim($v->comment));
                    if (empty($name)) $name = strtoupper($column);
                    $this->listData[$column] = array($column, $name, $column);
                }
            }
        }
    }
    public function validAdd($attribute,$params)
    {
        if ($this->_table) {
            if (!empty($this->addAttributes)) {
                $list = explode("\n", $this->addAttributes);
                foreach ($list as $i=>$line) {
                    $line = trim($line);
                    if (empty($line)) continue;
                    list($column, $name, $type, $data, $htmlOptions, $rules) = explode(":", $line);
                    if (!isset($this->typeList[$type])) {
                        $type = 'textField';
                    }
                    if (!isset($this->_table->tableSchema->columns[$column])) {
                        $this->addError('addAttributes', 'line:'.($i+1).' column:'.$column.' is unkown');
                    }
                    $this->addData[$column] = array($column, $name, $type, $data, $htmlOptions, $rules);
                }
            }
            else {
                $idKey = $this->_table->tableSchema->primaryKey;
                foreach ($this->_table->tableSchema->columns as $column=>$v) {
                    if ($column == $this->_table->tableSchema->primaryKey) continue;
                    $type = 'textField';
                    $htmlOptions = array();
                    if(stripos($v->dbType,'text')!==false) {
                        $type = 'textArea';
                        $htmlOptions = array('rows'=>6, 'cols'=>50);
                    }
                    list($name) = preg_split("/[;,\s]+/", trim($v->comment));
                    if (empty($name)) $name = strtoupper($column);
                    $this->addData[$column] = array($column, $name);
                }
            }
        }
    }
    public function validEdit($attribute,$params)
    {
        if ($this->_table) {
            if (!empty($this->editAttributes)) {
                $list = explode("\n", $this->editAttributes);
                foreach ($list as $i=>$line) {
                    $line = trim($line);
                    if (empty($line)) continue;
                    list($column, $name, $type, $data, $htmlOptions, $rules) = explode(":", $line);
                    if (!isset($this->typeList[$type])) {
                        $type = 'textField';
                    }
                    if (!isset($this->_table->tableSchema->columns[$column])) {
                        $this->addError('editAttributes', 'line:'.($i+1).' column:'.$column.' is unkown');
                    }
                    $this->editData[$column] = array($column, $name, $type, $data, $htmlOptions, $rules);
                }
            }
            else {
                $idKey = $this->_table->tableSchema->primaryKey;
                foreach ($this->_table->tableSchema->columns as $column=>$v) {
                    if ($column == $this->_table->tableSchema->primaryKey) continue;
                    $type = 'textField';
                    $htmlOptions = array();
                    if(stripos($v->dbType,'text')!==false) {
                        $type = 'textArea';
                        $htmlOptions = array('rows'=>6, 'cols'=>50);
                    }
                    list($name) = preg_split("/[;,\s]+/", trim($v->comment));
                    if (empty($name)) $name = strtoupper($column);
                    $this->editData[$column] = array($column, $name);
                }
            }
        }
    }
    public function prepare()
    {
        $this->files=array();
        $templatePath=$this->templatePath;
        $controllerTemplateFile=$templatePath.DIRECTORY_SEPARATOR.'controller.php';

        $this->files[]=new CCodeFile(
            $this->controllerFile,
            $this->render($controllerTemplateFile)
        );

        $scandir = $templatePath.DIRECTORY_SEPARATOR.'actions';
        $files=scandir($scandir);
        foreach($files as $file) {
            if(is_file($scandir.DIRECTORY_SEPARATOR.$file) && CFileHelper::getExtension($file)==='php')
            {
                $this->files[]=new CCodeFile(
                    $this->getControllerPath().DIRECTORY_SEPARATOR.$this->controller.DIRECTORY_SEPARATOR.$file,
                    $this->render($scandir.DIRECTORY_SEPARATOR.$file)
                );
            }
        }
        $scandir = $templatePath.DIRECTORY_SEPARATOR.'views';
        $files=scandir($scandir);
        foreach($files as $file) {
            if(is_file($scandir.DIRECTORY_SEPARATOR.$file) && CFileHelper::getExtension($file)==='php')
            {
                $this->files[]=new CCodeFile(
                    $this->getViewPath().DIRECTORY_SEPARATOR.$file,
                    $this->render($scandir.DIRECTORY_SEPARATOR.$file)
                );
            }
        }
        $scandir = $templatePath.DIRECTORY_SEPARATOR.'models';
        $files=scandir($scandir);
        foreach($files as $file) {
            if(is_file($scandir.DIRECTORY_SEPARATOR.$file) && CFileHelper::getExtension($file)==='php')
            {
                $this->files[]=new CCodeFile(
                    $this->getModelPath().DIRECTORY_SEPARATOR.ucfirst($this->controller).ucfirst($file),
                    $this->render($scandir.DIRECTORY_SEPARATOR.$file)
                );
            }
        }
    }

    public function getModelClass()
    {
        return $this->_modelClass;
    }

    public function getControllerClass()
    {
        if(($pos=strrpos($this->controller,'/'))!==false)
            return ucfirst(substr($this->controller,$pos+1)).'Controller';
        else
            return ucfirst($this->controller).'Controller';
    }

    public function getModule()
    {
        if($this->module && ($module=Yii::app()->getModule($this->module))!==null) {
            return $module;
        }
        else if(($pos=strpos($this->controller,'/'))!==false)
        {
            $id=substr($this->controller,0,$pos);
            if(($module=Yii::app()->getModule($id))!==null)
                return $module;
        }
        return Yii::app();
    }

    public function getControllerID()
    {
        if($this->getModule()!==Yii::app() && ($pos=strpos($this->controller,'/'))!==false)
            $id=substr($this->controller,strpos($this->controller,'/')+1);
        else
            $id=$this->controller;
        if(($pos=strrpos($id,'/'))!==false)
            $id[$pos+1]=strtolower($id[$pos+1]);
        else
            $id[0]=strtolower($id[0]);
        return $id;
    }

    public function getUniqueControllerID()
    {
        $id=$this->controller;
        if(($pos=strrpos($id,'/'))!==false)
            $id[$pos+1]=strtolower($id[$pos+1]);
        else
            $id[0]=strtolower($id[0]);
        return $id;
    }

    public function getControllerFile()
    {
        $module=$this->getModule();
        $id=$this->getControllerID();
        if(($pos=strrpos($id,'/'))!==false)
            $id[$pos+1]=strtoupper($id[$pos+1]);
        else
            $id[0]=strtoupper($id[0]);
        return $module->getControllerPath().'/'.$id.'Controller.php';
    }
    public function getControllerPath()
    {
        $module=$this->getModule();
        return $module->getControllerPath();
    }
    public function getViewPath()
    {
        return $this->getModule()->getViewPath().'/'.$this->getControllerID();
    }
    public function getModelPath()
    {
        return dirname($this->getModule()->getViewPath()).'/models';
    }
    public function getTableSchema()
    {
        return $this->_table->tableSchema;
    }
    public function getTable()
    {
        return $this->_table;
    }
    public function generateInputLabel($modelClass,$column)
    {
        return "CHtml::activeLabelEx(\$model,'{$column->name}')";
    }

    public function generateInputField($modelClass,$column)
    {
        if($column->type==='boolean')
            return "CHtml::activeCheckBox(\$model,'{$column->name}')";
        else if(stripos($column->dbType,'text')!==false)
            return "CHtml::activeTextArea(\$model,'{$column->name}',array('rows'=>6, 'cols'=>50))";
        else
        {
            if(preg_match('/^(password|pass|passwd|passcode)$/i',$column->name))
                $inputField='activePasswordField';
            else
                $inputField='activeTextField';

            if($column->type!=='string' || $column->size===null)
                return "CHtml::{$inputField}(\$model,'{$column->name}',array('class'=>'form-control','placeholder'=>''))";
            else
            {
                if(($size=$maxLength=$column->size)>60)
                    $size=60;
                return "CHtml::{$inputField}(\$model,'{$column->name}',array('size'=>$size,'maxlength'=>$maxLength))";
            }
        }
    }

    public function generateActiveLabel($modelClass,$column)
    {
        return "\$form->labelEx(\$model,'{$column->name}')";
    }

    public function generateActiveField($modelClass,$column)
    {
        $model = CActiveRecord::model($modelClass);
        $relationNames = $model->relationNames();
        $relation = isset($relationNames[$column->name])?$relationNames[$column->name]:null;
        $condition = $relation[2]?'\'condition\'=>\''.addcslashes($relation[2],"'").'\'':null;
        if ($relation) {
            return "\$form->autoGrid(\$model, '{$column->name}', array($condition))";
        }
        if ($model->hasColumnOption($column->name)) {
            return "\$form->modelColumnField(\$model,'{$column->name}')";
        }
        if($column->type==='boolean')
            return "\$form->checkBox(\$model,'{$column->name}')";
        else if ($column->dbType === 'date')
            return "\$form->dateInput(\$model,'{$column->name}')";
        else if(stripos($column->dbType,'text')!==false)
            return "\$form->textArea(\$model,'{$column->name}',array('rows'=>6, 'cols'=>50,'class'=>'form-control'))";
        else
        {
            if(preg_match('/^(password|pass|passwd|passcode)$/i',$column->name))
                $inputField='passwordField';
            else
                $inputField='textField';

            if($column->type!=='string' || $column->size===null)
                return "\$form->{$inputField}(\$model,'{$column->name}',array('class'=>'form-control','placeholder'=>''))";
            else
            {
                if(($size=$maxLength=$column->size)>60)
                    $size=60;
                return "\$form->{$inputField}(\$model,'{$column->name}',array('size'=>$size,'maxlength'=>$maxLength,'class'=>'form-control'))";
            }
        }
    }
    public function getMessage($name)
    {
        if (isset($this->messageData[$name]))
            return $this->messageData[$name];
        return $name;
    }
    public function guessNameColumn($columns)
    {
        foreach($columns as $column)
        {
            if(!strcasecmp($column->name,'name'))
                return $column->name;
        }
        foreach($columns as $column)
        {
            if(!strcasecmp($column->name,'title'))
                return $column->name;
        }
        foreach($columns as $column)
        {
            if($column->isPrimaryKey)
                return $column->name;
        }
        return 'id';
    }
}