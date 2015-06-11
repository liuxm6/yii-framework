<?php
$q = $_REQUEST['q'];
if (!empty($q))
	$total = 100;
else
	$total = 239;
$page = $_REQUEST['page'];
$id = ($page-1) * 10 + 1;
?>
{                                                      
	"total":<?php echo $total?>,                                                      
	"rows":[                                                          
		{"code":"<?php echo sprintf("%03d", $id++); ?>","name":"Name 1","addr":"Address 11","col4":"col4 data"},         
		{"code":"<?php echo sprintf("%03d", $id++); ?>","name":"Name 2","addr":"Address 13","col4":"col4 data"},         
		{"code":"<?php echo sprintf("%03d", $id++); ?>","name":"Name 3","addr":"Address 87","col4":"col4 data"},         
		{"code":"<?php echo sprintf("%03d", $id++); ?>","name":"Name 4","addr":"Address 63","col4":"col4 data"},         
		{"code":"<?php echo sprintf("%03d", $id++); ?>","name":"Name 5","addr":"Address 45","col4":"col4 data"},         
		{"code":"<?php echo sprintf("%03d", $id++); ?>","name":"Name 6","addr":"Address 16","col4":"col4 data"},          
		{"code":"<?php echo sprintf("%03d", $id++); ?>","name":"Name 7","addr":"Address 27","col4":"col4 data"},          
		{"code":"<?php echo sprintf("%03d", $id++); ?>","name":"Name 8","addr":"Address 81","col4":"col4 data"},          
		{"code":"<?php echo sprintf("%03d", $id++); ?>","name":"Name 9","addr":"Address 69","col4":"col4 data"},          
		{"code":"<?php echo sprintf("%03d", $id++); ?>","name":"Name 10","addr":"Address 78","col4":"col4 data"}     
	]                                                          
}                                                           
