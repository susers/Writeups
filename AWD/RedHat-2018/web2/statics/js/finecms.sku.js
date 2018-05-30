

function dr_change_price(oname, value) {
	value = parseFloat(value);
	arrayValue[oname+"_price"] = value;
}
function dr_change_quantity(oname, value) {
	arrayValue[oname+"_quantity"] = value;
}
function dr_change_sn(oname, value) {
	arrayValue[oname+"_sn"] = value;
}
function dr_select_mall_spec() {
	step.Creat_Table();
}
function dr_select_mall_spec2(obj) {
	$(obj).parent().remove()
	step.Creat_Table();
}
// 删除自定义组
function dr_spec_select_group(obj, id) {
	$(obj).parent().parent().parent().remove();
	step.Creat_Table();
}
// 更新操作
function dr_update_spec_value(obj) {
	var v = $(obj).val();
	if (!v) {
		dr_tips("名称未填写");
		return;
	}
	var pobj = $(obj).parent().parent().children(".dr_update_spec_value");
	//pobj.val(v);
	pobj.attr("vname", v);
	step.Creat_Table();
}
function dr_spec_edit_group(id) {
	$("#dr_spec_show_name_"+id).hide();
	$("#dr_spec_edit_name_"+id).show();
}
function dr_spec_save_group(obj, id) {
	var v = $(obj).val();
	if (!v) {
		dr_tips("名称未填写");
		return;
	}
	$("#dr_spec_show_name_"+id).html(v);
	$("#dr_spec_show_name_"+id).show();
	$("#dr_spec_edit_name_"+id).hide();
	var pobj = $(obj).parent().parent().parent().children("li").attr("title", v);
	step.Creat_Table();
}
var myArraymin=function(array) {
    return Float.min.apply(Float,array);
}

