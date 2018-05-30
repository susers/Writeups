
function dr_loginout(msg) {
    $.ajax({
        type: "POST",
        dataType: "json",
        url: "/index.php?s=member&c=login&m=out",
        success: function(data) {
            dr_tips(msg, 3, 1);
            setTimeout('window.location.href="' + data.backurl + '"', 2000);
            var sync_url = data.syncurl;
            for (var i in sync_url) {
                $.ajax({
                    type: "GET",
                    async: false,
                    url: sync_url[i],
                    dataType: "jsonp",
                    success: function(json) {},
                    error: function() {}
                })
            }
        },
        error: function(HttpRequest, ajaxOptions, thrownError) {
            alert(HttpRequest.responseText);
        }
    })
}
function dr_tips(msg, time, mark) {
    if (!msg || msg == '' || msg == '&nbsp;') {
        return
    }
    var mymsg;
    if (mark == 1) {
        mymsg = '<span style="color: green"><i class="fa fa-check-circle"></i> ' + msg + '</span>'
    } else if (mark == 2) {
        mymsg = msg
    } else {
        mymsg = '<span style="color: red"><i class="fa fa-times-circle"></i> ' + msg + '</span>'
    }
    if (!time) {
        time = 3
    }
    var dmsg = top.dialog({
        zIndex: 9999999999,
        content: mymsg,
        quickClose: true
    });
    dmsg.show();
    setTimeout(function() {
            dmsg.close().remove()
        },
        time * 1000);
    return dmsg
}
function dr_confirm_url(title, url) {
    var d = top.dialog({
        title: lang["tips"],
        fixed: true,
        content: '<img src="/statics/js/skins/icons/question.png"> ' + title,
        okValue: lang['ok'],
        ok: function() {
            dr_tips(lang["waiting"], 3, 1);
            window.location.href = url;
            return true
        },
        cancelValue: lang['cancel'],
        cancel: function() {}
    });
    d.show()
}
function dr_dialog_msg(msg) {
    dialog({
        quickClose: true,
        content: msg,
        title: lang["tips"]
    }).show()
}
function dr_add_favorite(url, title) {
    try {
        window.external.addFavorite(url, title)
    } catch(e) {
        try {
            window.sidebar.addPanel(title, url, "")
        } catch(e) {
            dr_dialog_msg(fc_lang[28])
        }
    }
}
function dr_set_homepage(url) {
    if ($.browser.msie) {
        document.body.style.behavior = "url(#default#homepage)";
        document.body.setHomePage(url)
    } else {
        dr_tips(fc_lang[29], 3)
    }
}
function dr_remove_file(name, id) {
    var d = top.dialog({
        title: lang["tips"],
        fixed: true,
        content: '<img src="/statics/js/skins/icons/question.png"> ' + lang["confirm"],
        okValue: lang['ok'],
        ok: function() {
            var fileid = $("#fileid_" + name + "_" + id).val();
            var value = $("#dr_" + name + "_del").val();
            $("#files_" + name + "_" + id).remove();
            $("#dr_" + name + "_del").val(value + "|" + fileid);
            return true
        },
        cancelValue: lang['cancel'],
        cancel: function() {}
    });
    d.show()
}
function dr_edit_file(url, name, id) {
    art.dialog.open(url, {
        title: lang["upload"],
        opacity: 0.1,
        width: 550,
        height: 400,
        ok: function() {
            var iframe = this.iframe.contentWindow;
            if (!iframe.document.body) {
                dr_tips("iframe loading");
                return false
            }
            var value = iframe.document.getElementById("att-status").innerHTML;
            if (value == "" || value == undefined) {
                dr_tips(lang["notselectfile"]);
                return false
            } else {
                var file = value.split("|");
                var info = file[1].split(",");
                $("#fileid_" + name + "_" + id).val(info[0]);
                $("#span_" + name + "_" + id).html('<a href="javascript:;" class="btn btn-sm blue" onclick="dr_show_file_info(\'' + info[0] + '\')"><i class="fa fa-search"></i></a>');
                return true
            }
        },
        cancel: true
    })
}
function dr_input_files(name, count) {
    var size = $("#" + name + "-sort-items li").size();
    var total = count - size;
    if (total <= 0) {
        dr_tips(fc_lang[42]);
        return
    }
    var id = size + 1;
    var url = '/index.php?s=member&c=api&&m=upload_input';
    $.ajax({
        type: "GET",
        url: url,
        dataType: "text",
        success: function(text) {
            var d = top.dialog({
                title: lang["input"],
                content: text,
                okValue: lang['ok'],
                ok: function() {
                    var title = top.$('#dr_title').val();
                    var furl = top.$('#dr_url').val();
                    if (!title || !furl) {
                        dr_tips(fc_lang[43]);
                        return false
                    }
                    var c = "";
                    c += '<li id="files_' + name + "_" + id + '" list="' + id + '" style="cursor:move;">';
                    c += '<input type="hidden" value="' + furl + '" name="data[' + name + '][file][]" id="fileid_' + name + "_" + id + '" />';
                    c += '<label><input type="text" class="form-control" style="width:300px;height:30px;" value="' + title + '" name="data[' + name + '][title][]" /></label>\t';
                    c += '<label><a href="javascript:;" class="btn btn-sm red" onclick="dr_remove_file(\'' + name + "','" + id + "')\">";
                    c += '<i class="fa fa-trash"></i></a></label>\t';
                    c += '<label id="span_' + name + '_' + id + '"><a href="javascript:;" class="btn btn-sm blue" onclick="dr_show_file_info(\'' + furl + "')\">";
                    c += '<i class="fa fa-search"></i></a></label>\t';
                    c += "</li>";
                    $("#" + name + "-sort-items").append(c);
                    return true
                },
                cancelValue: lang['cancel'],
                cancel: function() {
                    return true
                }
            });
            d.show()
        },
        error: function(HttpRequest, ajaxOptions, thrownError) {
            dr_alert(HttpRequest.responseText)
        }
    })
}
function dr_edit_input_file(file2, title2, name, id) {
    var url = '/index.php?s=member&c=api&m=upload_input&file=' + file2 + '&title=' + title2;
    $.ajax({
        type: "GET",
        url: url,
        dataType: "text",
        success: function(text) {
            var d = top.dialog({
                title: lang["input"],
                content: text,
                okValue: lang['ok'],
                ok: function() {
                    var title = top.$('#dr_title').val();
                    var furl = top.$('#dr_url').val();
                    if (!title || !furl) {
                        dr_tips(fc_lang[43]);
                        return false
                    }
                    var c = "";
                    c += '<input type="hidden" value="' + furl + '" name="data[' + name + '][file][]" id="fileid_' + name + "_" + id + '" />';
                    c += '<label><input type="text" class="form-control" style="width:300px;height:30px;" value="' + title + '" name="data[' + name + '][title][]" /></label>\t';
                    c += '<label><a href="javascript:;" class="btn btn-sm red" onclick="dr_remove_file(\'' + name + "','" + id + "')\">";
                    c += '<i class="fa fa-trash"></i></a></label>\t';
                    c += '<label><a href="javascript:;" class="btn btn-sm green" onclick="dr_edit_input_file(\'' + furl + "', '" + title + "', '" + name + "','" + id + "')\">";
                    c += '<i class="fa fa-edit"></i></a></label>\t';
                    c += '<label id="span_' + name + '_' + id + '"><a href="javascript:;" class="btn btn-sm blue" onclick="dr_show_file_info(\'' + furl + "')\">";
                    c += '<i class="fa fa-search"></i></a></label>\t';
                    $('#files_' + name + "_" + id + '').html(c);
                    return true
                },
                cancelValue: lang['cancel'],
                cancel: function() {
                    return true
                }
            });
            d.show()
        },
        error: function(HttpRequest, ajaxOptions, thrownError) {
            dr_alert(HttpRequest.responseText)
        }
    })
}
function dr_upload_files(name, url, pan, count) {
    var size = $("#" + name + "-sort-items li").size();
    var total = count - size;
    art.dialog.open(url + "&count=" + total, {
        title: lang["upload"],
        opacity: 0.1,
        width: 550,
        height: 400,
        ok: function() {
            var iframe = this.iframe.contentWindow;
            if (!iframe.document.body) {
                dr_tips("iframe loading");
                return false
            }
            var value = iframe.document.getElementById("att-status").innerHTML;
            if (value == "" || value == undefined) {
                return false
            } else {
                var file = value.split("|");
                for (var i in file) {
                    var filepath = file[i];
                    var id = parseInt(size) + parseInt(i);
                    if (filepath) {
                        var info = filepath.split(",");
                        if ($("#" + name + '-sort-items [value="' + info[0] + '"]').length > 0) {
                            dr_tips(fc_lang[27]);
                            return false
                        }
                        if (!info[0] || info[0] == undefined) {
                            info[0] = ''
                        }
                        if (!info[3] || info[3] == undefined) {
                            info[3] = info[0]
                        }
                        info[3] = dr_remove_ext(info[3]);
                        var c = "";
                        c += '<li id="files_' + name + "_" + id + '" list="' + id + '" style="cursor:move;">';
                        c += '<input type="hidden" value="' + info[0] + '" name="data[' + name + '][file][]" id="fileid_' + name + "_" + id + '" />';
                        c += '<label><input type="text" class="form-control" style="width:300px;height:30px;" value="' + info[3] + '" name="data[' + name + '][title][]" /></label>\t';
                        c += '<label><a href="javascript:;" class="btn btn-sm red" onclick="dr_remove_file(\'' + name + "','" + id + "')\">";
                        c += '<i class="fa fa-trash"></i></a></label>\t';
                        c += '<label id="span_' + name + '_' + id + '"><a href="javascript:;" class="btn btn-sm blue" onclick="dr_show_file_info(\'' + info[0] + "')\">";
                        c += '<i class="fa fa-search"></i></a></label>\t';
                        c += "</li>";
                        $("#" + name + "-sort-items").append(c)
                    }
                }
                return true
            }
        },
        cancel: true
    })
}
function dr_new_upload_file(name, url) {
    $.ajax({
        type: "GET",
        url: url + '&' + Math.random(),
        dataType: "text",
        success: function(text) {
            var d = top.dialog({
                title: fc_lang[3],
                content: text,
                okValue: lang['ok'],
                ok: function() {
                    var title = top.$('#dr_title').val();
                    var furl = top.$('#dr_url').val();
                    if (!title || !furl) {
                        dr_tips(fc_lang[43]);
                        return false
                    }
                    return true
                },
                cancelValue: lang['cancel'],
                cancel: function() {
                    return true
                }
            });
            d.show()
        },
        error: function(HttpRequest, ajaxOptions, thrownError) {
            dr_alert(HttpRequest.responseText);
        }
    })
}
function dr_upload_file(name, url) {
    art.dialog.open(url + "&df=1", {
        title: lang["upload"],
        opacity: 0.1,
        width: 550,
        height: 400,
        ok: function() {
            var iframe = this.iframe.contentWindow;
            if (!iframe.document.body) {
                dr_tips("iframe loading");
                return false
            }
            var value = iframe.document.getElementById("att-status").innerHTML;
            if (value == "" || value == undefined) {
                dr_tips(lang["notselectfile"]);
                return false
            } else {
                $("#file_info_" + name).remove();
                var file = value.split("|");
                var info = file[1].split(",");
                if (iframe.$("#is_down").attr("checked")) {
                    var url2 = memberpath + "index.php?s=member&c=api&m=down_file";
                    $("#show_" + name).html("&nbsp;&nbsp;远程文件下载中...");
                    $.ajax({
                        type: "POST",
                        url: url2,
                        data: {
                            url: url,
                            file: info[0]
                        },
                        dataType: "json",
                        success: function(text) {
                            $("#show_" + name).html("");
                            if (text.status == 0) {
                                dr_tips(text.code);
                                return false
                            } else {
                                info[0] = text.id;
                                info[1] = text.name;
                                $("#dr_" + name).val(text.id);
                                $("#fileid_" + name).val(text.id);
                                $("#dr_my_" + name + "_list").html("<button type=\"button\" style=\"cursor:pointer;\" class=\"btn green btn-sm file_info_" + name + "\" onclick=\"dr_show_file_info('" + info[0] + "')\"> <i class=\"fa fa-search\"></i> " + info[1] + "</button> <button type=\"button\" style=\"cursor:pointer;\" class=\"btn red btn-sm file_info_" + name + "\" onclick=\"dr_delete_file_js('" + name + "')\"> <i class=\"fa fa-trash\"></i> " + lang['del'] + "</button> ")
                            }
                        }
                    })
                } else {
                    $("#dr_" + name).val(info[0]);
                    $("#fileid_" + name).val(info[0]);
                    $("#dr_my_" + name + "_list").html("<button type=\"button\" style=\"cursor:pointer;\" class=\"btn green btn-sm file_info_" + name + "\" onclick=\"dr_show_file_info('" + info[0] + "')\"> <i class=\"fa fa-search\"></i> " + info[3] + "</button> <button type=\"button\" style=\"cursor:pointer;\" class=\"btn red btn-sm file_info_" + name + "\" onclick=\"dr_delete_file_js('" + name + "')\"> <i class=\"fa fa-trash\"></i> " + lang['del'] + "</button> ");
                    return true
                }
            }
        },
        cancel: true
    })
}
function dr_login() {
    top.dialog({
        quickClose: true,
        url: memberpath + "index.php?s=member&c=login&m=ajax",
        title: lang["login"],
        opacity: 0.1,
        lock: true,
        width: 380,
        height: 220,
        okValue: lang['ok'],
        ok: function() {
            window.location.reload(true);
            return false
        },
        cancelValue: lang['cancel'],
        cancel: function() {}
    }).show()
}
function dr_chat(_this) {
    var uid = $(_this).attr("uid");
    var online = $(_this).attr("online");
    var username = $(_this).attr("username");
    if (online == -1) {
        var title = "正在与" + username + "聊天中..."
    } else {
        if (online == 1) {
            var title = "正在与" + username + "聊天中... [在线]"
        } else {
            var title = "正在与" + username + "聊天中... [离线]"
        }
    }
    var throughBox = $.dialog.through;
    var dr_dialog = throughBox({
        id: "dr_webchat",
        title: title,
        padding: 0,
        width: 420,
        height: 480
    });
    var url = memberpath + "index.php?s=member&c=api&m=webchat&username=" + username + "&uid=" + uid + "&online=" + online + "&" + Math.random();
    $.ajax({
        type: "GET",
        url: url,
        dataType: "jsonp",
        jsonp: "callback",
        async: false,
        success: function(text) {
            dr_dialog.content(text.html)
        },
        error: function(HttpRequest, ajaxOptions, thrownError) {
            dr_dialog.close();
            dr_login()
        }
    })
}
function dr_delete_file_js(name) {
    $("#dr_" + name).val("");
    $(".file_info_" + name).remove()
}
function dr_delete_file(name) {
    var d = top.dialog({
        title: lang["tips"],
        fixed: true,
        content: '<img src="/statics/js/skins/icons/question.png"> ' + lang["confirm"],
        okValue: lang['ok'],
        ok: function() {
            $("#dr_" + name).val("");
            $(".file_info_" + name).remove();
            return true
        },
        cancelValue: lang['cancel'],
        cancel: function() {}
    });
    d.show()
}
function dr_delete_file2(name) {
    var d = top.dialog({
        title: lang["tips"],
        fixed: true,
        content: '<img src="/statics/js/skins/icons/question.png"> ' + lang["confirm"],
        okValue: lang['ok'],
        ok: function() {
            $("#fileid_" + name).val("");
            $("#dr_my_" + name + "_list").html("");
            return true
        },
        cancelValue: lang['cancel'],
        cancel: function() {}
    });
    d.show()
}
function dr_show_file_info(name) {
    var url = memberpath + "index.php?s=member&c=api&m=fileinfo&name=" + name + "&rand=" + Math.random();
    var d = top.dialog({
        title: lang["fileinfo"],
        fixed: true,
        url: url,
        quickClose: true
    });
    d.show()
}
function dr_upload(name, ext, size, count) {
    alert("此函数已废弃");
    return
}
function dr_remove_ext(str) {
    var reg = /\.\w+$/;
    return str.replace(reg, "")
}
function dr_clear_date(name) {
    $("#dr_" + name).val("0");
    $("#calendar_" + name).val("")
}
function dr_clear_color(name) {
    $("#input_colorId" + name).val("");
    $("#background_colorId" + name).css("background-color", "");
    $("#dr_" + name).val("");
    $(".sp-preview-inner").attr("style", "background-color:rgb(0, 0, 0);");
    $("#dr_color_value_" + name).attr("style", "background:none");
    dr_closeBox(name)
}
function dr_set_color(name) {
    var v = $("#input_colorId" + name).val();
    $("#dr_" + name).val(v);
    $("#dr_color_value_" + name).attr("style", "background:" + v);
    dr_closeBox(name)
}
var ColorHex = new Array("00", "33", "66", "99", "CC", "FF");
var SpColorHex = new Array("FF0000", "00FF00", "0000FF", "FFFF00", "00FFFF", "FF00FF");
var current = null;
var colorTable = "";
function dr_color(name) {
    for (i = 0; i < 2; i++) {
        for (j = 0; j < 6; j++) {
            colorTable = colorTable + "<tr height=12>";
            colorTable = colorTable + "<td width=11 onmouseover=\"dr_onmouseover_color('" + name + "', '000')\" onclick=\"dr_select_color('" + name + "','000')\" style=\"background-color:#000\">";
            if (i == 0) {
                colorTable = colorTable + "<td width=11 onmouseover=\"dr_onmouseover_color('" + name + "', '" + ColorHex[j] + ColorHex[j] + ColorHex[j] + "')\" onclick=\"dr_select_color('" + name + "','" + ColorHex[j] + ColorHex[j] + ColorHex[j] + '\')" style="background-color:#' + ColorHex[j] + ColorHex[j] + ColorHex[j] + '">'
            } else {
                colorTable = colorTable + "<td width=11 onmouseover=\"dr_onmouseover_color('" + name + "', '" + SpColorHex[j] + "')\" onclick=\"dr_select_color('" + name + "','" + SpColorHex[j] + '\')" style="background-color:#' + SpColorHex[j] + '">'
            }
            colorTable = colorTable + "<td width=11 onmouseover=\"dr_onmouseover_color('" + name + "', '000')\" onclick=\"dr_select_color('" + name + "','000')\" style=\"background-color:#000\">";
            for (k = 0; k < 3; k++) {
                for (l = 0; l < 6; l++) {
                    colorTable = colorTable + "<td width=11 onmouseover=\"dr_onmouseover_color('" + name + "', '" + ColorHex[k + i * 3] + ColorHex[l] + ColorHex[j] + "')\" onclick=\"dr_select_color('" + name + "','" + ColorHex[k + i * 3] + ColorHex[l] + ColorHex[j] + '\')"  style="background-color:#' + ColorHex[k + i * 3] + ColorHex[l] + ColorHex[j] + '">'
                }
            }
        }
    }
    colorTable = '<div class="dr_color" style="position:relative;width:253px; height:176px"><a href="javascript:;" onclick="dr_closeBox(\'' + name + '\');" class="close-own">X</a><table width=253 border="0" cellspacing="0" cellpadding="0" style="border:1px #000 solid;border-bottom:none;border-collapse: collapse" bordercolor="000000">' + "<tr height=30><td colspan=21 bgcolor=#eeeeee>" + '<table cellpadding="0" cellspacing="1" border="0" style="border-collapse: collapse"><tr><td width="3"><td><input type="text" name="DisColor" size="6" id="background_colorId' + name + '" disabled style="border:solid 1px #000000;background-color:#ffff00"></td><td width="3"><td><input type="text" name="HexColor" size="7" id="input_colorId' + name + '" style="border:inset 1px;font-family:Arial;" value="#000000"></td><td><a href="javascript:;" onclick="dr_set_color(\'' + name + '\');"> ok</a>&nbsp;&nbsp;<a href="javascript:;" onclick="dr_clear_color(\'' + name + "');\"> clear</a></td></tr></table></td></table>" + '<table width=253  border="1" cellspacing="0" cellpadding="0" style="border-collapse: collapse" bordercolor="000000" style="cursor:hand;">' + colorTable + "</table></div>";
    $("#dr_color_" + name).html(colorTable);
    colorTable = ""
}
function dr_onmouseover_color(name, color) {
    var color = "#" + color;
    $("#background_colorId" + name).css("background-color", color);
    $("#input_colorId" + name).val(color)
}
function dr_select_color(name, color) {
    var color = "#" + color;
    $("#dr_color_" + name).html(" ");
    $("#dr_color_value_" + name).attr("style", "background:" + color);
    $("#dr_" + name).val(color)
}
function dr_closeBox(name) {
    $("#dr_color_" + name).html(" ")
};