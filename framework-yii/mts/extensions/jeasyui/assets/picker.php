<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>Complex DataGrid - jQuery EasyUI Demo</title>
	<link rel="stylesheet" type="text/css" href="themes/default/easyui.css">
	<link rel="stylesheet" type="text/css" href="themes/icon.css">
	<link rel="stylesheet" type="text/css" href="themes/picker.css">
	<script type="text/javascript" src="jquery-1.8.0.min.js"></script>
	<script type="text/javascript" src="jquery.easyui.min.js"></script>
	<script type="text/javascript" src="picker.js"></script>

</head>
<body>
	<?php
	include "picker.func.php";
	 echo easy_select('user_select', array(
	 	'url'=>'demo/datagrid_data.json.php',
	 	'idfield'=>'code',
	 	'namefield'=>'name',
	 	'columns'=>array(
	 		array('field'=>'code','title'=>'编号'),
	 		array('field'=>'name','title'=>'名字'),
	 		array('field'=>'addr','title'=>'地址'),
	 	)
	 ));
	 ?>
	
</body>
</html>