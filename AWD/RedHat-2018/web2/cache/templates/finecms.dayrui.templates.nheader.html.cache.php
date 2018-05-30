<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE8" />
    <title>admin</title>
    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link href="<?php echo THEME_PATH; ?>admin/css/index.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo THEME_PATH; ?>admin/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo THEME_PATH; ?>admin/css/font-awesome/css/font-awesome.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo THEME_PATH; ?>admin/css/table_form.css" rel="stylesheet"  />
    <link href="<?php echo THEME_PATH; ?>admin/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo THEME_PATH; ?>admin/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo THEME_PATH; ?>admin/global/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo THEME_PATH; ?>admin/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo THEME_PATH; ?>admin/global/plugins/bootstrap-tagsinput/bootstrap-tagsinput.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo THEME_PATH; ?>admin/global/plugins/bootstrap-colorpicker/css/colorpicker.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo THEME_PATH; ?>admin/global/plugins/jquery-minicolors/jquery.minicolors.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo THEME_PATH; ?>admin/global/plugins/jquery-notific8/jquery.notific8.min.css" rel="stylesheet" type="text/css" />
    <!-- END GLOBAL MANDATORY STYLES -->
    <!-- BEGIN THEME GLOBAL STYLES -->
    <link href="<?php echo THEME_PATH; ?>admin/global/css/components-rounded.min.css" rel="stylesheet" id="style_components" type="text/css" />
    <link href="<?php echo THEME_PATH; ?>admin/global/css/plugins.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo THEME_PATH; ?>admin/my.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo THEME_PATH; ?>admin/css/font-awesome/css/font-awesome.css" rel="stylesheet" />
    <!-- END THEME GLOBAL STYLES -->
    <!-- BEGIN THEME LAYOUT STYLES -->
    <link href="<?php echo THEME_PATH; ?>admin/layouts/layout/css/layout.min.css" rel="stylesheet" type="text/css" />
    <!-- END THEME LAYOUT STYLES -->
    <!--[if lt IE 9]>
    <script src="<?php echo THEME_PATH; ?>admin/global/plugins/respond.min.js"></script>
    <script src="<?php echo THEME_PATH; ?>admin/global/plugins/excanvas.min.js"></script>
    <![endif]-->
    <script type="text/javascript">var siteurl = "<?php echo SITE_PATH;  echo SELF; ?>";var memberpath = "<?php echo MEMBER_PATH; ?>";var sys_theme = "<?php echo THEME_PATH; ?>admin/";</script>
    <script type="text/javascript" src="<?php echo THEME_PATH; ?>js/<?php echo SITE_LANGUAGE; ?>.js"></script>
    <!-- BEGIN CORE PLUGINS -->
    <script src="<?php echo THEME_PATH; ?>admin/global/plugins/jquery.min.js" type="text/javascript"></script>
    <script src="<?php echo THEME_PATH; ?>admin/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="<?php echo THEME_PATH; ?>admin/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
    <script src="<?php echo THEME_PATH; ?>admin/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
    <script src="<?php echo THEME_PATH; ?>admin/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
    <script src="<?php echo THEME_PATH; ?>admin/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
    <script src="<?php echo THEME_PATH; ?>admin/global/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
    <script src="<?php echo THEME_PATH; ?>admin/global/plugins/bootstrap-tagsinput/bootstrap-tagsinput.min.js" type="text/javascript"></script>
    <script src="<?php echo THEME_PATH; ?>admin/global/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.js" type="text/javascript"></script>
    <script src="<?php echo THEME_PATH; ?>admin/global/plugins/jquery-minicolors/jquery.minicolors.min.js" type="text/javascript"></script>
    <script src="<?php echo THEME_PATH; ?>admin/global/plugins/bootstrap-confirmation/bootstrap-confirmation.min.js" type="text/javascript"></script>
    <script src="<?php echo THEME_PATH; ?>admin/global/plugins/jquery-notific8/jquery.notific8.min.js" type="text/javascript"></script>
    <!-- END CORE PLUGINS -->
    <!-- BEGIN THEME GLOBAL SCRIPTS -->
    <script src="<?php echo THEME_PATH; ?>admin/global/scripts/app.min.js" type="text/javascript"></script>
    <script src="<?php echo THEME_PATH; ?>admin/layouts/layout/scripts/layout.min.js" type="text/javascript"></script>
    <script src="<?php echo THEME_PATH; ?>admin/layouts/layout/scripts/demo.min.js" type="text/javascript"></script>
    <script src="<?php echo THEME_PATH; ?>admin/layouts/global/scripts/quick-sidebar.min.js" type="text/javascript"></script>
    <script type="text/javascript" src="<?php echo THEME_PATH; ?>js/jquery-ui.min.js"></script>
    <script type="text/javascript" src="<?php echo THEME_PATH; ?>js/jquery.cookie.js"></script>
    <link rel="stylesheet" href="<?php echo THEME_PATH; ?>js/ui-dialog.css">
    <script type="text/javascript" src="<?php echo THEME_PATH; ?>js/dialog-plus.js"></script>
    <script type="text/javascript" src="<?php echo THEME_PATH; ?>js/jquery.artDialog.js?skin=default"></script>
    <script type="text/javascript" src="<?php echo THEME_PATH; ?>js/validate.js"></script>
    <script type="text/javascript" src="<?php echo THEME_PATH; ?>js/admin.js"></script>
    <script type="text/javascript" src="<?php echo THEME_PATH; ?>js/dayrui.js"></script>
    <script type="text/javascript">
        var dr_login = 1;
        $(function() {
            if ($(".page-bar").height() > 60) {
                top.hideNavShow();
            }
            top.$("#rightMain").attr("url", "<?php echo SELF; ?>"+window.location.search);
            //离开提示失效
            var _t;
            var blnCheckUnload = false;
            window.onunloadcancel = function(){
                clearTimeout(_t);
                top.$('.page-loading').remove();
            }
            window.onbeforeunload = function() {
                if (blnCheckUnload) {
                    setTimeout(function(){_t = setTimeout(onunloadcancel, 0)}, 0);
                    return "<?php echo fc_lang('本页面的数据未被保存'); ?>";
                }
            }
            $("[type='submit'], [type='button']").click(function(){
                blnCheckUnload = false;
            });
            $(":input").change(function(){
                blnCheckUnload = true;
            });

            <?php if (!$mymain || !$sysfield) { ?>top.hideNavHide();<?php } ?>
            $(".table-list tr").last().addClass("dr_border_none");
            $("#pages a").first().addClass("noloading");
            $(".subnav .content-menu span").last().remove();
            //art.dialog.close();
            $("input[name='dr_select']").click(function(){
                $(".dr_select").prop("checked",$(this).attr("checked"));
            });
            // 排序操作
            $('.table thead th').click(function(e) {
                var _class = $(this).attr("class");
                if (_class == undefined) return;
                var _name = $(this).attr("name");
                var _order = '';
                if (_class == "sorting") {
                    _order = 'desc';
                } else if (_class == "sorting_desc") {
                    _order = 'asc';
                } else {
                    _order = 'desc';
                }
                <?php if (isset($param['search']) && $param['search']) $get['search'] = 1; ?>
                var url = "<?php echo dr_url(1, $get); ?>&order="+_name+" "+_order;
                location.href=url;
            });
            // 关闭加载窗口
            top.$('.page-loading').remove();
            $('.onloading, #pages a, .sorting, .sorting_desc, .sorting_asc, :submit').click(function(){
                top.dr_loading();
            });
            $('.sp-choose, .noloading').click(function(){
                top.$('.page-loading').remove();
            });
        });


    </script>
    <style>
    .page-content {
        margin-left:0 !important;
        background-color: #f5f5f5 !important;
    }
    .myfooter {
        background-color: #f5f5f5 !important;
    }
    <?php if (IS_PC) { ?>
    .page-container-bg-solid .page-bar .page-breadcrumb>li>i.fa-circle,.page-content-white .page-bar .page-breadcrumb>li>i.fa-circle {
        top:0px !important;
    }
    <?php } ?>
</style>
</head>
<body class="page-content-white page-footer-fixed" index="nheader" style="background-color: #f5f5f5;">
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content mybody-nheader">
