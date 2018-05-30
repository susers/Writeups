<?php if ($fn_include = $this->_include("nheader.html")) include($fn_include); ?>
<script type="text/javascript">
$(function() {
	<?php if ($result == 1) { ?>
	dr_tips('<?php echo fc_lang("操作成功"); ?>', 3, 1);
    <?php } else if ($result) { ?>
    dr_tips('<?php echo $result; ?>', 3);
    <?php }  if (empty($data['SITE_IMAGE_WATERMARK'])) { ?>
    $('.dr_image').hide();
    <?php } else { ?>
	dr_set_mw_type(<?php echo intval($data['SITE_IMAGE_TYPE']); ?>);
    <?php }  if (empty($data['SITE_CLOSE'])) { ?>
        $('.dr_close_msg').hide();
    <?php } else { ?>
        $('.dr_close_msg').show();
    <?php } ?>
    dr_theme(<?php echo $is_theme; ?>);
});
function dr_form_check() {
	if (d_required('name')) return false;
	if (d_isdomain('domain')) return false;
	return true;
}
function dr_set_mw_type(id) {
	$(".dr_mw_1").hide();
	$(".dr_mw_0").hide();
	$(".dr_mw_"+id).show();
}
function dr_theme(id) {
    if (id == 1) {
        $("#dr_theme_html").html($("#dr_web").html());
    } else {
        $("#dr_theme_html").html($("#dr_local").html());
    }
}
</script>
<div id="dr_local" style="display: none">
    <label class="col-md-2 control-label"><?php echo fc_lang('主题风格'); ?>：</label>
    <div class="col-md-9">
        <label><select class="form-control" name="data[SITE_THEME]">
            <option value="default"> -- </option>
            <?php if (is_array($theme)) { $count=count($theme);foreach ($theme as $t) { ?>
            <option<?php if ($t==$data['SITE_THEME']) { ?> selected=""<?php } ?> value="<?php echo $t; ?>"><?php echo $t; ?></option>
            <?php } } ?>
        </select></label>
        <span class="help-block"><?php echo fc_lang('位于网站主站根目录下：根目录/statics/风格名称/'); ?></span>
    </div>
</div>
<div id="dr_web" style="display: none">
    <label class="col-md-2 control-label"><?php echo fc_lang('远程资源'); ?>：</label>
    <div class="col-md-9">
        <input class="form-control  input-xlarge" type="text" placeholder="http://" name="data[SITE_THEME]" value="<?php echo strpos($data['SITE_THEME'], 'http') === 0 ? $data['SITE_THEME'] : ''; ?>">
        <span class="help-block"><?php echo fc_lang('网站将调用此地址的css,js,图片等静态资源'); ?></span>
    </div>
</div>

