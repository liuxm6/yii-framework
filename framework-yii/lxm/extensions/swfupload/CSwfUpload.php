<?php
class CSwfUpload extends CWidget
{
	public $jsHandlerUrl;
	public $postParams=array();
	public $config=array(
		'use_query_string'=>true,
		'file_size_limit'=>'2 MB',
		'file_types'=>'*.jpg;*.png;*.gif',
		'file_types_description'=>'Image Files',
		'file_upload_limit'=>0,
		'file_queue_error_handler'=>'js:fileQueueError',
		'file_dialog_complete_handler'=>'js:fileDialogComplete',
		'upload_progress_handler'=>'js:uploadProgress',
		'upload_error_handler'=>'js:uploadError',
		'upload_success_handler'=>'js:uploadSuccess',
		'upload_complete_handler'=>'js:uploadComplete',
		'custom_settings'=>array('upload_target'=>'divFileProgressContainer'),
		'button_placeholder_id'=>'swfupload',
		'button_width'=>170,
		'button_height'=>20,
		'button_text'=>'<span class="button" style="border:1px solid red;width:100px">Upload(Max 2 MB)</span>',
		'button_text_style'=>'.button { font-family: "Lucida Sans Unicode", "Lucida Grande", sans-serif; font-size: 11pt; text-align: center; }',
		'button_text_top_padding'=>0,
		'button_text_left_padding'=>0,
		'button_window_mode'=>'js:SWFUpload.WINDOW_MODE.TRANSPARENT',
		'button_cursor'=>'js:SWFUpload.CURSOR.HAND',
	);

	public function run()
	{
		$assets = dirname(__FILE__).'/assets';
		$baseUrl = Yii::app()->assetManager->publish($assets);
		Yii::app()->clientScript->registerScriptFile($baseUrl . '/swfupload.js', CClientScript::POS_HEAD);
		if(isset($this->jsHandlerUrl)) {
			Yii::app()->clientScript->registerScriptFile($this->jsHandlerUrl);
			unset($this->jsHandlerUrl);
		}
		else {
			Yii::app()->clientScript->registerScriptFile($baseUrl . '/handlers.js');
		}
		$postParams = array('PHPSESSID'=>session_id());
		if(isset($this->postParams))
		{
				$postParams = array_merge($postParams, $this->postParams);
		}
		$config = array('post_params'=> $postParams, 'flash_url'=>$baseUrl. '/swfupload.swf');
		$config = array_merge($config, $this->config);
		$config = CJavaScript::encode($config);
		Yii::app()->getClientScript()->registerScript(__CLASS__, "
		var swfu;
			swfu = new SWFUpload($config);
		");
		$button_placeholder_id = $this->config['button_placeholder_id'];
		echo '<span id="'.$button_placeholder_id.'"></span>';
	}

}