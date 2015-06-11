<?php


class Html extends CHtml
{
    public static function activeTextFields($model,$attribute,$htmlOptions=array())
    {
        self::resolveNameID($model,$attribute,$htmlOptions);
        $htmlOptions['name'] = $htmlOptions['name']."[]";
        self::clientChange('change',$htmlOptions);
        return self::activeInputField('text',$model,$attribute,$htmlOptions);
    }
}