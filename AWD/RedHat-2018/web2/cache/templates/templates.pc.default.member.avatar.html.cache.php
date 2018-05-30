<?php if ($fn_include = $this->_include("mheader.html")) include($fn_include); ?>


<link href="<?php echo THEME_PATH; ?>admin/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css" />

<script src="<?php echo THEME_PATH; ?>admin/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
<div class="blog-module shadow">
	<div class="blog-module-title">上传头像</div>

	<div class="member-form">

		<div class="fileinput fileinput-new" data-provides="fileinput">
			<div class="fileinput-preview thumbnail" id="mytx" style="width: 100%; height:auto;">
				<img src="<?php echo dr_avatar($member['uid'], 0); ?>">
			</div>
			<div>
                    <span class="btn red btn-outline btn-file">
                        <span class="fileinput-new"> 上传 </span>
                        <span class="fileinput-exists"> 上传 </span>
                        <input type="file" name="..."> </span>
				<a href="javascript:;" class="btn red fileinput-exists" data-dismiss="fileinput"> <i class="fa fa-trash-o"></i>  删除 </a>
				<a href="javascript:dr_save();" class="btn green fileinput-exists"> <i class="fa fa-save"></i> 保存 </a>
			</div>
		</div>

		<script type="text/javascript">
            function dr_save() {
                dr_tips('正在上传...', 3, 2);
                var tx = $('#mytx img').attr('src');
                $.post("<?php echo dr_member_url('account/upload', array('iajax' => 1)); ?>", {tx: tx}, function(data) {
                    if(data == 1) {
                        dr_tips('上传成功', 3, 1);
                        window.location.reload();
                    } else {
                        dr_tips('上传失败：'+data.code);
                    }
                }, 'json');
            }
		</script>

	</div>
</div>



<?php if ($fn_include = $this->_include("mfooter.html")) include($fn_include); ?>