<?php if ($fn_include = $this->_include("nheader.html")) include($fn_include); ?>
<script type="text/javascript">
function dr_to_key() {
	$.post("<?php echo dr_url('system/syskey'); ?>", function(data){
        $("#sys_key").val(data);
    });
    $.post("<?php echo dr_url('system/referer'); ?>", function(data){
        $("#sys_referer").val(data);
    });
}
</script>
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
    <small></small>
</h3>

<form class="form-horizontal" action="" method="post" id="myform" name="myform">
<input name="page" id="mypage" type="hidden" value="<?php echo $page; ?>" />
    <div class="portlet light bordered myfbody">
        <div class="portlet-body">
            <div class="tab-content">

                <div class="tab-pane <?php if ($page==0) { ?>active<?php } ?>" id="tab_0">
                    <div class="form-body">

                        <div class="form-group">
                            <label class="col-md-2 control-label"><?php echo fc_lang('调试器'); ?>：</label>
                            <div class="col-md-9">
                                <input type="checkbox" name="data[SYS_DEBUG]" value="TRUE" <?php if ($data['SYS_DEBUG']) { ?>checked<?php } ?> data-on-text="<?php echo fc_lang('开启'); ?>" data-off-text="<?php echo fc_lang('关闭'); ?>" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">
                                <span class="help-block"><?php echo fc_lang('用于后台查看程序和SQL执行详情'); ?></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-2 control-label"><?php echo fc_lang('HTTPS模式'); ?>：</label>
                            <div class="col-md-9">
                                <input type="checkbox" name="data[SYS_HTTPS]" value="TRUE" <?php if ($data['SYS_HTTPS']) { ?>checked<?php } ?> data-on-text="<?php echo fc_lang('开启'); ?>" data-off-text="<?php echo fc_lang('关闭'); ?>" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">
                                <span class="help-block"><?php echo fc_lang('开启前需要保证服务器已经支持https协议，并且能够正常访问本网站'); ?></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-2 control-label"><?php echo fc_lang('系统缓存'); ?>：</label>
                            <div class="col-md-9">
                                <input type="checkbox" name="data[SYS_AUTO_CACHE]" value="TRUE" <?php if ($data['SYS_AUTO_CACHE']) { ?>checked<?php } ?> data-on-text="<?php echo fc_lang('开启'); ?>" data-off-text="<?php echo fc_lang('关闭'); ?>" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">
                                <span class="help-block"><?php echo fc_lang('关闭缓存将会大大降低系统运行效率'); ?></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-2 control-label"><?php echo fc_lang('栏目模型唯一'); ?>：</label>
                            <div class="col-md-9">
                                <input type="checkbox" name="data[SYS_CAT_MODULE]" value="TRUE" <?php if ($data['SYS_CAT_MODULE']) { ?>checked<?php } ?> data-on-text="<?php echo fc_lang('开启'); ?>" data-off-text="<?php echo fc_lang('关闭'); ?>" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">
                                <span class="help-block"><?php echo fc_lang('开启之后，创建栏目选择的模型必须和上下级栏目保持一致'); ?></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label"><?php echo fc_lang('后台操作日志'); ?>：</label>
                            <div class="col-md-9">
                                <input type="checkbox" name="data[SYS_LOG]" value="TRUE" <?php if ($data['SYS_LOG']) { ?>checked<?php } ?> data-on-text="<?php echo fc_lang('开启'); ?>" data-off-text="<?php echo fc_lang('关闭'); ?>" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">
                                <span class="help-block"><?php echo fc_lang('多用户操作后台建议打开日志功能'); ?></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label"><?php echo fc_lang('后台登录验证码'); ?>：</label>
                            <div class="col-md-9">
                                <input type="checkbox" name="data[SITE_ADMIN_CODE]" value="TRUE" <?php if ($data['SITE_ADMIN_CODE']) { ?>checked<?php } ?> data-on-text="<?php echo fc_lang('开启'); ?>" data-off-text="<?php echo fc_lang('关闭'); ?>" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">

                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label"><?php echo fc_lang('后台数据分页条数'); ?>：</label>
                            <div class="col-md-9">
                                <label><input class="form-control" type="text" name="data[SITE_ADMIN_PAGESIZE]" value="<?php echo $data['SITE_ADMIN_PAGESIZE']; ?>" ></label>
                                <span class="help-block"><?php echo fc_lang('例如文章每页显示的数量控制'); ?></span>
                            </div>
                        </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label"><?php echo fc_lang('系统邮箱'); ?>：</label>
                                <div class="col-md-9">
                                    <label><input class="form-control" type="text" name="data[SYS_EMAIL]" value="<?php echo $data['SYS_EMAIL']; ?>" ></label>
                                    <span class="help-block"><?php echo fc_lang('用于接收系统发送的通知信息的可用邮箱'); ?></span>
                                </div>
                            </div>



                        <div class="form-group">
                            <label class="col-md-2 control-label"><?php echo fc_lang('安全密钥'); ?>：</label>
                            <div class="col-md-9">
                                <label><input class="form-control" type="text" name="data[SYS_KEY]" id="sys_key" value="<?php if ($data['SYS_KEY']) { ?>***<?php } ?>"  ></label>
                                <label><button class="btn btn-sm blue" type="button" name="button" onclick="dr_to_key()"> <?php echo fc_lang('重新生成'); ?> </button></label>
                                <span class="help-block"><?php echo fc_lang('密钥建议定期更换'); ?></span>
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
    </div>
</form>
<script type="text/javascript">
<!--
function memcache_test() {
	$("#memcache").val('Loading');
	$.get("<?php echo dr_url('system/memcache'); ?>&"+Math.random(),function(data){
		alert(data);
		$("#memcache").val('<?php echo fc_lang('测试'); ?>');
	})
}
//-->
</script>
<?php if ($fn_include = $this->_include("nfooter.html")) include($fn_include); ?>