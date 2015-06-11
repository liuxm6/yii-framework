<?php echo "<?php\n"; ?>
/**
 * 搜索模型
 */
class <?php echo ucfirst($this->controller); ?>SearchModel extends FormModel
{
<?php
    foreach ($this->searchData as $column=>$one):
?>
    public $<?php echo $column;?>;
<?php
    endforeach;
?>

    public function attributeLabels()
    {
        return array(
<?php
    foreach ($this->searchData as $column=>$one):
?>
            '<?php echo $column;?>'=>'<?php echo $one[1];?>',
<?php
    endforeach;
?>
        );
    }
    public function getCriteria($alias='t')
    {
        $criteria = new CDbCriteria;
<?php
    $withs = array();
    foreach ($this->searchData as $column=>$one):
        $compare = "'".$one[2]."'";
        if (strpos($one[2],'.') === false && $this->searchUseRelation) {
            $compare = '$alias.\'.'.$one[2]."'";
        }
        $m = $this->table->tableSchema->columns[$column]->type=='string'; //类型是字符时显示true
        if (strpos($one[2],'.') !== false) {
            $m = true;
            $withs[] = substr($one[2], 0, strpos($one[2],'.'));
        }
?>
        $criteria->compare(<?php echo $compare;?>, $this-><?php echo $column;?><?php echo $m?', true':'';?>);
<?php
    endforeach;
    if (!empty($withs)) {
        $withstr = implode(",", $withs);
        echo '        $criteria->with=array("'.$withstr.'");';
    }
?>

        return $criteria;
    }
}