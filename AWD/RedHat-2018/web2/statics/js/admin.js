
function dr_notify(msg, time) {

	if (!time) {
		time = 3000;
	}
	var settings = {
			theme: "teal",
			life: time,
			horizontalEdge: "top",
			verticalEdge: "right"
		},
		$button = $(this);

	$.notific8('zindex', 11500);
	$.notific8($.trim(msg), settings);
}

function dr_alert(msg) {
	dr_tips(msg, 9);
}
function dr_diy_type(t) {
	$("#dr_diy_type_0").hide();
	$("#dr_diy_type_1").hide();
	$("#dr_diy_type_" + t).show()
}
function dr_member_rule(id, url, title) {

	$.ajax({
		type: "GET",
		url: url,
		dataType: "text",
		success: function(text) {
			var d = top.dialog({
				title: title,
				content: text,
				okValue: lang['ok'],
				ok: function () {
					var that = this;
					that.title(''+lang['ing']);
					top.$("#mark").val("0");
					if (top.dr_form_check()) {
						var _data = top.$("#myform").serialize();
						$.ajax({
							type: "POST",
							dataType: "text",
							url: url,
							data: _data,
							success: function(data) {
								d.close().remove();
								$("#dr_status_" + id).html(" <i class='fa fa-check-square'></i>");
								dr_tips(fc_lang[37], 2, 1);
							},
							error: function(HttpRequest, ajaxOptions, thrownError) {

							}
						})
					}

					return false;
				},
				cancelValue: lang['cancel'],
				cancel: function () {
					return true;
				}
			});
			d.show();
		},
		error: function(HttpRequest, ajaxOptions, thrownError) {
			dr_alert(HttpRequest.responseText)
		}
	});

}
function dr_install(text, url) {

	var d = top.dialog({
		title: fc_lang["33"],
		content: "<div style='width:500px;line-height:23px;font-size:13px;padding-bottom:10px'>" + text + "</div>",
		okValue: fc_lang["34"],
		ok: function () {
			var that = this;
			that.title(''+lang['ing']);
			dr_tips(lang["waiting"], 99, 1);
			dr_goto_url(url);

			return false;
		},
		cancelValue: fc_lang["35"],
		cancel: function () {
			return true;
		}
	});
	d.show();
}
function dr_install_share(text, url) {

	var d = top.dialog({
		title: "安装",
		content: "<div style='width:500px;line-height:23px;font-size:13px;padding-bottom:10px'>" + text + "</div>",
		button: [
			{
				value: lang["module-1"],
				callback: function () {
					dr_tips(lang["waiting"], 99, 1);
					dr_goto_url(url+'&share=1');
					return false;
				},
				focus: true
			},
			{
				value: lang["module-2"],
				callback: function () {
					dr_tips(lang["waiting"], 99, 1);
					dr_goto_url(url);
					return false;
				}
			},
		]
	});
	d.show();

}
function dr_dialog_member(id) {
	if (id == "author") {
		id = $("#dr_author").val()
	}
	top.dialog({
		zIndex:99,
		quickClose: true,
		url: siteurl + "?c=api&m=member&uid=" + id,
		title: lang["smember"]
	}).show();
}
function dr_dialog_ip(id) {
	var name = $("#dr_" + id).val();
	if (name) {
		top.dialog({
			quickClose: true,
			url: "http://www.baidu.com/baidu?wd=" + name,
			title: "IP",
			width: 700,
			height: 400
		}).show();
	} else {
		dr_tips("[" + id + "] " + lang["iperror"], 3)
	}
}
/*
(function(config) {
	config["lock"] = true;
	config["fixed"] = true;
	config["drag"] = true;
	config["esc"] = true;
	config["resize"] = false;
	config["opacity"] = 0.1;
	config["padding"] = "5px 10px 5px 10px"
})(art.dialog.defaults);
*/
function dr_page_rule() {
	var body = '<style>.table-list2 tbody td, .table-list2 .btn {padding-bottom:1px;padding-top:1px;}</style><table border="0" cellpadding="1" cellspacing="0" class="table-list table-list2">';
	body += "<tr><td>{id}</td><td>Id</td></tr><tr>";
	body += "<tr><td>{page}</td><td>" + lang["page"] + "</td></tr>";
	body += "<tr><td>{dirname}</td><td>" + lang["dirname"] + "</td></tr>";
	body += "<tr><td>{pdirname}</td><td>" + lang["pdirname"] + "</td></tr>";
	body += "<tr><td>{fid}</td><td>" + lang["fid"] + "</td></tr>";
	body += "</table>&nbsp;";
	top.dialog({
		quickClose: true,
		content: body,
		title: lang["tagurl"]
	}).show();
}
function dr_url_rule() {
	var body = '<style>.table-list2 tbody td, .table-list2 .btn {padding-bottom:1px;padding-top:1px;}</style><table border="0" cellpadding="1" cellspacing="0" class="table-list table-list2">';
	body += '<tr><td width="15%">' + lang["tag"] + '</td><td width="85%">&nbsp;</td></tr>';
	body += "<tr><td>{id}</td><td>Id</td></tr><tr>";
	body += "<tr><td>{page}</td><td>" + lang["page"] + "</td></tr>";
	body += "<tr><td>{modname}</td><td>" + lang["modname"] + "</td></tr>";
	body += "<tr><td>{dirname}</td><td>" + lang["dirname"] + "</td></tr>";
	body += "<tr><td>{pdirname}</td><td>" + lang["pdirname"] + "</td></tr>";
	body += "<tr><td>{fid}</td><td>" + lang["fid"] + "</td></tr>";
	body += "<tr><td>&nbsp;</td><td>" + lang["tagvalue"] + "</td></tr>";
	body += "<tr><td>" + lang["function"] + "</td><td>&nbsp;</td></tr>";
	body += "<tr><td>{md5({id})}</td><td>" + lang["funcvalue1"] + "</td></tr>";
	body += "<tr><td>{test($data)}</td><td>" + lang["funcvalue2"] + "</td></tr>";
	body += "<tr><td>&nbsp;</td><td>" + lang["tagmore"] + "</td></tr>";
	body += "</table>";
	top.dialog({
		quickClose: true,
		content: body,
		title: lang["tagurl"]
	}).show();
}
function dr_seo_rule() {
	var body = '<style>.table-list tbody td, .table-list .btn {height:25px;line-height:25px;padding-bottom:1px;padding-top:1px;}</style><table border="0" cellpadding="1" cellspacing="0" class="table-list">';
	body += '<tr><td width="15%">' + lang["tag"] + '</td><td width="85%">&nbsp;</td></tr>';
	body += "<tr><td>{join}</td><td>" + lang["seojoin"] + "</td></tr><tr>";
	body += "<tr><td>{modulename}</td><td>" + lang["seoname"] + "</td></tr><tr>";
	body += "<tr><td>[{page}]</td><td>" + lang["seopage"] + "</td></tr>";
	body += "<tr><td>{SITE_NAME}</td><td>" + lang["seositename"] + "</td></tr>";
	body += "<tr><td>&nbsp;</td><td>" + lang["seovalue1"] + "</td></tr>";
	body += "<tr><td>&nbsp;</td><td>" + lang["seovalue2"] + "</td></tr>";
	body += "<tr><td>" + lang["function"] + "</td><td>&nbsp;</td></tr>";
	body += "<tr><td>{test($data)}</td><td>" + lang["seofuncvalue"] + "</td></tr>";
	body += "<tr><td>&nbsp;</td><td>" + lang["seodiy"] + "</td></tr>";
	body += "<tr><td>&nbsp;</td><td>" + lang["tagmore"] + "</td></tr>";
	body += "</table>";
	top.dialog({
		quickClose: true,
		content: body,
		title: lang["tagseo"]
	}).show();
}
function set_frontop(v) {
	if (v == 1) {
		$(".tabBut li:gt(1)").show()
	} else {
		$(".tabBut li:gt(1)").hide()
	}
}
function set_urlmode(v) {
	if (v == 1) {
		$("#urlmode").show()
	} else {
		$("#urlmode").hide()
	}
}
function set_sitemode(v) {
	if (v == 1) {
		$("#sitemode").show()
	} else {
		$("#sitemode").hide()
	}
}
function set_urltohtml(v) {
	if (v == 1) {
		$("#html").show()
	} else {
		$("#html").hide()
	}
}
function SwapTab(id) {
	$("#myform .tabBut").children("li").removeClass("on");
	$(".tabBut li:eq(" + id + ")").attr("class", "on");
	$("#myform .dr_hide").hide();
	$("#cnt_" + id).show();
	$("#myform #page").val(id)
}
function dr_form_tips(name, status, code) {
	var obj = $("#dr_" + name + "_tips");
	obj.html("");
	if (status) {
		obj.attr("class", "");
		dr_tips(code, 3, 1)
	} else {
		obj.attr("class", "");
		dr_tips(code)
	}
}
function dr_selected() {
	if ($("#dr_select").prop("checked")) {
		$(".dr_select").prop("checked", true);
	} else {
		$(".dr_select").prop("checked", false);
	}
}
function dr_selected_by(id) {
	if ($("#" + id).prop("checked")) {
		$("." + id).prop("checked", true);
	} else {
		$("." + id).prop("checked", false);
	}
}
function dr_goto_url(url) {
	window.location.href = url;
}
function dr_waiting() {
	dr_tips(lang["waiting"], 3, 1);
}
function dr_dialog_show(title, url) {
	$.ajax({
		type: "POST",
		dataType: "text",
		url: url,
		data: {},
		success: function(data) {
			top.dialog({
				quickClose: true,
				content: data,
				title: title
			}).show();
		},
		error: function(HttpRequest, ajaxOptions, thrownError) {}
	})


}
function dr_dialog_set(text, url) {

	var d = top.dialog({
		title: lang['tips'],
		fixed: true,
		content: '<img src="/statics/js/skins/icons/question.png"> '+text,
		okValue: lang['ok'],
		ok: function () {
			this.title(''+lang['ing']);
			$.ajax({
				type: "POST",
				dataType: "json",
				url: url,
				data: {},
				success: function(data) {
					if (data.status == 1) {
						d.close().remove();
						dr_tips(data.code, 2, 1);
						setTimeout("window.location.reload(true)", 2000)
					} else {
						dr_tips(data.code, 2, 1);
						top.$(".page-loading").remove()
					}
				},
				error: function(HttpRequest, ajaxOptions, thrownError) {}
			})
		},
		cancelValue: lang['cancel'],
		cancel: function () {}
	});
	d.show();
}
function dr_confirm_set_all(title, del) {

	var d = top.dialog({
		title: lang['tips'],
		fixed: true,
		content: '<img src="/statics/js/skins/icons/question.png"> '+title,
		okValue: lang['ok'],
		ok: function () {
			this.title(''+lang['ing']);
			var _data = $("#myform").serialize();
			var _url = window.location.href;
			if ((_data.split("ids")).length - 1 <= 0) {
				d.close().remove();
				dr_tips(lang["select_null"]);
				return false;
			}
			$.ajax({
				type: "POST",
				dataType: "json",
				url: _url,
				data: _data,
				success: function(data) {
					if (data.status == 1) {
						dr_tips(data.code, 3, 1);
						if (del == 1) {
							$(".dr_select").each(function() {
								if ($(this).attr("checked")) {
									$("#dr_row_" + $(this).val()).remove()
								}
							})
						} else {
							setTimeout("window.location.reload(true)", 3000)
						}
					} else {
						dr_tips(data.code, 3, 2);
						top.$(".page-loading").remove();
						return true
					}
				},
				error: function(HttpRequest, ajaxOptions, thrownError) {}
			});
		},
		cancelValue: lang['cancel'],
		cancel: function () {}
	});
	d.show();
}
function dr_dialog_del(text, url) {

	var d = top.dialog({
		title: lang["del"],
		fixed: true,
		content: '<img src="/statics/js/skins/icons/question.png"> '+text,
		okValue: lang['ok'],
		ok: function () {
			this.title(''+lang['ing']);

			$.ajax({
				type: "POST",
				dataType: "json",
				url: url,
				data: {},
				success: function(data) {
					if (data.status == 1) {
						d.close().remove();
						dr_tips(data.code, 3, 1);
						setTimeout("window.location.reload(true)", 2000)
					} else {
						dr_tips(data.code, 2, 2);
						top.$(".page-loading").remove()
					}
				},
				error: function(HttpRequest, ajaxOptions, thrownError) {}
			})
		},
		cancelValue: lang['cancel'],
		cancel: function () {}
	});
	d.show();

}
function dr_confirm_del_all() {

	var d = top.dialog({
		title: lang["tips"],
		fixed: true,
		content: '<img src="/statics/js/skins/icons/question.png"> '+lang["confirm"],
		okValue: lang['ok'],
		ok: function () {
			this.title(''+lang['ing']);
			var _data = $("#myform").serialize();
			var _url = window.location.href;
			if ((_data.split("ids")).length - 1 <= 0) {
				d.close().remove();
				dr_tips(lang["select_null"], 2);
				return true
			}
			$.ajax({
				type: "POST",
				dataType: "json",
				url: _url,
				data: _data,
				success: function(data) {
					if (data.status == 1) {
						d.close().remove();
						dr_tips(data.code, 2, 1);
						setTimeout("window.location.reload(true)", 2000)
					} else {
						dr_tips(data.code, 2, 2);
						top.$(".page-loading").remove();
						return true
					}
				},
				error: function(HttpRequest, ajaxOptions, thrownError) {}
			});
			return true
		},
		cancelValue: lang['cancel'],
		cancel: function () {}
	});
	d.show();
}
function dr_dialog(url, func) {
	switch (func) {
		case "add":
			var _title = lang["add"];
			break;
		case "edit":
			var _title = lang["edit"];
			break;
		default:
			return false;
			break
	}
	$.ajax({
		type: "GET",
		url: url,
		dataType: "text",
		success: function(text) {
			var d = top.dialog({
				title: _title,
				content: text,
				okValue: lang['ok'],
				ok: function () {
					var that = this;
					that.title(''+lang['ing']);
					top.$("#mark").val("0");
					if (top.dr_form_check()) {
						var _data = top.$("#myform").serialize();
						$.ajax({
							type: "POST",
							dataType: "json",
							url: url,
							data: _data,
							success: function(data) {
								if (data.status == 1) {
									d.close().remove();
									dr_tips(data.code, 2, 1);
									setTimeout("window.location.reload(true)", 3000)
								} else {
									that.title(_title);
									top.d_tips(data.id, false, data.code);
									top.$(".page-loading").remove();
									return false
								}
							},
							error: function(HttpRequest, ajaxOptions, thrownError) {}
						})
					}

					return false;
				},
				cancelValue: lang['cancel'],
				cancel: function () {
					return true;
				}
			});
			d.show();
		},
		error: function(HttpRequest, ajaxOptions, thrownError) {
			dr_alert(HttpRequest.responseText)
		}
	});
}
function dr_upload_files2(url) {
	top.dialog({
		title: lang["upload"],
		quickClose: true,
		url: url,
		width: 550,
		height: 400,
		okValue: lang['ok'],
		ok: function() {
			window.location.reload(true)
		}
	}).show();
};


