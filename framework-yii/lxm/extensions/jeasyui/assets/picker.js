/**
 * 示例参考picker.htm
 *
 */
(function($) {
	var idx=0;
	$.fn.picker = function(options) {
		var opts = $.extend({}, $.fn.picker.defaults, options);
		/*
		 * 定义各个元素ID,与初始的id相关(toolbar,search,ok,cancel,picker)
		 */
		var id = $(this).attr('id');
		var id_grid = id+"_grid";
		var id_toolbar = id+"_toolbar";
		var id_search_val = id + "_search_val";
		var id_search_btn = id + "_search_btn";
		var id_ok_btn = id + "_ok_btn";
		var id_cancel_btn = id + "_cancel_btn";
		var id_picker_data = id + "_picker_data";
		/*
		 *定义窗口内容格式，上，中，下3层，上位datagrid,中间picker data 下边是操作按钮
		 */
		var win_content = '';
		win_content += '<table id="'+id_grid+'"></table>';
		win_content += '<div id="'+id_toolbar+'"><div class="picker-toolbar">';
		win_content += '<input id="'+id_search_val+'"/><a id="'+id_search_btn+'" class="easyui-linkbutton" iconCls="icon-search">'+opts.searchText+'</a>';
		win_content += '</div></div>';
		win_content += '<div class="picker-data-border" ><div class="picker-data" id="'+id_picker_data+'"></div></div>';
		win_content += '<div class="picker-action">';
		win_content += '<a class="easyui-linkbutton" iconCls="icon-ok" id="'+id_ok_btn+'">'+opts.okText+'</a>';
		win_content += '<a class="easyui-linkbutton" iconCls="icon-cancel" id="'+id_cancel_btn+'">'+opts.cancelText+'</a>';
		win_content += '</div>';

		/*
		 *定义打开窗口属性，默认是隐藏状态
		 */
		$("#"+id).window({
			width:opts.winWidth,
			height:opts.winHeight,
			closed:opts.winClosed,
			title:opts.winTitle,
			resizable:opts.winResizable,
			collapsible:opts.winCollapsible,
			minimizable:opts.winMinimizable,
			maximizable:opts.winMaximizable,
			closable:opts.winClosable,
			content:win_content,
			modal:true
		});
		/**
		 * 根据调用参数重新格式化datagrid的columns,主要是调整width,使总宽度适合窗口宽度
		 * 增加排序
		 */
		var grid_columns = [{field:'ck',checkbox:true,width:49}];//这个宽度在checkbox列无意义,这里为了兼容其他列的宽度和比总宽度少49像素而设置
		for (var i=0;i<opts.columns.length;i++) {
			var row = opts.columns[i];
			if (!row.hasOwnProperty('field')) {
				continue;
			}
			if (!row.hasOwnProperty('title')) {
				row['title'] = row.field;
			}
			if (!row.hasOwnProperty('width')) {
				row['width'] = parseInt((opts.winWidth - 49)/opts.columns.length);
			}
			if (!row.hasOwnProperty('sortable')) {
				row['sortable'] = true;
			}
			grid_columns.push(row);
		}
		if (grid_columns.length > 1) {
			var n1width=0;
			for (var i=0;i<grid_columns.length-1;i++) {
				n1width += grid_columns[i].width;
			}
			grid_columns[grid_columns.length-1].width = opts.winWidth - n1width;
		}
		/**
		 * datagrid参数设置
		 * checkbox4种操作都触发数据选择更新
		 */
		$("#"+id_grid).datagrid({
			width:opts.winWidth-14,
			height:opts.winHeight-116,
			nowrap:true,
			autoRowHeight: true,
			striped: true,
			collapsible:false,
			remoteSort: false,
			url:opts.url,
			idField:opts.idField,
			singleSelect:opts.single,
			columns:[grid_columns],
			pagination:true,
			toolbar:"#"+id_toolbar,
			onSelect:function(index,row){updatePickerData();},
			onCheck:function(rowIndex,row){updatePickerData();},
			onUncheck:function(rowIndex,row){updatePickerData();},
			onCheckAll:function(rows){updatePickerData();},
			onUncheckAll:function(rows){updatePickerData();},
		});
		/**
		 * ok,cancel,search3个按钮的点击操作
		 */
		$("#"+id_ok_btn).click(function(){
			opts.okcallback.call(null, $("#"+id_grid).datagrid('getSelections'));
			$("#"+id).window("close");
		});
		$("#"+id_cancel_btn).click(function(){
			$("#"+id).window("close");
		});
		$("#"+id_search_btn).click(function(){
			$("#"+id_grid).datagrid({
				queryParams: {
					q: $("#"+id_search_val).val()
				}
			});
			setPageConfig();
		});
		setPageConfig(); //定义分页导航的样式和语言（中文)
		/**
		 * 开放open接口，接收隐藏域ids和显示域names数组参数，
		 * 作为打开窗口后datagrid的初始化数据
		 */
		this.open = function(ids, names) {
			$("#"+id_search_val).val('');
			var rows = $("#"+id_grid).datagrid('getSelections');
			while (rows.length>0) {
				rows.pop();
			}
			for (var i=0;i<ids.length;i++) {
				var row = {};
				row[opts.idField] = ids[i];
				row[opts.nameField] = names[i];
				rows.push(row);
			}
			$("#"+id_grid).datagrid("load", {queryParams:{}});
			$("#"+id).window("open");
		}
		return this; //保证对象能被调用端获取到
		/**
		 *分页配置
		 */
		function setPageConfig()
		{
			var p = $("#"+id_grid).datagrid('getPager');
			$(p).pagination({
				showPageList:false,
				showRefresh:false,
				beforePageText:opts.beforePageText,
				afterPageText:opts.afterPageText,
				displayMsg:opts.displayMsg,
			});
		}
		/**
		 *根据datagrid已选择的数据更新picker data内容
		 */
		function updatePickerData()
		{
			var rows = $("#"+id_grid).datagrid('getSelections');
			var curids = [];
			var addids = [];
			var delids = [];
			$("#"+id_picker_data).find("div").each(function(){
				curids.push($(this).attr('value'));
			});

			for (var i=0;i<rows.length;i++) {
				var eq = false;
				for (var j=0; j<curids.length;j++) {
					if (rows[i][opts.idField] == curids[j]) {
						eq = true;
					}
				}
				if (!eq) {
					addids.push(rows[i]);
				}
			}
			for (var i=0;i<curids.length;i++) {
				var eq = false;
				for (var j=0; j<rows.length;j++) {
					if (rows[j][opts.idField] == curids[i]) {
						eq = true;
					}
				}
				if (!eq) {
					delids.push(curids[i]);
				}
			}
			for (var i=0;i<delids.length;i++) {
				$("#"+id_picker_data).find("div[value="+delids[i]+"]").remove();
			}
			for (var i=0;i<addids.length;i++) {
				var div = $('<div value="'+addids[i][opts.idField]+'"></div>');
				div.width(parseInt(opts.winWidth/5)-4);
				var chkbox = $('<input type="checkbox" checked />');
				/**
				 * picker data中的checkbox点击操作都触发删除该项，并同步datagrid的选择数据
				 * 更新当前datagrid页面
				 */
				chkbox.click(function(){
					var val = $(this).parent().attr("value");
					var currows = $("#"+id_grid).datagrid('getSelections');
					var newrows = [];
					for(var i=0;i<currows.length;i++) {
						if (currows[i][opts.idField] != val) {
							newrows.push(currows[i]);
						}
					}
					while (currows.length>0) {
						currows.pop();
					}
					for (var i=0;i<newrows.length;i++) {
						currows.push(newrows[i]);
					}
					$("#"+id_grid).datagrid("reload");
					$(this).parent().remove();

				});
				div.append(chkbox);
				div.append(addids[i][opts.nameField]);
				$("#"+id_picker_data).append(div);
			}
		}
	};


	$.fn.picker.defaults = {
		winWidth:560,
		winHeight:460,
		winTitle:"数据选择",
		winClosed:true,
		winResizable:false,
		winCollapsible:false,
		winMinimizable:false,
		winMaximizable:false,
		winClosable:false,
		searchText:"查找",
		okText:"确定",
		cancelText:"取消",
		single:false,
		url:"",
		idField:"id",
		nameField:"name",
		columns:[{title:'名称',field:'name'}],
		beforePageText:"页",
		afterPageText:"总{pages}页",
		displayMsg:"显示 {from} - {to} 条  共 {total} 条记录",
		okcallback:function(rows){}
	};

})(jQuery);
