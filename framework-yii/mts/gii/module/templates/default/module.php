<?php echo "<?php\n"; ?>

class <?php echo $this->moduleClass; ?> extends WebModule
{
    public function init()
    {
        $this->setImport(array(
            '<?php echo $this->moduleID; ?>.dbmodels.*',
            '<?php echo $this->moduleID; ?>.models.*',
            '<?php echo $this->moduleID; ?>.components.*',
        ));
    }

    public function beforeControllerAction($controller, $action)
    {
        if(parent::beforeControllerAction($controller, $action))
        {
            return true;
        }
        else
            return false;
    }
}