$(function() {
	$('#bs_confirmation_delete').on('confirmed.bs.confirmation', function () {
		$('#action').val('del');
		var _data = $("#myform").serialize();
		var _url = window.location.href;
		if ((_data.split("ids")).length - 1 <= 0) {
			dr_tips(lang["select_null"], 2);
			return true
		}
		$.ajax({
			type: "POST",
			dataType: "json",
			url: _url,
			data: _data,
			success: function(data) {
				if (data.status == 1) {
					dr_tips(data.code, 2, 1);
					setTimeout("window.location.reload(true)", 2000)
				} else {
					dr_tips(data.code, 3, 2);
					top.$(".page-loading").remove();
					return true
				}
			},
			error: function(HttpRequest, ajaxOptions, thrownError) {}
		});
		return true
	});
	$('#dr_confirm_set_all').on('confirmed.bs.confirmation', function () {
		$('#action').val('del');
		var _data = $("#myform").serialize();
		var _url = window.location.href;
		if ((_data.split("ids")).length - 1 <= 0) {
			dr_tips(lang["select_null"], 2);
			return true
		}
		$.ajax({
			type: "POST",
			dataType: "json",
			url: _url,
			data: _data,
			success: function(data) {
				if (data.status == 1) {
					dr_tips(data.code, 3, 1);
					setTimeout("window.location.reload(true)", 3000)
				} else {
					dr_tips(data.code, 3, 2);
					top.$(".page-loading").remove();
					return true
				}
			},
			error: function(HttpRequest, ajaxOptions, thrownError) {}
		});
		return true
	});
	$('#dr_confirm_order').on('confirmed.bs.confirmation', function () {
		$('#action').val('order');
		var _data = $("#myform").serialize();
		var _url = window.location.href;
		if ((_data.split("ids")).length - 1 <= 0) {
			dr_tips(lang["select_null"], 2);
			return true
		}
		$.ajax({
			type: "POST",
			dataType: "json",
			url: _url,
			data: _data,
			success: function(data) {
				if (data.status == 1) {
					dr_tips(data.code, 3, 1);
					setTimeout("window.location.reload(true)", 3000)
				} else {
					dr_tips(data.code, 3, 2);
					top.$(".page-loading").remove();
					return true
				}
			},
			error: function(HttpRequest, ajaxOptions, thrownError) {}
		});
		return true
	});
	$('#dr_confirm_verify').on('confirmed.bs.confirmation', function () {
		$('#action').val('verify');
		var _data = $("#myform").serialize();
		var _url = window.location.href;
		if ((_data.split("ids")).length - 1 <= 0) {
			dr_tips(lang["select_null"], 2);
			return true
		}
		$.ajax({
			type: "POST",
			dataType: "json",
			url: _url,
			data: _data,
			success: function(data) {
				if (data.status == 1) {
					dr_tips(data.code, 3, 1);
					setTimeout("window.location.reload(true)", 3000)
				} else {
					dr_tips(data.code, 3, 2);
					top.$(".page-loading").remove();
					return true
				}
			},
			error: function(HttpRequest, ajaxOptions, thrownError) {}
		});
		return true
	});
});