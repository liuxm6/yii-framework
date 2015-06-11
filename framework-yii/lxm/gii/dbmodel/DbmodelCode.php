<?php

class DbmodelCode extends CCodeModel
{
    public $tablePrefix;
    public $tableName;
    public $modelClass;
    public $connectionID = 'db';
    public $modelPath='application.dbmodels';
    public $baseClass='ActiveRecord';
    public $buildRelations=true;

    protected $db;
    protected $labels;
    protected $relations;
    protected $relationNames = array();
    protected $labelPrefix = 'Label_';
    protected $behaviorPrefix = 'Behavior_';

    public function rules()
    {
        return array_merge(parent::rules(), array(
            array('tablePrefix, connectionID, baseClass, tableName, modelClass, modelPath', 'filter', 'filter'=>'trim'),
            array('tableName, modelPath, baseClass', 'required'),
            //array('tablePrefix, tableName, modelPath', 'match', 'pattern'=>'/^(\w+[\w\.]*|\*?|\w+\.\*)$/', 'message'=>'{attribute} should only contain word characters, dots, and an optional ending asterisk.'),
            array('tableName', 'validateTableName', 'skipOnError'=>true),
            array('tablePrefix, modelClass, baseClass', 'match', 'pattern'=>'/^[a-zA-Z_]\w*$/', 'message'=>'{attribute} should only contain word characters.'),
            array('modelPath', 'validateModelPath', 'skipOnError'=>true),
            array('baseClass, modelClass', 'validateReservedWord', 'skipOnError'=>true),
            array('baseClass', 'validateBaseClass', 'skipOnError'=>true),
            array('tablePrefix, connectionID, modelPath, baseClass, buildRelations', 'sticky'),
            array('connectionID', 'checkConnection'),
        ));
    }
    public function checkConnection($attribute,$params)
    {
        if (!isset(Yii::app()->{$this->$attribute}) || !(Yii::app()->{$this->$attribute} instanceof CDbConnection))
            $this->addError('connectionID','DB Name is required to run this generator');
    }
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), array(
            'connectionID'=>'DB Name',
            'tablePrefix'=>'Table Prefix',
            'tableName'=>'Table Name',
            'modelPath'=>'Model Path',
            'modelClass'=>'Model Class',
            'baseClass'=>'Base Class',
            'buildRelations'=>'Build Relations',
        ));
    }

    public function requiredTemplates()
    {
        return array(
            'model.php',
        );
    }

    public function init()
    {
        if(!isset(Yii::app()->{$this->connectionID}) || !(Yii::app()->{$this->connectionID} instanceof CDbConnection))
            throw new CHttpException(500,'An active "db" connection is required to run this generator.');
        $this->db = Yii::app()->{$this->connectionID};
        $this->tablePrefix=$this->db->tablePrefix;
        parent::init();
    }

    public function prepare()
    {
        $this->db = Yii::app()->{$this->connectionID};
        //$loaddata = $this->loadFromFile(Yii::getPathOfAlias($this->modelPath).DIRECTORY_SEPARATOR."tables".DIRECTORY_SEPARATOR.$this->dataFile);
        if(($pos=strrpos($this->tableName,'.'))!==false)
        {
            $schema=substr($this->tableName,0,$pos);
            $tableName=substr($this->tableName,$pos+1);
        }
        else
        {
            $schema='';
            $tableName=$this->tableName;
        }
        if($tableName[strlen($tableName)-1]==='*')
        {
            $tables=$this->db->schema->getTables($schema);
            if($this->tablePrefix!='')
            {
                foreach($tables as $i=>$table)
                {
                    if(strpos($table->name,$this->tablePrefix.(strlen($tableName)>1?substr($tableName,0,-1):""))!==0)
                        unset($tables[$i]);
                }
            }
            else {

                foreach($tables as $i=>$table) {
                    $s = strlen($tableName)>1?substr($tableName,0,-1):"";
                    if($s && strpos($table->name, $s)!==0)
                        unset($tables[$i]);
                }
            }
        }
        else
            $tables=array($this->getTableSchema($this->tableName));

        $this->files=array();
        $templatePath=$this->templatePath;
        $this->buildFromRelation();
        $this->buildFromComment();

        foreach($tables as $table)
        {
            $tableName=$this->removePrefix($table->name);
            $className=$this->generateClassName($table->name);
            $params=array(
                'tableName'=>$schema==='' ? $tableName : $schema.'.'.$tableName,
                'modelClass'=>$className,
                'userModelClass'=>$className,
                'columns'=>$table->columns,
                'rules'=>$this->generateRules($table),
                'relations'=>isset($this->relations[$className])?$this->relations[$className]:array(),
                'relationNames'=>isset($this->relationNames[$className])?$this->relationNames[$className]:array(),
                'labelPrefix'=>$this->labelPrefix,
                'behaviorPrefix'=>$this->behaviorPrefix,
                'labeldata'=>$this->labels[$className],
            );
            /*
            if (!is_dir(Yii::getPathOfAlias($this->modelPath).'/tables/'))
                @mkdir(Yii::getPathOfAlias($this->modelPath).'/tables/', 0700, true);
            $this->files[]=new CCodeFile(
                Yii::getPathOfAlias($this->modelPath).'/tables/T_'.$className.'.php',
                $this->render($templatePath.'/table.php', $params)
            );
            */
            /*
            $this->files[]=new CCodeFile(
                Yii::getPathOfAlias($this->modelPath).'/tables/'.$this->labelPrefix.$className.'.php',
                $this->getFileContent($this->labels[$className])
            );*/

            $this->files[]=new CCodeFile(
                Yii::getPathOfAlias($this->modelPath).'/'.$className.'.php',
                $this->render($templatePath.'/table.php', $params)
            );
        }
    }

    public function validateTableName($attribute,$params)
    {
        $invalidTables=array();
        $invalidColumns=array();

        if($this->tableName[strlen($this->tableName)-1]==='*')
        {
            if(($pos=strrpos($this->tableName,'.'))!==false)
                $schema=substr($this->tableName,0,$pos);
            else
                $schema='';

            $this->modelClass='';
            $tables=$this->db->schema->getTables($schema);
            foreach($tables as $table)
            {
                if($this->tablePrefix=='' || strpos($table->name,$this->tablePrefix)===0)
                {
                    if(in_array(strtolower($table->name),self::$keywords))
                        $invalidTables[]=$table->name;
                    if(($invalidColumn=$this->checkColumns($table))!==null)
                        $invalidColumns[]=$invalidColumn;
                }
            }
        }
        else
        {
            if(($table=$this->getTableSchema($this->tableName))===null)
                $this->addError('tableName',"Table '{$this->tableName}' does not exist.");
            if($this->modelClass==='')
                $this->addError('modelClass','Model Class cannot be blank.');

            if(!$this->hasErrors($attribute) && ($invalidColumn=$this->checkColumns($table))!==null)
                    $invalidColumns[]=$invalidColumn;
        }

        if($invalidTables!=array())
            $this->addError('tableName', 'Model class cannot take a reserved PHP keyword! Table name: '.implode(', ', $invalidTables).".");
        if($invalidColumns!=array())
            $this->addError('tableName', 'Column names that does not follow PHP variable naming convention: '.implode(', ', $invalidColumns).".");
    }

    /*
     * Check that all database field names conform to PHP variable naming rules
     * For example mysql allows field name like "2011aa", but PHP does not allow variable like "$model->2011aa"
     * @param CDbTableSchema $table the table schema object
     * @return string the invalid table column name. Null if no error.
     */
    public function checkColumns($table)
    {
        foreach($table->columns as $column)
        {
            if(!preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/',$column->name))
                return $table->name.'.'.$column->name;
        }
    }

    public function validateModelPath($attribute,$params)
    {
        if(Yii::getPathOfAlias($this->modelPath)===false)
            $this->addError('modelPath','Model Path must be a valid path alias.');
    }

    public function validateBaseClass($attribute,$params)
    {
        $class=Yii::import($this->baseClass,true);
        if(!is_string($class) || !$this->classExists($class))
            $this->addError('baseClass', "Class '{$this->baseClass}' does not exist or has syntax error.");
        else if($class!=='CActiveRecord' && !is_subclass_of($class,'CActiveRecord'))
            $this->addError('baseClass', "'{$this->model}' must extend from CActiveRecord.");
    }

    public function getTableSchema($tableName)
    {
        return $this->db->getSchema()->getTable($tableName);
    }
    public function buildFromRelation()
    {
        $relations = $this->generateRelations();
        $this->relations = $relations;
        return $this->relations;
    }
    public function buildFromComment()
    {
        foreach($this->db->schema->getTables() as $table) {
            $tableName=$table->name;
            $className = $this->generateClassName($tableName);
            foreach($table->columns as $column)
            {
                $label = null;
                $relation = null;
                if (isset($column->comment) && !empty($column->comment)) {
                    $list = preg_split("/[;,\s]+/", trim($column->comment));
                    $label = $list[0];
                    $ok = preg_match("/[^\[]*\[([^\]]*)\].*/", $column->comment, $matches);
                    if ($ok) {
                        $relation = $matches[1];
                    }
                }
                else {
                    $label=ucwords(trim(strtolower(str_replace(array('-','_'),' ',preg_replace('/(?<![A-Z])[A-Z]/', ' \0', $column->name)))));
                }
                $label=preg_replace('/\s+/',' ',$label);
                if(strcasecmp(substr($label,-3),' id')===0)
                    $label=substr($label,0,-3);
                if($label==='Id')
                    $label='ID';
                $this->labels[$className][$column->name]=$label;
                if ($relation) {
                    $list = $this->str_split($relation);
                    $fkName = $column->name;
                    $refClassName = $this->generateClassName($list[0]);
                    $relcondition = isset($list[2])&&!empty($list[2])?",'".addcslashes ($list[2],"'")."'":'';
                    $relationName = $this->generateRelationName($tableName, $fkName, false);
                    $this->relationNames[$className][$fkName][0] = "array('$relationName', '".$list[1]."'$relcondition)";
                    $this->relationNames[$className][$fkName][1] = array($relationName, $list[1]);
                    $this->relations[$className][$relationName] = "array(self::BELONGS_TO, '$refClassName', '$fkName')";
                }
            }
        }
        ksort($this->relationNames);
        ksort($this->relations);
        ksort($this->labels);
    }
    /**
     * 使用自定义分割字符串数组，过滤掉引号，括号内的分隔符号
     */
    public function str_split($str, $ch=',')
    {
        $list = array();
        $stack = array();
        $start=0;
        $len=strlen($str);
        for ($i=0;$i<$len;$i++) {
            if ($str[$i] == $ch) {
                if (empty($stack)) {
                    $list[] = substr($str, $start, $i-$start);
                    $start = $i+1;
                }
            }
            else if (in_array($str[$i], array('(',')','\'','"'))) {
                $count = count($stack);
                if ($count > 0) {
                    $lastch = $stack[$count-1];
                    if ($str[$i] == $lastch || ($lastch=='(' && $str[$i]==')')) {
                        array_pop($stack);
                    }
                    else if ($str[$i] != ')') {
                        $stack[] = $str[$i];
                    }
                }
                else {
                    if ($str[$i] != ')') {
                        $stack[] = $str[$i];
                    }
                }
            }
        }
        $list[] = substr($str, $start, $len-$start);
        return $list;
    }
    public function generateRules($table)
    {
        $rules=array();
        $required=array();
        $integers=array();
        $numerical=array();
        $length=array();
        $safe=array();
        foreach($table->columns as $column)
        {
            if($column->autoIncrement)
                continue;
            $r=!$column->allowNull && $column->defaultValue===null;
            if($r)
                $required[]=$column->name;
            if($column->type==='integer')
                $integers[]=$column->name;
            else if($column->type==='double')
                $numerical[]=$column->name;
            else if($column->type==='string' && $column->size>0)
                $length[$column->size][]=$column->name;
            else if(!$column->isPrimaryKey && !$r)
                $safe[]=$column->name;
        }
        if($required!==array())
            $rules[]="array('".implode(', ',$required)."', 'required')";
        if($integers!==array())
            $rules[]="array('".implode(', ',$integers)."', 'numerical', 'integerOnly'=>true)";
        if($numerical!==array())
            $rules[]="array('".implode(', ',$numerical)."', 'numerical')";
        if($length!==array())
        {
            foreach($length as $len=>$cols)
                $rules[]="array('".implode(', ',$cols)."', 'length', 'max'=>$len)";
        }
        if($safe!==array())
            $rules[]="array('".implode(', ',$safe)."', 'safe')";

        return $rules;
    }

    protected function removePrefix($tableName,$addBrackets=true)
    {
        if($addBrackets && $this->db->tablePrefix=='')
            return $tableName;
        $prefix=$this->tablePrefix!='' ? $this->tablePrefix : $this->db->tablePrefix;
        if($prefix!='')
        {
            if($addBrackets && $this->db->tablePrefix!='')
            {
                $prefix=$this->db->tablePrefix;
                $lb='{{';
                $rb='}}';
            }
            else
                $lb=$rb='';
            if(($pos=strrpos($tableName,'.'))!==false)
            {
                $schema=substr($tableName,0,$pos);
                $name=substr($tableName,$pos+1);
                if(strpos($name,$prefix)===0)
                    return $schema.'.'.$lb.substr($name,strlen($prefix)).$rb;
            }
            else if(strpos($tableName,$prefix)===0)
                return $lb.substr($tableName,strlen($prefix)).$rb;
        }
        return $tableName;
    }

    protected function generateRelations()
    {
        if(!$this->buildRelations)
            return array();
        $relations=array();
        foreach($this->db->schema->getTables() as $table)
        {
            if($this->tablePrefix!='' && strpos($table->name,$this->tablePrefix)!==0)
                continue;
            $tableName=$table->name;

            if ($this->isRelationTable($table))
            {
                $pks=$table->primaryKey;
                $fks=$table->foreignKeys;

                $table0=$fks[$pks[0]][0];
                $table1=$fks[$pks[1]][0];
                $className0=$this->generateClassName($table0);
                $className1=$this->generateClassName($table1);

                $unprefixedTableName=$this->removePrefix($tableName);

                $relationName=$this->generateRelationName($table0, $table1, true);
                $relations[$className0][$relationName]="array(self::MANY_MANY, '$className1', '$unprefixedTableName($pks[0], $pks[1])')";

                $relationName=$this->generateRelationName($table1, $table0, true);
                $relations[$className1][$relationName]="array(self::MANY_MANY, '$className0', '$unprefixedTableName($pks[1], $pks[0])')";
            }
            else {
                $className=$this->generateClassName($tableName);
                foreach ($table->foreignKeys as $fkName => $fkEntry) {
                    // Put table and key name in variables for easier reading
                    $refTable=$fkEntry[0]; // Table name that current fk references to
                    $refKey=$fkEntry[1];   // Key in that table being referenced
                    $refClassName=$this->generateClassName($refTable);

                    // Add relation for this table
                    $relationName=$this->generateRelationName($tableName, $fkName, false);
                    $relations[$className][$relationName]="array(self::BELONGS_TO, '$refClassName', '$fkName')";
                    $this->relationNames[$className][$fkName][0] = "array('$relationName', '".$refKey."'$relcondition)";
                    $this->relationNames[$className][$fkName][1] = array($relationName, $refKey);


                    // Add relation for the referenced table
                    $relationType=$table->primaryKey === $fkName ? 'HAS_ONE' : 'HAS_MANY';
                    $relationName=$this->generateRelationName($refTable, $this->removePrefix($tableName,false), $relationType==='HAS_MANY');
                    $i=1;
                    $rawName=$relationName;
                    while(isset($relations[$refClassName][$relationName]))
                        $relationName=$rawName.($i++);
                    $relations[$refClassName][$relationName]="array(self::$relationType, '$className', '$fkName')";
                }
            }
        }
        return $relations;
    }

    /**
     * Checks if the given table is a "many to many" pivot table.
     * Their PK has 2 fields, and both of those fields are also FK to other separate tables.
     * @param CDbTableSchema table to inspect
     * @return boolean true if table matches description of helpter table.
     */
    protected function isRelationTable($table)
    {
        $pk=$table->primaryKey;
        return (count($pk) === 2 // we want 2 columns
            && isset($table->foreignKeys[$pk[0]]) // pk column 1 is also a foreign key
            && isset($table->foreignKeys[$pk[1]]) // pk column 2 is also a foriegn key
            && $table->foreignKeys[$pk[0]][0] !== $table->foreignKeys[$pk[1]][0]); // and the foreign keys point different tables
    }

    protected function generateClassName($tableName)
    {
        if($this->tableName===$tableName || ($pos=strrpos($this->tableName,'.'))!==false && substr($this->tableName,$pos+1)===$tableName)
            return $this->modelClass;

        $tableName=$this->removePrefix($tableName,false);
        $className='';
        foreach(explode('_',$tableName) as $name)
        {
            if($name!=='')
                $className.=ucfirst($name);
        }
        return $className;
    }

    /**
     * Generate a name for use as a relation name (inside relations() function in a model).
     * @param string the name of the table to hold the relation
     * @param string the foreign key name
     * @param boolean whether the relation would contain multiple objects
     * @return string the relation name
     */
    protected function generateRelationName($tableName, $fkName, $multiple)
    {
        if(strcasecmp(substr($fkName,-2),'id')===0 && strcasecmp($fkName,'id'))
            $relationName=rtrim(substr($fkName, 0, -2),'_');
        else
            $relationName=$fkName;
        $relationName[0]=strtolower($relationName);

        if($multiple)
            $relationName=$this->pluralize($relationName);

        $names=preg_split('/_+/',$relationName,-1,PREG_SPLIT_NO_EMPTY);
        if(empty($names)) return $relationName;  // unlikely
        for($name=$names[0], $i=1;$i<count($names);++$i)
            $name.=ucfirst($names[$i]);

        $rawName=$name;
        $table=$this->db->schema->getTable($tableName);
        $i=0;
        while(isset($table->columns[$name]))
            $name=$rawName.($i++);

        return $name;
    }
    protected function loadFromFile($file)
    {
        if(is_file($file))
            return require($file);
        else
            return array();
    }

    protected function getFileContent($data)
    {
        return "<?php\nreturn ".var_export($data,true).";\n";
    }
}