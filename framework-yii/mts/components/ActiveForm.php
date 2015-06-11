<?php

class ActiveForm extends CActiveForm
{
    public function error($model,$attribute,$htmlOptions=array(),$enableAjaxValidation=true,$enableClientValidation=true)
    {
        $html = parent::error($model,$attribute,$htmlOptions,$enableAjaxValidation,$enableClientValidation);
        return $html;
    }
    public function fckeditor($model,$attribute,$htmlOptions=array())
    {
        $options = $htmlOptions;
        if (!isset($options['name'])) $options['name'] = $attribute;
        $options['model'] = $model;
        $options['attribute'] = $attribute;
        return $this->widget('mts.extensions.fckeditor.FCKEditorWidget', $options, true);
    }
    public function winGrid($model, $attribute, $options=array(), $htmlOptions=array())
    {
        $opts = array();
        $opts['model'] = $model;
        $opts['attribute'] = $attribute;
        $list = array('relation','single','className','idField','textField','url','toolbar','title','columns','callback','script','condition','width','search','json_url');
        foreach ($list as $l) {
            if (isset($options[$l]))
                $opts[$l] = $options[$l];
        }
        if ($model instanceof ActiveRecord && empty($opts['className'])) {
            $className = $model->getRelationClassNameByAttribute($attribute);
            $relation = $model->getRelationNameByAttribute($attribute);
            $opts['className'] = $className;
            $config = Yii::app()->params['wingrid'];
            if (is_array($config[get_class($model).".".$relation])) {
                $opts = array_merge($config[get_class($model).".".$relation], $opts);
            }
        }

        if (!$opts['className'])
            return $this->textField($model, $attribute, $htmlOptions);
        else {
            $opts['htmlOptions'] = $htmlOptions;
            return $this->widget('mts.extensions.wingrid.WinGrid', $opts, true);
        }
    }
    public function autoGrid($model, $attribute, $options=array(), $htmlOptions=array())
    {
        $opts = array();
        $opts['model'] = $model;
        $opts['attribute'] = $attribute;
        $list = array('relation','single','className','idField','textField','url','toolbar','title','columns','callback','script','condition','width','search','json_url');
        foreach ($list as $l) {
            if (isset($options[$l]))
                $opts[$l] = $options[$l];
        }
        if ($model instanceof ActiveRecord) {
            $modelPath = $model->getModelPath();
            $className = $model->getRelationClassNameByAttribute($attribute);
            $relations = $model->relationNames();
            $opts['className'] = $className;
            $opts['textField'] = $relations[$attribute][1];
            $opts['modelPath'] = $modelPath;
            $opts['htmlOptions'] = $htmlOptions;
            return $this->widget('mts.extensions.wingrid.AutoGrid', $opts, true);
        }
        return $this->textField($model, $attribute, $htmlOptions);

    }
    /**
     * 读取model中 getColumnOptions中定义的显示, 结合gii来生成
     */
    public function modelColumnField($model, $attribute, $htmlOptions=array())
    {
        $opts = $model->getColumnOption($attribute);
        $type = strtolower($opts['type']);
        $html = '';
        switch ($type) {
            case 'radiobuttonlist':
                $data = $opts['data'];

                $html = $this->radioButtonList($model, $attribute, $data, array('separator'=>' '));
                break;
            default:
                $html = $this->textField($model, $attribute, $htmlOptions);
        }
        return $html;
    }
    public function dateInput($model,$attribute,$htmlOptions=array())
    {
        if (!isset($htmlOptions['class'])) {
            $htmlOptions['class'] = 'Wdate';
        }
        else {
            $htmlOptions['class'] .= ' Wdate';
        }
        return $this->widget('mts.extensions.picker.DateTimePicker', array(
            'model'=>$model,
            'attribute'=>$attribute,
            'mode'=>'date',
            'language'=>'zh-cn',
            'htmlOptions'=>$htmlOptions,
        ), true);
    }
    public function resolveName($model, $attribute)
    {
        return CHtml::resolveName($model, $attribute);
    }
    public function datetimeInput($model,$attribute,$htmlOptions=array())
    {
        if (!isset($htmlOptions['class'])) {
            $htmlOptions['class'] = 'Wdate';
        }
        else {
            $htmlOptions['class'] .= ' Wdate';
        }
        return $this->widget('mts.extensions.picker.DateTimePicker', array(
            'model'=>$model,
            'attribute'=>$attribute,
            'mode'=>'datetime',
            'language'=>'zh-cn',
            'htmlOptions'=>$htmlOptions,
        ), true);
    }
    public function checkBoxList($model,$attribute,$data,$htmlOptions=array())
    {
        $htmlOptions['separator'] = ' ';
        return parent::checkBoxList($model,$attribute,$data,$htmlOptions);
    }
}
