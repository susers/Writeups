<?php if ($fn_include = $this->_include("nheader.html")) include($fn_include); ?>
<script type="text/javascript">
function dr_confirm_move() {
	var d = top.dialog({
		title: lang["tips"],
		fixed: true,
		content: '<img src="/statics/js/skins/icons/question.png"> <?php echo fc_lang("您确定要这样操作吗？"); ?>',
		okValue: lang['ok'],
		ok: function () {
			$('#action').val('move');
			var _data = $("#myform").serialize();
			var _url = window.location.href;
			if ((_data.split('ids')).length-1 <= 0) {
				d.close().remove();
				dr_tips(lang['select_null'], 2);
				return true;
			}
			// 将表单数据ajax提交验证
			$.ajax({type: "POST",dataType:"json", url: _url, data: _data,
				success: function(data) {
					d.close().remove();
					//验证成功
					if (data.status == 1) {
						dr_tips(data.code, 3, 1);
						$("input[name='ids[]']:checkbox:checked").each(function(){
							$.post("<?php echo $html_url; ?>c=show&m=create_html&id="+$(this).val(), {}, function(){});
						});
						$.post("<?php echo $html_url; ?>c=home&m=create_list_html&id="+$('#move_id').val(), {}, function(){});
						setTimeout('window.location.reload(true)', 3000); // 刷新页
						return true;
					} else {
						dr_tips(data.code, 3, 2);
						return true;
					}
				},
				error: function(HttpRequest, ajaxOptions, thrownError) {
					alert(HttpRequest.responseText);
				}
			});
		},
		cancelValue: lang['cancel'],
		cancel: function () {}
	});
	d.show();
}
function dr_status(id, v) {
	var title = "";
	if (v == 9) {
		title = "<font color=red><b><?php echo fc_lang('您确定要将它关闭吗？'); ?></b></font>";
	} else {
		title = "<font color=blue><b><?php echo fc_lang('您确定要将它开启吗？'); ?></b></font>";
	}
	var d = top.dialog({
		title: lang["tips"],
		fixed: true,
		content: '<img src="/statics/js/skins/icons/question.png"> '+title,
		okValue: lang['ok'],
		ok: function () {
			$.ajax({type: "POST",dataType:"json", url: "<?php echo dr_url('content/status'); ?>&mid=<?php echo $mid; ?>&id="+id+"&v="+v, success: function(data) {
				//验证成功
				if (data.status == 1) {
					dr_tips(data.code, 3, 1);

					setTimeout('window.location.reload(true)', 3000); // 刷新页
				} else {
					dr_tips(data.code);
				}
			},
				error: function(HttpRequest, ajaxOptions, thrownError) {
					alert(HttpRequest.responseText);
				}
			});
			return true
		},
		cancelValue: lang['cancel'],
		cancel: function () {}
	});
	d.show();
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
<div class="mytopsearch">
	<form method="post" class="row" action="" name="searchform" id="searchform">
		<input name="search" id="search" type="hidden" value="1" />
		<div class="col-md-12">
			<label style="padding-right: 5px;"><?php echo $select2; ?></label>
			<label style="padding-right: 10px;"><i class="fa"></i></label>
			<label>
				<select name="data[field]" class="form-control">
					<option value="id" <?php if ($param['field']=='id') { ?>selected<?php } ?>>Id</option>
					<?php if (is_array($field)) { $count=count($field);foreach ($field as $t) {  if ($t['ismain'] && $t['fieldname'] != 'inputtime' && $t['fieldname'] != 'updatetime') { ?>
					<option value="<?php echo $t['fieldname']; ?>" <?php if ($param['field']==$t['fieldname']) { ?>selected<?php } ?>><?php echo $t['name']; ?></option>
					<?php }  } } ?>
				</select>
			</label>
			<label><i class="fa fa-caret-right"></i></label>
			<label style="padding-right: 20px;"><input type="text" class="form-control" placeholder="<?php echo fc_lang('多个Id可以用“,”分隔'); ?>" value="<?php echo $param['keyword']; ?>" name="data[keyword]" /></label>
			<label><?php echo fc_lang('录入时间'); ?> ：</label>
			<label><?php echo dr_field_input('start', 'Date', array('option'=>array('format'=>'Y-m-d','width'=>'110')), (int)$param['start']); ?></label>
			<label><i class="fa fa-minus"></i></label>
			<label style="margin-right:10px"><?php echo dr_field_input('end', 'Date', array('option'=>array('format'=>'Y-m-d','width'=>'110')), (int)$param['end']); ?></label>
			<label><button type="submit" class="btn green btn-sm" name="submit" > <i class="fa fa-search"></i> <?php echo fc_lang('搜索'); ?></button></label>
		</div>
	</form>
</div>

<form action="" method="post" name="myform" id="myform">
	<input name="action" id="action" type="hidden" value="" />
	<div class="portlet mylistbody">
		<div class="portlet-body">
			<div class="table-scrollable">

				<table class="mytable table table-striped table-bordered table-hover table-checkable dataTable">
					<thead>
					<tr>
						<th></th>
						<th width="50" style="text-align:center"><?php echo fc_lang('排序'); ?></th>
						<th class="<?php echo ns_sorting('title'); ?>" name="title"><?php echo fc_lang('主题'); ?></th>
						<th width="120"  class="<?php echo ns_sorting('catid'); ?>" name="catid"><?php echo fc_lang('栏目分类'); ?></th>
						<th width="160" class="<?php echo ns_sorting('updatetime'); ?>" name="updatetime"><?php echo fc_lang('更新时间'); ?></th>
						<th width="80" class="<?php echo ns_sorting('status'); ?>" name="status"><?php echo fc_lang('状态'); ?></th>
						<th><?php echo fc_lang('操作'); ?></th>
					</tr>
					</thead>
					<tbody>
					<?php if (is_array($list)) { $count=count($list);foreach ($list as $t) { ?>
					<tr class="dr_row_hide" id="dr_row_<?php echo $t['id']; ?>">
						<td><input name="ids[]" type="checkbox" class="dr_select toggle md-check" value="<?php echo $t['id']; ?>" /></td>
						<td style="text-align:center"><input class="input-text displayorder" type="text" name="data[<?php echo $t['id']; ?>][displayorder]" value="<?php echo $t['displayorder']; ?>" /></td>
						<td><a title="<?php echo dr_clearhtml($t['title']); ?>" class="onloading" href="<?php if ($t['status'] == 9) {  echo dr_url_prefix($t['url']);  } else {  echo SITE_URL; ?>index.php?c=show&id=<?php echo $t['id'];  } ?>" target="_blank" ><?php if ($t['thumb']) { ?><img src="<?php echo THEME_PATH; ?>admin/images/img.png" align="absmiddle" height="18" width="15">&nbsp;<?php }  echo dr_keyword_highlight(dr_strcut(dr_clearhtml($t['title']), 40), $param['keyword']);  if ($t['link_id'] >0) { ?><img align="absmiddle" src="<?php echo THEME_PATH; ?>admin/images/link2.png"><?php } ?></a></td>
						<td><a title="<?php echo dr_cat_value($t['catid'], 'name'); ?>" href="<?php echo dr_url('content/index', array('mid'=>$mid, 'catid'=>$t['catid'])); ?>"><?php echo dr_strcut(dr_cat_value($t['catid'], 'name'), 12); ?></a></td>
						<td><?php echo dr_date($t['updatetime'], NULL, 'red'); ?></td>
						<td><label><?php if ($t['status'] == 9) { ?><a class="btn blue btn-xs" href="javascript:dr_status('<?php echo $t['id']; ?>', '<?php echo $t['status']; ?>');"><?php echo fc_lang('正常'); ?></a><?php } else { ?><a class="btn red btn-xs" href="javascript:dr_status(<?php echo $t['id']; ?>, '<?php echo $t['status']; ?>');"><?php echo fc_lang('关闭'); ?></a><?php } ?></label></td>
						<td>
							<?php if ($clink) { ?>
							<label>
								<div class="btn-group">
									<button type="button" class="btn dark btn-xs dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="1000" data-close-others="true"> <i class="fa fa-plug"></i> <?php echo fc_lang('插件'); ?>
										<i class="fa fa-angle-down"></i>
									</button>
									<ul class="dropdown-menu" role="menu">
										<?php if (is_array($clink)) { $count=count($clink);foreach ($clink as $a) { ?>
										<li><a class="onloading" href="<?php echo str_replace(array('{mid}', '{id}'), array($mid, $t['id']), $a['url']); ?>"><i class="<?php echo $a['icon']; ?>"></i> <?php echo $a['name'];  if ($a['field']) { ?><span class="badge badge-info"> <?php echo intval($t[$a['field']]); ?> </span><?php } ?> </a></li>
										<?php } } ?>
									</ul>
								</div>
							</label>
							<?php } ?>
							<label>
								<a href="<?php echo dr_url('content/edit',array('mid'=>$mid, 'id'=>$t['id'])); ?>" class="btn btn-xs green onloading">
									<i class="fa fa-edit"></i> <?php echo fc_lang('修改'); ?></a>
							</label>
						</td>
					</tr>
					<?php } } ?>
					<tr class="mtable_bottom">
						<th width="20" ><input name="dr_select" class="toggle md-check" id="dr_select" type="checkbox" onClick="dr_selected()" /></th>
						<td colspan="99" >
							<?php if (!$get['flag']) { ?>
							<label><button type="button" class="btn red btn-sm" name="option" onClick="$('#action').val('del');dr_confirm_set_all('<?php echo fc_lang('您确定要这样操作吗？'); ?>')"> <i class="fa fa-trash"></i> <?php echo fc_lang('删除'); ?></button></label>
							<?php } else { ?>
							<label><button type="button" class="btn red btn-sm" name="option" onClick="$('#action').val('flag');dr_confirm_set_all('<?php echo fc_lang('您确定要这样操作吗？'); ?>')"> <i class="fa fa-flag"></i> <?php echo fc_lang('移出'); ?></button></label>
							<?php } ?>

							<label><button type="button" class="btn green btn-sm" name="option" onClick="$('#action').val('order');dr_confirm_set_all('<?php echo fc_lang('您确定要这样操作吗？'); ?>')"> <i class="fa fa-edit"></i>  <?php echo fc_lang('排序'); ?></button></label>
							<label><button type="button" class="btn blue btn-sm" name="option" onClick="dr_confirm_move();"> <i class="fa fa-share"></i>  <?php echo fc_lang('移动至'); ?></button></label>
							<label><?php echo $select; ?></label>

						</td>
					</tr>
					</tbody>
				</table>

			</div>
	</div>
</div>

</form>
<div id="pages"><a><?php echo fc_lang('共%s条', $param['total']); ?></a><?php echo $pages; ?></div>
<?php if ($fn_include = $this->_include("nfooter.html")) include($fn_include); ?>