<?php if ($fn_include = $this->_include("mheader.html")) include($fn_include); ?>
<script type="text/javascript">
    $(function(){
        <?php if ($result_error) { ?>
        dr_tips('<?php echo $result_error['msg']; ?>', 3);
        <?php } ?>
    });
</script>

<div class="blog-module shadow">
	<div class="blog-module-title">修改资料</div>

	<div class="member-form">

		<form action="" method="post" class="form-horizontal" novalidate="novalidate">
			<input type="hidden" name="data[uid]" value="<?php echo $member['uid']; ?>">
			<div class="form-body">

				<div class="form-group">
					<label class="col-md-2 control-label">邮箱：</label>
					<div class="col-md-10" style="padding-top: 10px;">
						<?php echo $member['email']; ?>
					</div>
				</div>
				<?php echo $myfield; ?>
			</div>

			<div class="form-actions">
				<div class="row">
					<div class="col-md-offset-2 col-md-3">
						<button type="submit" class="mysubmit btn green"><i class="fa fa-save"></i> 保存</button>
					</div>
				</div>
			</div>

		</form>

	</div>
</div>



<?php if ($fn_include = $this->_include("mfooter.html")) include($fn_include); ?>