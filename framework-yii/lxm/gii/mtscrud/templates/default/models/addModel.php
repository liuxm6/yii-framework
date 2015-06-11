<?php echo "<?php\n"; ?>
/**
 * 添加模型
 */
class <?php echo ucfirst($this->controller); ?>AddModel extends FormModel
{
<?php
    foreach ($this->editData as $column=>$one):
?>
    public $<?php echo $column;?>;
<?php
    endforeach;
?>
    public function attributeLabels()
    {
        return array(
    <?php
        foreach ($this->editData as $column=>$one):
    ?>
            '<?php echo $column;?>'=>'<?php echo $one[1];?>',
    <?php
        endforeach;
    ?>
        );
    }
    public function rules()
    {
<?php if($this->editAttributes):?>
        return array(
       <?php foreach ($this->editData as $column=>$one):?>
<?php if($one[4]):?>
<?php $rules = explode("|", $one[4]);?>
<?php foreach($rules as $r):?>
    <?php echo $r.',';?>

<?php endforeach;?>
<?php endif;?>
<?php endforeach;?>
        );
<?php else:?>
        $dbmodel = <?php echo $this->modelClass;?>::model();
        return $dbmodel->rules();
<?php endif;?>
    }
    public function hasError($attribute) {
        $error = $this->getError($attribute);
        return $error !== null;
    }
}