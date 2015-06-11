<?php echo "<?php\n"; ?>

Yii::import("application.models.table.<?php echo $modelClass; ?>");
class <?php echo $userModelClass; ?> extends <?php echo $modelClass."\n"; ?>
{
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
