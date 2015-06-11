<?php
/**
 * 选择生成器
 */
class SelectCode extends CCodeModel
{
    public $model;
    public $with;
    public $controllerString;
    public $controller;
    public $module;
    public $idColumn = 'id';
    public $nameColumn = 'name';
    public $listAttributes;
    private $_modelClass;
    private $_table;
    public $listData=array();

    public function rules()
    {
        return array_merge(parent::rules(), array(
            array('model,controllerString,listAttributes', 'filter', 'filter'=>'trim'),
            array('model,controllerString', 'required'),
            array('controllerString', 'match', 'pattern'=>'/^\w+[\/\w+\\.]*$/', 'message'=>'{attribute} should only contain word characters and dots.'),
            array('controllerString', 'validateController'),
            array('model', 'validateModel'),
            array('listAttributes', 'validList'),
            array('idColumn,nameColumn,with', 'sticky'),
        ));
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), array(
            'model'     =>'Database Model',
            'controllerString'=>'Select Controller',
        ));
    }

    public function requiredTemplates()
    {
        return array(
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
    public function validList($attribute,$params)
    {
        if ($this->_table) {
            if (!isset($this->_table->tableSchema->columns[$this->idColumn])) {
                $this->addError('idColumn', 'id column '.$this->idColumn.' not exists');
            }
            if (!isset($this->_table->tableSchema->columns[$this->nameColumn])) {
                $this->addError('nameColumn', 'name column '.$this->nameColumn.' not exists');
            }
            if (!empty($this->listAttributes)) {
                $list = explode("\n", $this->listAttributes);
                $withs = array();
                foreach ($list as $i=>$line) {
                    $line = trim($line);
                    if (empty($line)) continue;
                    list($column, $name, $relationAttribute,$placeholder) = explode(",", $line);
                    if ($column == $this->idColumn || $column == $this->nameColumn || $column == 'page' || $column=='pageSize') {
                        $this->addError('listAttributes', 'list key can not is id or name column');
                    }
                    if (empty($relationAttribute)) $relationAttribute = $column;
                    if (empty($name)) {
                        list($name) = preg_split("/[;,\s]+/", trim($this->_table->tableSchema->columns[$column]->comment));
                        if (empty($name)) $name = strtoupper($column);
                    }
                    $this->listData[$column] = array($column, $name, $relationAttribute,$placeholder);
                    if (strpos($relationAttribute,'.') !== false) {
                        $withs[] = substr($relationAttribute,0,strpos($relationAttribute,'.'));
                    }
                }
                $this->with = $withs;
            }
        }
    }
    public function validateController($attribute,$params)
    {
        $this->controllerString = rtrim($this->controllerString,'/');
        if (strpos($this->controllerString,'/') !== false) {
            list($this->module, $this->controller) = explode('/', $this->controllerString);
        }
        else {
            $this->module = '';
            $this->controller = $this->controllerString;
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
}