var step = {
	//SKU信息组合
	Creat_Table: function () {
		step.hebingFunction();
		var SKUObj = $(".Father_Title");
		//var skuCount = SKUObj.length;//
		var arrayName = new Array();　//名称组数
		var arrayTile = new Array();　//标题组数
		var arrayInfor = new Array();　//盛放每组选中的CheckBox值的对象 
		var arrayColumn = new Array(); //指定列，用来合并哪些列
		var bCheck = true;//是否全选
		var columnIndex = 0;
		$.each(SKUObj, function (i, item) {
			arrayColumn.push(columnIndex);
			columnIndex++;
			arrayTile.push(SKUObj.find("li").eq(i).attr("title").replace("：", ""));
			var itemName = "Father_Item" + i;
			//选中的CHeckBox取值
			var order = new Array();
			var order_name = new Array();
			$("." + itemName + " input[type=checkbox]:checked").each(function () {
				order.push($(this).attr("vname"));
				order_name.push($(this).val());
			});
			arrayInfor.push(order);
			arrayName.push(order_name);

			if (order.join() == "") {
				bCheck = false;
			}
			//var skuValue = SKUObj.find("li").eq(index).html();
		});

		//开始创建Table表            
		if (bCheck == true) {
			var RowsCount = 0;
			$("#dr_mall_sku").html("");
			var table = $("<table class=\"sku-style\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"width:100%;padding:5px;\"></table>");
			table.appendTo($("#dr_mall_sku"));
			var thead = $("<thead></thead>");
			thead.appendTo(table);
			var trHead = $("<tr></tr>");
			trHead.appendTo(thead);
			//创建表头
			$.each(arrayTile, function (index, item) {
				var td = $("<th>" + item + "</th>");
				td.appendTo(trHead);
			});
			var itemColumHead = $("<th>价格</th><th>数量</th><th>编码</th>");
			itemColumHead.appendTo(trHead);

			var tbody = $("<tbody></tbody>");
			tbody.appendTo(table);

			////生成组合
			var zuheDate = step.doExchange(arrayInfor);
			var zuheDate2 = step.doExchange(arrayName);
			if (zuheDate.length > 0) {
				//创建行
				$.each(zuheDate, function (index, item) {
					var td_array = item.split(",");
					var tr = $("<tr></tr>");
					var oname = zuheDate2[index].replace(/,/g, "_");
					var ovalue1 = arrayValue[oname+"_price"];
					var ovalue2 = arrayValue[oname+"_quantity"];
					var ovalue3 = arrayValue[oname+"_sn"];
					if (ovalue1 == undefined) {
						ovalue1 = '0.00';
					}
					if (ovalue2 == undefined) {
						ovalue2 = '0';
					}
					if (ovalue3 == undefined) {
						ovalue3 = '';
					}
					tr.appendTo(tbody);
					$.each(td_array, function (i, values) {
						var td = $("<td>" + values + "</td>");
						td.appendTo(tr);
					});
					var td1 = $("<td ><input name=\"data[order_specification][value]["+oname+"][price]\" onblur=\"dr_change_price('"+oname+"', this.value)\" class=\"dr_price input-text\" size=\"10\" type=\"text\" value=\""+ovalue1+"\"></td>");
					td1.appendTo(tr);
					var td2 = $("<td ><input name=\"data[order_specification][value]["+oname+"][quantity]\" onblur=\"dr_change_quantity('"+oname+"', this.value)\" class=\"dr_quantity input-text\" size=\"10\" type=\"text\" value=\""+ovalue2+"\"></td>");
					td2.appendTo(tr);
					var td3 = $("<td ><input name=\"data[order_specification][value]["+oname+"][sn]\" onblur=\"dr_change_sn('"+oname+"', this.value)\" class=\"input-text\" size=\"10\" type=\"text\" value=\""+ovalue3+"\"></td>");
					td3.appendTo(tr);
				});
			}
			//结束创建Table表
			arrayColumn.pop();//删除数组中最后一项
			//合并单元格
			$(table).mergeCell({
				// 目前只有cols这么一个配置项, 用数组表示列的索引,从0开始
				cols: arrayColumn
			});
		}
	},//合并行
	hebingFunction: function () {
		$.fn.mergeCell = function (options) {
			return this.each(function () {
				var cols = options.cols;
				for (var i = cols.length - 1; cols[i] != undefined; i--) {
					// fixbug console调试 
					// console.debug(cols[i]); 
					mergeCell($(this), cols[i]);
				}
				dispose($(this));
			});
		};
		// 如果对javascript的closure和scope概念比较清楚, 这是个插件内部使用的private方法            
		function mergeCell($table, colIndex) {
			$table.data('col-content', ''); // 存放单元格内容 
			$table.data('col-rowspan', 1); // 存放计算的rowspan值 默认为1 
			$table.data('col-td', $()); // 存放发现的第一个与前一行比较结果不同td(jQuery封装过的), 默认一个"空"的jquery对象 
			$table.data('trNum', $('tbody tr', $table).length); // 要处理表格的总行数, 用于最后一行做特殊处理时进行判断之用 
			// 我们对每一行数据进行"扫面"处理 关键是定位col-td, 和其对应的rowspan 
			$('tbody tr', $table).each(function (index) {
				// td:eq中的colIndex即列索引 
				var $td = $('td:eq(' + colIndex + ')', this);
				// 取出单元格的当前内容 
				var currentContent = $td.html();
				// 第一次时走此分支 
				if ($table.data('col-content') == '') {
					$table.data('col-content', currentContent);
					$table.data('col-td', $td);
				} else {
					// 上一行与当前行内容相同 
					if ($table.data('col-content') == currentContent) {
						// 上一行与当前行内容相同则col-rowspan累加, 保存新值 
						var rowspan = $table.data('col-rowspan') + 1;
						$table.data('col-rowspan', rowspan);
						// 值得注意的是 如果用了$td.remove()就会对其他列的处理造成影响 
						$td.hide();
						// 最后一行的情况比较特殊一点 
						// 比如最后2行 td中的内容是一样的, 那么到最后一行就应该把此时的col-td里保存的td设置rowspan 
						if (++index == $table.data('trNum'))
							$table.data('col-td').attr('rowspan', $table.data('col-rowspan'));
					} else { // 上一行与当前行内容不同 
						// col-rowspan默认为1, 如果统计出的col-rowspan没有变化, 不处理 
						if ($table.data('col-rowspan') != 1) {
							$table.data('col-td').attr('rowspan', $table.data('col-rowspan'));
						}
						// 保存第一次出现不同内容的td, 和其内容, 重置col-rowspan 
						$table.data('col-td', $td);
						$table.data('col-content', $td.html());
						$table.data('col-rowspan', 1);
					}
				}
			});
		}
		// 同样是个private函数 清理内存之用 
		function dispose($table) {
			$table.removeData();
		}
	},
	//组合数组
	doExchange: function (doubleArrays) {
		var len = doubleArrays.length;
		if (len >= 2) {
			var arr1 = doubleArrays[0];
			var arr2 = doubleArrays[1];
			var len1 = doubleArrays[0].length;
			var len2 = doubleArrays[1].length;
			var newlen = len1 * len2;
			var temp = new Array(newlen);
			var index = 0;
			for (var i = 0; i < len1; i++) {
				for (var j = 0; j < len2; j++) {
					temp[index] = arr1[i] + "," + arr2[j];
					index++;
				}
			}
			var newArray = new Array(len - 1);
			newArray[0] = temp;
			if (len > 2) {
				var _count = 1;
				for (var i = 2; i < len; i++) {
					newArray[_count] = doubleArrays[i];
					_count++;
				}
			}
			//console.log(newArray);
			return step.doExchange(newArray);
		}
		else {
			return doubleArrays[0];
		}
	}
}