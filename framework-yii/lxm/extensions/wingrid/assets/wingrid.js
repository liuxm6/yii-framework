(function($) {
    $.fn.wingrid = function(options) {
        var opts = $.extend({}, $.fn.wingrid.defaults, options);
        var id = opts.id;
        var showid = opts.showid;
        var id_win = id+"_win";
        var id_grid = id+"_grid";
        var id_toolbar = id+"_toolbar";
        var id_search_val = id + "_search_val";
        var id_search_btn = id + "_search_btn";
        var id_ok_btn = id + "_ok_btn";
        var id_remove_btn = id + "_remove_btn";
        var id_cancel_btn = id + "_cancel_btn";
        /**
         * 根据调用参数重新格式化datagrid的columns,主要是调整width,使总宽度适合窗口宽度
         * 增加排序
         */

        var grid_columns = [{field:'ck',checkbox:true,width:49}];//这个宽度在checkbox列无意义,这里为了兼容其他列的宽度和比总宽度少49像素而设置
        var gridwidth = opts.width-46;
        var gridcolwidth = parseInt(gridwidth/opts.columns.length)
        if (!opts.toolbar && opts.search) {
            gridcolwidth = parseInt((gridwidth-80)/opts.columns.length)
        }
        for (var i=0;i<opts.columns.length;i++) {
            var row = opts.columns[i];
            if (!row.hasOwnProperty('field')) {
                continue;
            }
            if (!row.hasOwnProperty('title')) {
                row['title'] = row.field;
            }
            if (!row.hasOwnProperty('width')) {
                row['width'] = gridcolwidth;
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
            grid_columns[grid_columns.length-1].width = opts.width - n1width + 6 - grid_columns.length; //修正水平卷动
        }
        if (!opts.toolbar && opts.search) {
            var search_content = '<div class="search-content"><div style="width:26px;">&nbsp;</div>';
            for (var i=1;i<grid_columns.length-1;i++) {
                search_content += '<div style="width:'+(grid_columns[i].width)+'px"><input class="search-input" style="width:'+(grid_columns[i].width-15)+'px" id="'+id+'_s_'+grid_columns[i].field+'" /></div>';
            }
            search_content += '<div><input class="search-input" style="width:'+(grid_columns[grid_columns.length-1].width-88)+'px" id="'+id+'_s_'+grid_columns[grid_columns.length-1].field+'" /><a id="'+id_search_btn+'" class="easyui-linkbutton" iconCls="icon-search">'+opts.searchText+'</a></div>';
            search_content += '</div>';
            opts.toolbar = search_content;
        }


        if ($("#"+id_win).length == 0) {
            var win_content = '';
            if (opts.toolbar) {
                win_content += '<div id="'+id_toolbar+'" class="wingrid-toolbar" grid="'+id_grid+'">'+opts.toolbar+'</div>';
            }
            win_content += '<div class="wingrid-grid"><table id="'+id_grid+'"></table></div>';
            win_content += '<div class="wingrid-action"><a class="easyui-linkbutton" iconCls="icon-ok" id="'+id_ok_btn+'">'+opts.okText+'</a>';
            win_content += '<a class="easyui-linkbutton" iconCls="icon-remove" id="'+id_remove_btn+'">'+opts.removeText+'</a>';
            win_content += '<a class="easyui-linkbutton" iconCls="icon-cancel" id="'+id_cancel_btn+'">'+opts.cancelText+'</a></div>';
            $("body").append('<div id="'+id_win+'"/>');
            $("#"+id_win).window({
                height:opts.toolbar?435:405,
                width:opts.width,
                closed:true,
                title:opts.title,
                resizable:false,
                collapsible:false,
                minimizable:false,
                maximizable:false,
                closable:true,
                content:win_content,
                modal:true
            });

            $("#"+id_grid).datagrid({
                width:opts.width-14,
                height:opts.toolbar?365:335,
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
                toolbar:"#"+id_toolbar
            });
            var ids = $("#"+id).val().split(",");
            var vals = $("#"+showid).val().split(",");
            var rows = $("#"+id_grid).datagrid('getSelections');
            for (var i=0;i<ids.length;i++) {
                var row = {};
                row[opts.idField] = ids[i];
                row[opts.textField] = vals[i];
                rows.push(row);
            }
            $("#"+id_ok_btn).click(function(){
                if (opts.callback)
                    opts.callback.call(null, $("#"+id_grid).datagrid('getSelections'));
                $("#"+id_win).window("close");
            });
            $("#"+id_remove_btn).click(function(){
                $("#"+id_grid).datagrid('clearSelections');
                $("#"+id_ok_btn).click();
            });
            $("#"+id_cancel_btn).click(function(){
                $("#"+id_win).window("close");
            });
            $("#"+id_search_btn).click(function(){
                var params = {};
                for (var i=0;i<opts.columns.length;i++) {
                    params[opts.columns[i]['field']] = $("#"+id+"_s_"+opts.columns[i]['field']).val();
                }
                $("#"+id_grid).datagrid({
                    queryParams: params
                });
            });
            $("input.search-input").keydown(function(event){
                if (event.keyCode == 13) {
                    $("#"+id_search_btn).click();
                    return false;
                }
            });
            setPageConfig();
        }
        $(this).focus(function(){
            $("#"+id_win).window("open");
            $(this).blur();
        });
        this.queryParams = function(params) {
            $("#"+id_grid).datagrid({
                queryParams: params
            });
        };
        this.reload = function(params) {
        	$("#"+id_grid).datagrid('load', params);
        }
        return this;
        function setPageConfig()
        {
            var p = $("#"+id_grid).datagrid('getPager');
            $(p).pagination({
                showPageList:false,
                showRefresh:false,
                beforePageText:opts.beforePageText,
                afterPageText:opts.afterPageText,
                displayMsg:opts.displayMsg
            });
        }
    };

    $.fn.wingrid.defaults = {
        width:600,
        title:"数据选择",
        toolbar:'',
        search:false,
        okText:"确定",
        cancelText:"取消",
        removeText:"清除",
        searchText:"查询",
        single:false,
        url:"",
        idField:"id",
        beforePageText: '页 ',
        afterPageText:"总{pages}页 ",
        displayMsg:"显示 {from} - {to} 条  共 {total} 条记录 ",
        callback:function(rows){},
        columns:[]
    };
})(jQuery);