<?php
/**
 * CJuiDateTimePicker class file.
 *
 * @author Anatoly Ivanchin <van4in@gmail.com>
 */


class DateTimePicker extends CInputWidget
{
    public $mode='datetime';
    public $options = array();
    public $language = null;
    public $args;

    public function init()
    {
        $this->language=Yii::app()->getLanguage();
        return parent::init();
    }

    public function run()
    {
        list($name,$id)=$this->resolveNameID();

        if(isset($this->htmlOptions['id']))
            $id=$this->htmlOptions['id'];
        else
            $this->htmlOptions['id']=$id;
        if(isset($this->htmlOptions['name']))
            $name=$this->htmlOptions['name'];
        else
            $this->htmlOptions['name']=$name;
        $argstr = $argstr1 = '';
        if (!empty($this->htmlOptions['args'])) {
            foreach ($this->htmlOptions['args'] as $k=>$v)
                $argstr .= $k.":".$v.',';
        }
        unset($this->htmlOptions['args']);
        if ($argstr)
            $argstr1 = substr($argstr,0,-1);
        if ($this->mode == 'datetime')
            $this->htmlOptions['onFocus'] = "WdatePicker({".$argstr."startDate:'%y-%M-01 00:00:00',dateFmt:'yyyy-MM-dd HH:mm:ss'})";
        else
            $this->htmlOptions['onFocus'] = "WdatePicker({".$argstr1."})";
        if($this->hasModel())
            echo CHtml::activeTextField($this->model,$this->attribute,$this->htmlOptions);
        else
            echo CHtml::textField($name,$this->value,$this->htmlOptions);


        if (isset($this->language)){
        }

        $cs = Yii::app()->getClientScript();

        $assets = Yii::app()->getAssetManager()->publish(dirname(__FILE__).DIRECTORY_SEPARATOR.'assets');
        $cs->registerScriptFile($assets.'/WdatePicker.js',CClientScript::POS_END);
    }
}