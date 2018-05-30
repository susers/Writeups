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
            <button type="button" class="btn green btn-sm btn-outline dropdown-toggle" data-toggle="dropdown" aria-expanded="false"> <?php echo fc_lang('操作菜单'); ?>
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
        <div class="portlet-title tabbable-line">
            <ul class="nav nav-tabs" style="float:left;">
                <li class="<?php if ($page==0) { ?>active<?php } ?>">
                    <a href="#tab_0" data-toggle="tab" onclick="$('#mypage').val('0')"> <i class="fa fa-cog"></i> 公众号 </a>
                </li>
            </ul>
        </div>
        <div class="portlet-body">
            <div class="tab-content">

                <div class="tab-pane <?php if ($page==0) { ?>active<?php } ?>" id="tab_0">
                    <div class="form-body">

                        <div class="form-group">
                            <label class="col-md-2 control-label">URL：</label>
                            <div class="col-md-9">
                                <div class="form-control-static"><?php echo SITE_URL; ?>index.php?c=weixin</div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-2 control-label">AppId：</label>
                            <div class="col-md-9">
                                <input class="form-control input-xlarge" type="text" name="data[key]" value="<?php echo $data['key']; ?>" >
                                <span class="help-block">请填写微信公众平台后台的AppId</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label">AppSecret：</label>
                            <div class="col-md-9">
                                <input class="form-control input-xlarge" type="text" name="data[secret]" value="<?php echo $data['secret']; ?>" >
                                <span class="help-block">请填写微信公众平台后台的AppSecret, 只有填写这两项才能管理自定义菜单</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label">Token：</label>
                            <div class="col-md-9">
                                <label><input class="form-control" type="text" name="data[token]" id="sys_key" value="<?php echo $data['token']; ?>"  ></label>
                                <label><button class="btn btn-sm blue" type="button" name="button" onclick="dr_to_key()"> <?php echo fc_lang('生成'); ?> </button></label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label">关联会员模式：</label>
                            <div class="col-md-9">
                                <div class="radio-list">
                                    <label class="radio-inline"><input name="data[user_type]" type="radio" value="1" <?php if ($data['user_type']) { ?>checked<?php } ?> /> <?php echo fc_lang('自动注册'); ?></label>
                                    <label class="radio-inline"><input name="data[user_type]" type="radio" value="0" <?php if (!$data['user_type']) { ?>checked<?php } ?> /> <?php echo fc_lang('绑定会员'); ?></label>
                                </div>
                                <span class="help-block">自动注册模式是用户关注时自动创建一个会员号</span>
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
    function dr_to_key() {
        $.post("<?php echo dr_url('system/syskey'); ?>", function(data){
            $("#sys_key").val(data);
        });
    }
</script>
<?php if ($fn_include = $this->_include("nfooter.html")) include($fn_include); ?>