<?php if ($fn_include = $this->_include("nheader.html")) include($fn_include); ?>

<!-- BEGIN PAGE BAR -->
<div class="page-bar">
    <ul class="page-breadcrumb">
        <li>
            <a href="<?php echo dr_url('home/main'); ?>"><?php echo fc_lang('网站后台'); ?></a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <a class="blue"><?php echo fc_lang('总览'); ?></a>
        </li>
    </ul>
    <div class="page-toolbar">
    </div>
</div>
<!-- END PAGE BAR -->

<!-- BEGIN PAGE TITLE-->
<h3 class="page-title">
    <small></small>
</h3>

<?php if ($admin['usermenu']) { ?>
<div class="row" style="margin-bottom: 20px">
    <div class="col-md-12">
        <div class="admin-usermenu">
            <?php if (is_array($admin['usermenu'])) { $count=count($admin['usermenu']);foreach ($admin['usermenu'] as $t) { ?>
            <a class="btn <?php if ($t['color'] && $t['color']!='default') {  echo $t['color'];  } else { ?>btn-default<?php } ?>" href="<?php echo $t['url']; ?>"> <?php echo $t['name']; ?> </a>
            <?php } } ?>
        </div>
    </div>
</div>
<?php } ?>


<div class="row">
    <div class="col-md-6 col-sm-6">
        <div class="portlet light ">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-cog font-red-sunglo" style="font-size: 20px;"></i>
                    <span class="caption-subject font-red-sunglo"><?php echo fc_lang('系统'); ?></span>
                </div>
            </div>
            <div class="portlet-body">
                <div>
                    <table class="table table-light mtable">
                        <tr>
                            <td width="160" class="mleft" align="right"><?php echo fc_lang('程序版本'); ?>：</td>
                            <td>&nbsp;<a href="<?php echo dr_url('upgrade/index'); ?>">FineCMS&nbsp;v<?php echo DR_VERSION; ?> bulid <?php echo str_replace('.','', DR_UPDATE); ?> </a></td>
                        </tr>
                        <tr>
                            <td class="mleft" align="right"><?php echo fc_lang('服务器IP'); ?>：</td>
                            <td>&nbsp;<?php echo $sip; ?></td>
                        </tr>
                        <tr>
                            <td class="mleft" align="right"><?php echo fc_lang('服务器环境'); ?>：</td>
                            <td>&nbsp;<?php echo $server; ?> </td>
                        </tr>
                        <tr>
                            <td class="mleft" align="right"><?php echo fc_lang('PHP版本'); ?>：</td>
                            <td>&nbsp;PHP<?php echo PHP_VERSION; ?></td>
                        </tr>
                        <tr>
                            <td class="mleft" align="right"><?php echo fc_lang('数据库版本'); ?>：</td>
                            <td>&nbsp;MySql<?php echo $sqlversion; ?></td>
                        </tr>
                        <tr>
                            <td class="mleft" align="right"><?php echo fc_lang('上传最大值'); ?>：</td>
                            <td>&nbsp;<?php echo @ini_get("upload_max_filesize"); ?> </td>
                        </tr>
                        <tr>
                            <td class="mleft" align="right"><?php echo fc_lang('POST最大值'); ?>：</td>
                            <td>&nbsp;<?php echo @ini_get("post_max_size"); ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-sm-6">
        <div class="portlet light ">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-user font-green-sharp" style="font-size: 20px;"></i>
                    <span class="caption-subject font-green-sharp  "><?php echo fc_lang('软件信息'); ?></span>
                </div>
            </div>
            <div class="portlet-body">
                <div>
                    <table class="table table-light mtable">
                        <tr>
                            <td width="160" class="mleft" align="right"><?php echo fc_lang('原作者'); ?>：</td>
                            <td><a href="http://www.dayrui.com/index.php?s=gathering&id=1" target="_blank">李睿</a></td>
                        </tr>
                        <tr>
                            <td class="mleft" align="right"><?php echo fc_lang('软件介绍'); ?>：</td>
                            <td><a href="http://www.finecms.net/" target="_blank">www.finecms.net</a> </td>
                        </tr>
                        <tr>
                            <td class="mleft" align="right"><?php echo fc_lang('交流论坛'); ?>：</td>
                            <td><a href="http://www.finebug.com/finecms" target="_blank">www.finebug.com</a> </td>
                        </tr>
                        <tr>
                            <td class="mleft" align="right"><?php echo fc_lang('使用手册'); ?>：</td>
                            <td><a href="http://www.finecms.net/#doc" target="_blank">www.finecms.net</a> </td>
                        </tr>
                        <tr>
                            <td class="mleft" align="right"><?php echo fc_lang('开发手册'); ?>：</td>
                            <td><a href="https://codeigniter.org.cn/user_guide/" target="_blank">codeigniter.org.cn</a> </td>
                        </tr>
                        <tr>
                            <td class="mleft" align="right"><?php echo fc_lang('QQ用户群'); ?>：</td>
                            <td><a href="//shang.qq.com/wpa/qunwpa?idkey=58824469df9cacea494e0ebad5e9dc69a2e4aded6b8f37ae28adb3f705c2ee9a" target="_blank">644732788（进群验证：finecms）</a> </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>


<?php if ($fn_include = $this->_include("nfooter.html")) include($fn_include); ?>