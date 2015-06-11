<?php echo "<?php\n"; ?>

include_once dirname(__FILE__)."/tables/<?php echo $modelClass; ?>.php";
class <?php echo $userModelClass; ?> extends <?php echo $modelClass."\n"; ?>
{
    /**
     * @return <?php echo $userModelClass."\n"; ?>
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}
