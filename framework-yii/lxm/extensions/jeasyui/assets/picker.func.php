<?php
function easy_select($name, $params=array())
{
	$id = isset($params['id'])? $params['id'] : "id_".$name;
	$winid = $id."_win";
	$hideid = $id."_hide";	
	$textattr = array();
	if (is_array($params['htmloptions'])) {
		foreach ($params['htmloptions'] as $k=>$v) {
			$textattr[] = $k.'="'.addcslashes($v,'"').'"';
		}
	}
	$textattr = implode(" ", $textattr);
	$url = $params['url'];
	$idfield = $params['idfield'];
	$namefield = $params['namefield'];
	$columns = $params['columns'];
	$columndata = array();
	foreach ($columns as $one) {
		$onedata = array();
		foreach ($one as $k=>$v) {
			$onedata[] = $k.':"'.$v.'"';
		}
		$columndata[] = "{".implode(",", $onedata)."}";
	}
	$columnstr = implode(",",$columndata);

	$template = <<<EOD
	<input type=text id="{$id}">
	<input type=hidden id="{$hideid}"  name="{$name}">
	<div id="{$winid}"></div>
	<script>
	\$(function(){
		var o = \$("#{$winid}").picker({
			url:"{$url}",
			idField:"{$idfield}",
			nameField:"{$namefield}",
			single:false,
			columns:[{$columnstr}],
			okcallback:function(rows) {
				var keys = [];
				var vals = [];
				for (var i=0;i<rows.length;i++) {					
					keys.push(rows[i]['{$idfield}']);
					vals.push(rows[i]['{$namefield}']);
				}
				\$("#{$id}").val(vals.join(","));
				\$("#{$hideid}").val(keys.join(","));
			}
		});
		\$("#{$id}").focus(function(){
			var keys = [];
			var vals = [];
			if (\$("#{$id}").val() != "") {				
				vals = \$("#{$id}").val().split(",");
			}
			if (\$("#{$hideid}").val() != "") {
				keys = \$("#{$hideid}").val().split(",");
			}
			o.open(keys,vals);
		});
	});
	</script>
EOD;
	return $template;
}