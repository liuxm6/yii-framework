<?php
/**
 * This is the template for generating the model class of a specified table.
 * - $this: the ModelCode object
 * - $tableName: the table name for this class (prefix is already removed if necessary)
 * - $modelClass: the model class name
 * - $columns: list of table columns (name=>CDbColumnSchema)
 * - $labels: list of attribute labels (name=>label)
 * - $rules: list of validation rules
 * - $relations: list of relations (name=>relation declaration)
 */
?>
<?php echo "<?php\n"; ?>

/**
 * 数据表模型类，表名： "<?php echo $tableName; ?>".
 *
 *  字段列表:
<?php foreach($columns as $column): ?>
 * @property <?php echo $column->type.' $'.$column->name."\n"; ?>
<?php endforeach; ?>
<?php if(!empty($relations)): ?>
 *
 * 表间关联:
<?php foreach($relations as $name=>$relation): ?>
 * @property <?php
    if (preg_match("~^array\(self::([^,]+), '([^']+)', '([^']+)'\)$~", $relation, $matches))
    {
        $relationType = $matches[1];
        $relationModel = $matches[2];

        switch($relationType){
            case 'HAS_ONE':
                echo $relationModel.' $'.$name."\n";
            break;
            case 'BELONGS_TO':
                echo $relationModel.' $'.$name."\n";
            break;
            case 'HAS_MANY':
                echo $relationModel.'[] $'.$name."\n";
            break;
            case 'MANY_MANY':
                echo $relationModel.'[] $'.$name."\n";
            break;
            default:
                echo 'mixed $'.$name."\n";
        }
    }
    ?>
<?php endforeach; ?>
<?php endif; ?>
 */
class <?php echo $modelClass; ?> extends <?php echo $this->baseClass."\n"; ?>
{
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
    public function getDbConfigName()
    {
        return '<?php echo $this->connectionID;?>';
    }
    public function getModelPath()
    {
        return '<?php echo $this->modelPath;?>';
    }
    public function tableName()
    {
        return '<?php echo $tableName; ?>';
    }

    public function rules()
    {
        return array(
<?php foreach($rules as $rule): ?>
            <?php echo $rule.",\n"; ?>
<?php endforeach; ?>
            array('<?php echo implode(', ', array_keys($columns)); ?>', 'safe', 'on'=>'search'),
        );
    }

    public function relations()
    {
        return array(
<?php foreach($relations as $name=>$relation): ?>
            <?php echo "'$name' => $relation,\n"; ?>
<?php endforeach; ?>
        );
    }


    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return <?php
            $labelstr = var_export($labeldata,true);
            $labelstr = str_replace(')', '        )', $labelstr);
            echo str_replace('  ', '            ', $labelstr);
        ?>;
    }

    public function behaviors()
    {
        return $this->getFileData(dirname(__FILE__).'/<?php echo $behaviorPrefix.$userModelClass ?>.php');
    }
    public function relationNames()
    {
        return array(
<?php foreach($relationNames as $name=>$relationName): ?>
            <?php echo "'$name' => {$relationName[0]},\n"; ?>
<?php endforeach; ?>
        );
    }
    /**
     * @return CActiveDataProvider
     */
    public function search($params=array())
    {
        $pageSize = isset($params['pagesize'])?$params['pagesize']:(isset(Yii::app()->params['pageSize'])&& Yii::app()->params['pageSize']>0?Yii::app()->params['pageSize']:20);
        unset($params['pagesize']);
        $criteria=new CDbCriteria;
        foreach ($params as $k=>$v) {
            $criteria->{$k} = $v;
        }

<?php
$tablealias="";
if (!empty($relationNames)) {
    echo "        \$criteria->with = array(\n";
    foreach ($relationNames as $name=>$val) {
        echo "            '".$val[1][0]."',\n";
    }
    echo "        );\n";
    $tablealias = "t.";
}
foreach($columns as $name=>$column)
{
    if (isset($relationNames[$name]) && $relationNames[$name]) {
        $rname = $relationNames[$name][1][0].".".$relationNames[$name][1][1];
        echo "        \$criteria->compare('$rname',\$this->$name,true);\n";
        continue;
    }
    else {
        $rname = $tablealias.$name;
    }
    if($column->type==='string')
    {
        echo "        \$criteria->compare('$rname',\$this->$name,true);\n";
    }
    else
    {
        echo "        \$criteria->compare('$rname',\$this->$name);\n";
    }
}
?>

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
            'pagination'=>array(
                'pageSize'=>$pageSize
            )
        ));
    }
    protected function getFileData($file)
    {
        if(is_file($file))
            return require($file);
        else
            return array();
    }
}