<form action="" class="form-horizontal" method="post" name="myform" id="myform" onsubmit="return dr_form_check()">
    <div class="page-bar">
        <ul class="page-breadcrumb mylink">
            <?php echo $menu['link']; ?>

        </ul>
        <ul class="page-breadcrumb myname">
            <?php echo $menu['name']; ?>
        </ul>
        <div class="page-toolbar">
            <div class="btn-group pull-right">
                <button type="button" class="btn green btn-sm btn-outline dropdown-toggle" data-toggle="dropdown" aria-expanded="false" data-hover="dropdown"> <?php echo fc_lang('操作菜单'); ?>
                    <i class="fa fa-angle-down"></i>
                </button>
                <ul class="dropdown-menu pull-right" role="menu">
                    <?php if (is_array($menu['quick'])) { $count=count($menu['quick']);foreach ($menu['quick'] as $t) { ?>
                    <li>
                        <a href="<?php echo $t['url']; ?>"><?php echo $t['icon'];  echo $t['name']; ?></a>
                    </li>
                    <?php } } ?>
                    <li class="divider"> </li>
                    <li>
                        <a href="javascript:window.location.reload();">
                            <i class="icon-refresh"></i> <?php echo fc_lang('刷新页面'); ?></a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <h3 class="page-title">
        <small><?php echo fc_lang('站点是系统的核心部分，各个站点数据独立，可以设置站点分库管理'); ?></small>
    </h3>

    <div class="portlet light bordered myfbody">
        <div class="portlet-title tabbable-line">
            <input type="hidden" name="page" id="mypage" value="<?php echo $page; ?>">
            <ul class="nav nav-tabs" style="float:left;">
                <li class="<?php if ($page==0) { ?>active<?php } ?>">
                    <a href="#tab_0" data-toggle="tab" onclick="$('#mypage').val('0')"> <i class="fa fa-cog"></i> <?php echo fc_lang('基本设置'); ?> </a>
                </li>
                <li class="<?php if ($page==1) { ?>active<?php } ?>">
                    <a href="#tab_1" data-toggle="tab" onclick="$('#mypage').val('1')"> <i class="fa fa-share-alt-square"></i> <?php echo fc_lang('域名及路径'); ?> </a>
                </li>
                <li class="<?php if ($page==2) { ?>active<?php } ?>">
                    <a href="#tab_2" data-toggle="tab" onclick="$('#mypage').val('2')"> <i class="fa fa-mobile"></i> <?php echo fc_lang('移动端'); ?> </a>
                </li>
                <li class="<?php if ($page==3) { ?>active<?php } ?>">
                    <a href="#tab_3" data-toggle="tab" onclick="$('#mypage').val('3')"> <i class="fa fa-internet-explorer"></i> <?php echo fc_lang('SEO设置'); ?> </a>
                </li>
                <li class="<?php if ($page==4) { ?>active<?php } ?>">
                    <a href="#tab_4" data-toggle="tab" onclick="$('#mypage').val('4')"> <i class="fa fa-picture-o"></i> <?php echo fc_lang('图片水印'); ?> </a>
                </li>
            </ul>
        </div>
        <div class="portlet-body">
            <div class="tab-content">

                <div class="tab-pane <?php if ($page==0) { ?>active<?php } ?>" id="tab_0">
                    <div class="form-body">


                        <div class="form-group">
                            <label class="col-md-2 control-label" style="padding-top: 10px"><?php echo fc_lang('网站状态'); ?>：</label>
                            <div class="col-md-9">
                                <div class="radio-list">
                                    <label class="radio-inline"><input type="radio" onclick="$('.dr_close_msg').hide()" name="data[SITE_CLOSE]" value="0" <?php if (empty($data['SITE_CLOSE'])) { ?>checked<?php } ?> /> <?php echo fc_lang('开启'); ?></label>
                                    <label class="radio-inline"><input type="radio" onclick="$('.dr_close_msg').show()" name="data[SITE_CLOSE]" value="1" <?php if ($data['SITE_CLOSE']) { ?>checked<?php } ?> /> <?php echo fc_lang('关闭'); ?></label>
                                </div>
                                <span class="help-block"><?php echo fc_lang('当关闭网站时，除管理员之外的用户将无法访问（静态页面除外）'); ?></span>
                            </div>
                        </div>
                        <div class="form-group dr_close_msg">
                            <label class="col-md-2 control-label"><?php echo fc_lang('关闭理由'); ?>：</label>
                            <div class="col-md-9">
                                <textarea class="form-control" style="height:100px" name="data[SITE_CLOSE_MSG]"><?php echo $data['SITE_CLOSE_MSG'] ? $data['SITE_CLOSE_MSG'] : '网站升级中....'; ?></textarea>

                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label"><?php echo fc_lang('网站名称'); ?>：</label>
                            <div class="col-md-9">
                                <label><input class="form-control" type="text" name="data[SITE_NAME]" id="dr_name" value="<?php echo $data['SITE_NAME']; ?>"></label>
                                <span class="help-block"><?php echo fc_lang('例如：FineCMS官方站'); ?></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label"><?php echo fc_lang('时间格式'); ?>：</label>
                            <div class="col-md-9">
                                <label><input class="form-control" type="text" name="data[SITE_TIME_FORMAT]" value="<?php echo $data['SITE_TIME_FORMAT']; ?>"></label>
                                <span class="help-block"><?php echo fc_lang('网站时间显示格式与date函数一致，默认Y-m-d H:i:s'); ?></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label"><?php echo fc_lang('网站语言'); ?>：</label>
                            <div class="col-md-9">
                                <label><select class="form-control" name="data[SITE_LANGUAGE]">
                                    <option value="zh-cn"> -- </option>
                                    <?php if (is_array($lang)) { $count=count($lang);foreach ($lang as $t) { ?>
                                    <option<?php if ($t==$data['SITE_LANGUAGE']) { ?> selected=""<?php } ?> value="<?php echo $t; ?>"><?php echo $t; ?></option>
                                    <?php } } ?>
                                </select></label>
                                <span class="help-block"><?php echo fc_lang('网站核心目录及各个模型或应用目录：/language/语言名称/'); ?></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label" style="padding-top:10px"><?php echo fc_lang('风格模式'); ?>：</label>
                            <div class="col-md-9">
                                <div class="radio-list">
                                    <label class="radio-inline"><input type="radio" onclick="dr_theme(1)" name="theme" value="1" <?php if ($is_theme) { ?>checked<?php } ?> /> <?php echo fc_lang('远程地址'); ?></label>
                                    <label class="radio-inline"><input type="radio" onclick="dr_theme(0)" name="theme" value="0" <?php if (!$is_theme) { ?>checked<?php } ?> /> <?php echo fc_lang('本站资源'); ?></label>
                                </div>
                                <span class="help-block"><?php echo fc_lang('可以将js,css,图片存储在远程地址或者本地statics目录之中'); ?></span>
                            </div>
                        </div>
                        <div class="form-group" id="dr_theme_html">

                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label"><?php echo fc_lang('模板目录'); ?>：</label>
                            <div class="col-md-9">
                                <label><select class="form-control" name="data[SITE_TEMPLATE]">
                                    <option value="default"> -- </option>
                                    <?php if (is_array($template_path)) { $count=count($template_path);foreach ($template_path as $t) { ?>
                                    <option<?php if ($t==$data['SITE_TEMPLATE']) { ?> selected=""<?php } ?> value="<?php echo $t; ?>"><?php echo $t; ?></option>
                                    <?php } } ?>
                                </select></label>
                                <span class="help-block"><?php echo fc_lang('位于网站主站根目录下：/templates/pc或mobile/目录名称/'); ?></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label"><?php echo fc_lang('网站时区'); ?>：</label>
                            <div class="col-md-9">
                                <label><select class="form-control" name="data[SITE_TIMEZONE]">
                                    <option value=""> -- </option>
                                    <option value="-12" <?php if ($data['SITE_TIMEZONE']=="-12") { ?>selected<?php } ?>>(GMT -12:00)</option>
                                    <option value="-11" <?php if ($data['SITE_TIMEZONE']=="-11") { ?>selected<?php } ?>>(GMT -11:00)</option>
                                    <option value="-10" <?php if ($data['SITE_TIMEZONE']=="-10") { ?>selected<?php } ?>>(GMT -10:00)</option>
                                    <option value="-9" <?php if ($data['SITE_TIMEZONE']=="-9") { ?>selected<?php } ?>>(GMT -09:00)</option>
                                    <option value="-8" <?php if ($data['SITE_TIMEZONE']=="-8") { ?>selected<?php } ?>>(GMT -08:00)</option>
                                    <option value="-7" <?php if ($data['SITE_TIMEZONE']=="-7") { ?>selected<?php } ?>>(GMT -07:00)</option>
                                    <option value="-6" <?php if ($data['SITE_TIMEZONE']=="-6") { ?>selected<?php } ?>>(GMT -06:00)</option>
                                    <option value="-5" <?php if ($data['SITE_TIMEZONE']=="-5") { ?>selected<?php } ?>>(GMT -05:00)</option>
                                    <option value="-4" <?php if ($data['SITE_TIMEZONE']=="-4") { ?>selected<?php } ?>>(GMT -04:00)</option>
                                    <option value="-3.5" <?php if ($data['SITE_TIMEZONE']=="-3.5") { ?>selected<?php } ?>>(GMT -03:30)</option>
                                    <option value="-3" <?php if ($data['SITE_TIMEZONE']=="-3") { ?>selected<?php } ?>>(GMT -03:00)</option>
                                    <option value="-2" <?php if ($data['SITE_TIMEZONE']=="-2") { ?>selected<?php } ?>>(GMT -02:00)</option>
                                    <option value="-1" <?php if ($data['SITE_TIMEZONE']=="-1") { ?>selected<?php } ?>>(GMT -01:00)</option>
                                    <option value="0" <?php if ($data['SITE_TIMEZONE']=="0") { ?>selected<?php } ?>>(GMT)</option>
                                    <option value="1" <?php if ($data['SITE_TIMEZONE']=="1") { ?>selected<?php } ?>>(GMT +01:00)</option>
                                    <option value="2" <?php if ($data['SITE_TIMEZONE']=="2") { ?>selected<?php } ?>>(GMT +02:00)</option>
                                    <option value="3" <?php if ($data['SITE_TIMEZONE']=="3") { ?>selected<?php } ?>>(GMT +03:00)</option>
                                    <option value="3.5" <?php if ($data['SITE_TIMEZONE']=="3.5") { ?>selected<?php } ?>>(GMT +03:30)</option>
                                    <option value="4" <?php if ($data['SITE_TIMEZONE']=="4") { ?>selected<?php } ?>>(GMT +04:00)</option>
                                    <option value="4.5" <?php if ($data['SITE_TIMEZONE']=="4.5") { ?>selected<?php } ?>>(GMT +04:30)</option>
                                    <option value="5" <?php if ($data['SITE_TIMEZONE']=="5") { ?>selected<?php } ?>>(GMT +05:00)</option>
                                    <option value="5.5" <?php if ($data['SITE_TIMEZONE']=="5.5") { ?>selected<?php } ?>>(GMT +05:30)</option>
                                    <option value="5.75" <?php if ($data['SITE_TIMEZONE']=="5.75") { ?>selected<?php } ?>>(GMT +05:45)</option>
                                    <option value="6" <?php if ($data['SITE_TIMEZONE']=="6") { ?>selected<?php } ?>>(GMT +06:00)</option>
                                    <option value="6.5" <?php if ($data['SITE_TIMEZONE']=="6.6") { ?>selected<?php } ?>>(GMT +06:30)</option>
                                    <option value="7" <?php if ($data['SITE_TIMEZONE']=="7") { ?>selected<?php } ?>>(GMT +07:00)</option>
                                    <option value="8" <?php if ($data['SITE_TIMEZONE']=="" || $data['SITE_TIMEZONE']=="8") { ?>selected<?php } ?>>(GMT +08:00)</option>
                                    <option value="9" <?php if ($data['SITE_TIMEZONE']=="9") { ?>selected<?php } ?>>(GMT +09:00)</option>
                                    <option value="9.5" <?php if ($data['SITE_TIMEZONE']=="9.5") { ?>selected<?php } ?>>(GMT +09:30)</option>
                                    <option value="10" <?php if ($data['SITE_TIMEZONE']=="10") { ?>selected<?php } ?>>(GMT +10:00)</option>
                                    <option value="11" <?php if ($data['SITE_TIMEZONE']=="11") { ?>selected<?php } ?>>(GMT +11:00)</option>
                                    <option value="12" <?php if ($data['SITE_TIMEZONE']=="12") { ?>selected<?php } ?>>(GMT +12:00)</option>
                                </select></label>
                                <span class="help-block"><?php echo fc_lang('例如中国地区选择“GMT +08:00”表示东八区'); ?></span>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="tab-pane  <?php if ($page==1) { ?>active<?php } ?>" id="tab_1">
                    <div class="form-body">
                        <div class="form-group">
                            <label class="col-md-2 control-label"><?php echo fc_lang('网站域名'); ?>：</label>
                            <div class="col-md-9">
                                <label><input class="form-control" type="text" name="domain" id="dr_domain" value="<?php echo $data['SITE_DOMAIN']; ?>"></label>
                                <span class="help-block" id="dr_domain_tips"><?php echo fc_lang('例如：www.finecms.net'); ?></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label"><?php echo fc_lang('其他域名'); ?>：</label>
                            <div class="col-md-9">
                                <textarea class="form-control" style="height:150px" name="data[SITE_DOMAINS]"><?php echo str_replace(',',PHP_EOL, $data['SITE_DOMAINS']); ?></textarea>
                                <span class="help-block"><?php echo fc_lang('当前站点支持绑定多个域名，它们将会301到主域名，域名之间以回车符分隔（请勿与其他站点的域名重复）'); ?></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-2 control-label"><?php echo fc_lang('URL唯一'); ?>：</label>
                            <div class="col-md-9">
                                <input type="checkbox" name="data[SITE_URL_301]" value="1" <?php if ($data['SITE_URL_301']) { ?>checked<?php } ?> data-on-text="<?php echo fc_lang('启用'); ?>" data-off-text="<?php echo fc_lang('关闭'); ?>" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">
                                <span class="help-block"><?php echo fc_lang('启用将会使全站URL保持唯一，非当前URL自动301定向'); ?></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label"><?php echo fc_lang('URL规则'); ?>：</label>
                            <div class="col-md-9">
                                <label>
                                    <select class="form-control" name="data[SITE_REWRITE]">
                                        <option value="0"> -- </option>
                                        <?php $rt_u = $this->list_tag("action=cache name=urlrule  return=u"); if ($rt_u) extract($rt_u); $count_u=count($return_u); if (is_array($return_u)) { foreach ($return_u as $key_u=>$u) {  if ($u['type']==4) { ?><option value="<?php echo $u['id']; ?>" <?php if ($u['id']==$data['SITE_REWRITE']) { ?>selected<?php } ?>> <?php echo $u['name']; ?> </option><?php }  } } ?>
                                    </select>
                                </label>
                                <label>&nbsp;&nbsp;<a href="<?php echo dr_url('urlrule/index'); ?>" style="color:blue !important"><?php echo fc_lang('[URL规则管理]'); ?></a></label>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="tab-pane  <?php if ($page==2) { ?>active<?php } ?>" id="tab_2">
                    <div class="form-body">
                        <div class="form-group">
                            <label class="col-md-2 control-label"><?php echo fc_lang('自动识别'); ?>：</label>
                            <div class="col-md-9">
                                <input type="checkbox" name="data[SITE_MOBILE_OPEN]" value="1" <?php if ($data['SITE_MOBILE_OPEN']) { ?>checked<?php } ?> data-on-text="<?php echo fc_lang('开启'); ?>" data-off-text="<?php echo fc_lang('关闭'); ?>" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">
                                <span class="help-block"><?php echo fc_lang('开启后将自动识别移动端并强制定向到此域名'); ?></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label"><?php echo fc_lang('移动端域名'); ?>：</label>
                            <div class="col-md-9">
                                <label><input class="form-control" type="text" name="data[SITE_MOBILE]" value="<?php echo $data['SITE_MOBILE']; ?>"></label>
                                <?php if ($data['SITE_MOBILE'] == $data['SITE_DOMAIN']) { ?>
                                <span class="help-block">此域名不能与站点域名相同</span>
                                <?php } else { ?>
                                <span class="help-block"><?php echo fc_lang('格式：test.finecms.net'); ?></span>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label"><?php echo fc_lang('移动端模板路径'); ?>：</label>
                            <div class="col-md-9">
                                <div class="form-control-static"><label><?php echo fc_lang('/templates/mobile/'.SITE_TEMPLATE.'/，如果不存在时自动加载电脑端模板'); ?></label></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane <?php if ($page==3) { ?>active<?php } ?> " id="tab_3">
                    <div class="form-body">
                        <div class="form-group">
                            <label class="col-md-2 control-label"><?php echo fc_lang('SEO连接符'); ?>：</label>
                            <div class="col-md-9">
                                <label><input class="form-control" type="text" name="data[SITE_SEOJOIN]" value="<?php echo $data['SITE_SEOJOIN'] ? $data['SITE_SEOJOIN'] : '_'; ?>"></label>
                                <span class="help-block"><?php echo fc_lang('默认为"_"，如：文章标题[连接符]栏目名称[连接符]模型名称'); ?></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label"><?php echo fc_lang('SEO标题'); ?>：</label>
                            <div class="col-md-9">
                                <input class="form-control" type="text" name="data[SITE_TITLE]" value="<?php echo $data['SITE_TITLE']; ?>">

                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label"><?php echo fc_lang('SEO关键字'); ?>：</label>
                            <div class="col-md-9">
                                <input class="form-control" type="text" name="data[SITE_KEYWORDS]" value="<?php echo $data['SITE_KEYWORDS']; ?>">

                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label"><?php echo fc_lang('SEO描述信息'); ?>：</label>
                            <div class="col-md-9">
                                <input class="form-control" type="text" name="data[SITE_DESCRIPTION]" value="<?php echo $data['SITE_DESCRIPTION']; ?>">

                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane  <?php if ($page==4) { ?>active<?php } ?>" id="tab_4">
                    <div class="form-body">

                        <div class="form-group">
                            <label class="col-md-2 control-label"><?php echo fc_lang('保持原始的纵横比例'); ?>：</label>
                            <div class="col-md-9">
                                <input type="checkbox" name="data[SITE_IMAGE_RATIO]" value="1" <?php if ($data['SITE_IMAGE_RATIO']) { ?>checked<?php } ?> data-on-text="<?php echo fc_lang('开启'); ?>" data-off-text="<?php echo fc_lang('关闭'); ?>" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">
                                <span class="help-block"><?php echo fc_lang('指定是否在缩放或使用硬值的时候使图像保持原始的纵横比例'); ?></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label" style="padding-top: 10px;"><?php echo fc_lang('图片水印总开关'); ?>：</label>
                            <div class="col-md-9">
                                <div class="radio-list">
                                    <label class="radio-inline"><input type="radio" onclick="$('.dr_image').show();dr_set_mw_type($('.dtype:checked').val());" name="data[SITE_IMAGE_WATERMARK]" value="1" <?php if ($data['SITE_IMAGE_WATERMARK']) { ?>checked<?php } ?> /> <?php echo fc_lang('开启'); ?></label>
                                    <label class="radio-inline"><input type="radio" onclick="$('.dr_image').hide();" name="data[SITE_IMAGE_WATERMARK]" value="0" <?php if (empty($data['SITE_IMAGE_WATERMARK'])) { ?>checked<?php } ?> /> <?php echo fc_lang('关闭'); ?></label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group dr_image">
                            <label class="col-md-2 control-label"><?php echo fc_lang('远程附件水印'); ?>：</label>
                            <div class="col-md-9">
                                <input type="checkbox" name="data[SITE_IMAGE_REMOTE]" value="1" <?php if ($data['SITE_IMAGE_REMOTE']) { ?>checked<?php } ?> data-on-text="<?php echo fc_lang('开启'); ?>" data-off-text="<?php echo fc_lang('关闭'); ?>" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">
                                <span class="help-block"><?php echo fc_lang('打开远程附件水印会降低服务器性能，建议远程附件不加水印'); ?></span>
                            </div>
                        </div>
                        <div class="form-group dr_image">
                            <label class="col-md-2 control-label"><?php echo fc_lang('文章内容图片水印'); ?>：</label>
                            <div class="col-md-9">
                                <input type="checkbox" name="data[SITE_IMAGE_CONTENT]" value="1" <?php if ($data['SITE_IMAGE_CONTENT']) { ?>checked<?php } ?> data-on-text="<?php echo fc_lang('开启'); ?>" data-off-text="<?php echo fc_lang('关闭'); ?>" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">
                                <span class="help-block"><?php echo fc_lang('开启后文章编辑器内容中上传的图片将会采用动态水印模式'); ?></span>
                            </div>
                        </div>
                        <div class="form-group dr_image">
                            <label class="col-md-2 control-label"><?php echo fc_lang('对齐方式'); ?>：</label>
                            <div class="col-md-9">
                                <label><select class="form-control" name="data[SITE_IMAGE_VRTALIGN]">
                                    <?php if (is_array($wm_vrt_alignment)) { $count=count($wm_vrt_alignment);foreach ($wm_vrt_alignment as $t) { ?>
                                    <option<?php if ($t==$data['SITE_IMAGE_VRTALIGN']) { ?> selected=""<?php } ?> value="<?php echo $t; ?>"><?php echo $t; ?></option>
                                    <?php } } ?>
                                </select> </label>
                                <label> <select class="form-control" name="data[SITE_IMAGE_HORALIGN]">
                                    <?php if (is_array($wm_hor_alignment)) { $count=count($wm_hor_alignment);foreach ($wm_hor_alignment as $t) { ?>
                                    <option<?php if ($t==$data['SITE_IMAGE_HORALIGN']) { ?> selected=""<?php } ?> value="<?php echo $t; ?>"><?php echo $t; ?></option>
                                    <?php } } ?>
                                </select></label>
                            </div>
                        </div>
                        <div class="form-group dr_image">
                            <label class="col-md-2 control-label"><?php echo fc_lang('偏移量设置'); ?>：</label>
                            <div class="col-md-9">
                                <label> <input class="form-control" type="text" name="data[SITE_IMAGE_VRTOFFSET]" value="<?php echo $data['SITE_IMAGE_VRTOFFSET']; ?>" /></label>
                                <label> <input class="form-control" type="text" name="data[SITE_IMAGE_HOROFFSET]" value="<?php echo $data['SITE_IMAGE_HOROFFSET']; ?>" /></label>
                                <span class="help-block"><?php echo fc_lang('这里可以设置水印图片/文字的偏移量来校正图片水印位置'); ?></span>
                            </div>
                        </div>
                        <div class="form-group dr_image">
                            <label class="col-md-2 control-label"><?php echo fc_lang('水印方式'); ?>：</label>
                            <div class="col-md-9">
                                <div class="radio-list">
                                    <label class="radio-inline"><input type="radio" class="dtype" name="data[SITE_IMAGE_TYPE]" value="1" onclick="dr_set_mw_type(1)" <?php if ($data['SITE_IMAGE_TYPE']) { ?>checked<?php } ?> /> <?php echo fc_lang('图片水印'); ?></label>
                                    <label class="radio-inline"><input type="radio" class="dtype" name="data[SITE_IMAGE_TYPE]" value="0" onclick="dr_set_mw_type(0)" <?php if (empty($data['SITE_IMAGE_TYPE'])) { ?>checked<?php } ?> /> <?php echo fc_lang('文字水印'); ?></label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group dr_image dr_mw_1" style="display:none">
                            <label class="col-md-2 control-label"><?php echo fc_lang('选择水印图片'); ?>：</label>
                            <div class="col-md-9">
                                <label><select class=" form-control" name="data[SITE_IMAGE_OVERLAY]">
                                    <?php if (is_array($wm_opacity)) { $count=count($wm_opacity);foreach ($wm_opacity as $t) { ?>
                                    <option<?php if ($t==$data['SITE_IMAGE_OVERLAY']) { ?> selected=""<?php } ?> value="<?php echo $t; ?>"><?php echo $t; ?></option>
                                    <?php } } ?>
                                </select></label>
                                <span class="help-block"><?php echo fc_lang('图片目录：“根目录/statics/watermark/”，必须是png格式的图片'); ?></span>
                            </div>
                        </div>
                        <div class="form-group dr_mw_1 dr_image" style="display:none">
                            <label class="col-md-2 control-label"><?php echo fc_lang('图像不透明度'); ?>：</label>
                            <div class="col-md-9">
                                <label> <input class="form-control" type="text" name="data[SITE_IMAGE_OPACITY]" value="<?php echo $data['SITE_IMAGE_OPACITY']; ?>" /></label>
                                <span class="help-block"><?php echo fc_lang('这将使水印模糊化，从而不会掩盖住底层原始图片的细节，通常设置为50'); ?></span>
                            </div>
                        </div>
                        <div class="form-group dr_mw_0 dr_image" style="display:none">
                            <label class="col-md-2 control-label"><?php echo fc_lang('文字字体'); ?>：</label>
                            <div class="col-md-9">
                                <label> <?php if ($wm_font_path) { ?>
                                    <select class="form-control" name="data[SITE_IMAGE_FONT]">
                                        <?php if (is_array($wm_font_path)) { $count=count($wm_font_path);foreach ($wm_font_path as $t) { ?>
                                        <option<?php if ($t==$data['SITE_IMAGE_FONT']) { ?> selected=""<?php } ?> value="<?php echo $t; ?>"><?php echo $t; ?></option>
                                        <?php } } ?>
                                    </select>
                                    <?php } ?></label>
                                <span class="help-block"><?php echo fc_lang('字体目录：“根目录/statics/watermark/”，必须是ttf格式的字体文件'); ?></span>
                            </div>
                        </div>
                        <div class="form-group dr_mw_0 dr_image" style="display:none">
                            <label class="col-md-2 control-label"><?php echo fc_lang('字体颜色'); ?>：</label>
                            <div class="col-md-9">
                                <label> <?php echo dr_field_input('SITE_IMAGE_COLOR', 'Color', array('option'=>array('value'=>$data['SITE_IMAGE_COLOR']))); ?></label>

                            </div>
                        </div>
                        <div class="form-group dr_mw_0 dr_image" style="display:none">
                            <label class="col-md-2 control-label"><?php echo fc_lang('字体大小'); ?>：</label>
                            <div class="col-md-9">
                                <label> <input class="form-control" type="text" name="data[SITE_IMAGE_SIZE]" value="<?php echo $data['SITE_IMAGE_SIZE']; ?>" /></label>

                            </div>
                        </div>
                        <div class="form-group dr_mw_0 dr_image" style="display:none">
                            <label class="col-md-2 control-label"><?php echo fc_lang('水印文字'); ?>：</label>
                            <div class="col-md-9">
                                <label> <input class="form-control" type="text" name="data[SITE_IMAGE_TEXT]" value="<?php echo $data['SITE_IMAGE_TEXT']; ?>" /></label>

                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="myfooter">
        <div class="row">
            <div class="portlet-body form">
                <div class="form-body">
                    <div class="form-actions">
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <button type="submit" class="btn green"> <i class="fa fa-save"></i> <?php echo fc_lang('保存'); ?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<?php if ($fn_include = $this->_include("nfooter.html")) include($fn_include); ?>