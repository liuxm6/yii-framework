<?php

class CrudCode extends CCodeModel
{
    public $model;
    public $controller;
    public $module;
    public $desc;
    public $baseControllerClass='Controller';
    public $hasIndex=true;
    public $hasAdd=true;
    public $hasEdit=true;
    public $hasView=true;
    public $hasDel=true;
    public $hasDelall=false;
    public $hasImport=false;
    public $hasExport=false;
    public $hasSearch=false;
    public $hasCheck=false;

    private $_modelClass;
    private $_table;


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
            array('hasIndex,hasAdd,hasEdit,hasView,hasDel,hasDelall,hasImport,hasExport,hasSearch,hasCheck', 'boolean'),
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
        if(Yii::app()->db===null)
            throw new CHttpException(500,'An active "db" connection is required to run this generator.');
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

    public function prepare()
    {
        $this->files=array();
        $templatePath=$this->templatePath;
        $controllerTemplateFile=$templatePath.DIRECTORY_SEPARATOR.'controller.php';

        $this->files[]=new CCodeFile(
            $this->controllerFile,
            $this->render($controllerTemplateFile)
        );

        $files=scandir($templatePath);
        foreach($files as $file)
        {
            if(is_file($templatePath.DIRECTORY_SEPARATOR.$file) && CFileHelper::getExtension($file)==='php' && $file!=='controller.php')
            {
                if ($this->checkFile($file))
                $this->files[]=new CCodeFile(
                    $this->viewPath.DIRECTORY_SEPARATOR.$file,
                    $this->render($templatePath.DIRECTORY_SEPARATOR.$file)
                );
            }
            else if (is_dir($templatePath.DIRECTORY_SEPARATOR.$file) && $file != '..' && $file != '.') {
                $subfiles = scandir($templatePath.DIRECTORY_SEPARATOR.$file);
                foreach ($subfiles as $subfile) {
                    if(is_file($templatePath.DIRECTORY_SEPARATOR.$file.DIRECTORY_SEPARATOR.$subfile) && CFileHelper::getExtension($subfile)==='php') {
                        if ($this->checkFile($subfile))
                        $this->files[]=new CCodeFile(
                            $this->viewPath.DIRECTORY_SEPARATOR.$file.DIRECTORY_SEPARATOR.$subfile,
                            $this->render($templatePath.DIRECTORY_SEPARATOR.$file.DIRECTORY_SEPARATOR.$subfile)
                        );
                    }
                }
            }
        }
    }
    public function checkFile($file)
    {
        if (in_array($file, array('add-error.php','add-success.php','add.php','nav-add.php')) && $this->hasAdd) return true;
        if (in_array($file, array('edit-error.php','edit-success.php','edit.php','nav-edit.php')) && $this->hasEdit) return true;
        if (in_array($file, array('import-error.php','import-success.php','import.php','nav-import.php')) && $this->hasImport) return true;
        if (in_array($file, array('view.php','nav-view.php')) && $this->hasView) return true;
        if (in_array($file, array('index.php', 'nav-index.php')) && $this->hasIndex) return true;
        if (($this->hasAdd || $this->hasEdit) && $file == 'form.php') return true;
        return false;
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

    public function getViewPath()
    {
        return $this->getModule()->getViewPath().'/'.$this->getControllerID();
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
                return "CHtml::{$inputField}(\$model,'{$column->name}')";
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
            return "\$form->textArea(\$model,'{$column->name}',array('rows'=>6, 'cols'=>50))";
        else
        {
            if(preg_match('/^(password|pass|passwd|passcode)$/i',$column->name))
                $inputField='passwordField';
            else
                $inputField='textField';

            if($column->type!=='string' || $column->size===null)
                return "\$form->{$inputField}(\$model,'{$column->name}')";
            else
            {
                if(($size=$maxLength=$column->size)>60)
                    $size=60;
                return "\$form->{$inputField}(\$model,'{$column->name}',array('size'=>$size,'maxlength'=>$maxLength))";
            }
        }
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