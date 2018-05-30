<?php if ($fn_include = $this->_include("header.html")) include($fn_include); ?>
<link href="<?php echo HOME_THEME_PATH; ?>css/home.css" rel="stylesheet" />
<script src="<?php echo HOME_THEME_PATH; ?>js/home.js"></script>
<!-- 主体 -->
<div class="blog-body">

	<!-- 这个一般才是真正的主体内容 -->
	<div class="blog-container">

		<div class="blog-main">
			<!-- 网站公告提示 -->
			<div class="home-tips shadow">
				<i style="float:left;line-height:17px;" class="fa fa-volume-up"></i>
				<div class="home-tips-container">
					<?php $gg = @explode(PHP_EOL, dr_block(1));  if (is_array($gg)) { $count=count($gg);foreach ($gg as $t) {  list($value, $color)=explode('|', $t); ?>
					<span style="color: <?php echo $color; ?>"><?php echo $value; ?></span>
					<?php } } ?>
				</div>
			</div>
			<!--左边-->
			<div class="blog-main-left">

				<div id="carousel-example-generic" style="margin-bottom: 10px" class="carousel slide" data-ride="carousel">
					<!-- Indicators -->

					<ol class="carousel-indicators">
						<li data-target="#carousel-example-generic" data-slide-to="0" class="active"></li>
						<li data-target="#carousel-example-generic" data-slide-to="1" class=""></li>
						<li data-target="#carousel-example-generic" data-slide-to="2" class=""></li>
					</ol>
					<!-- Wrapper for slides -->
					<div class="carousel-inner" role="listbox">
                        <div class="item active">
                            <img src="statics/assets/banner/finecms-1.jpg" />
                        </div>
                        <div class="item ">
                            <img src="statics/assets/banner/finecms-2.jpg" />
                        </div>
                        <div class="item ">
                            <img src="statics/assets/banner/finecms-3.jpg" />
                        </div>
					</div>

				</div>



				<?php $rt_c = $this->list_tag("action=category pid=0 tid=1 num=4  return=c"); if ($rt_c) extract($rt_c); $count_c=count($return_c); if (is_array($return_c)) { foreach ($return_c as $key_c=>$c) { ?>
				<div class="blog-module shadow">
					<div class="blog-module-title"><a href="<?php echo $c['url']; ?>"><?php echo $c['name']; ?></a></div>
					<div class="index-div-ul">
						<ul class="fa-ul blog-module-ul index-ul">
							<?php $rt = $this->list_tag("action=module catid=$c[id] order=displayorder,updatetime num=16"); if ($rt) extract($rt); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { ?>
							<li><a href="<?php echo $t['url']; ?>"><?php echo dr_strcut($t['title'], 40); ?></a></li>
							<?php } } ?>
						</ul>

					</div>
				</div>
				<?php } } ?>


			</div>
			<!--右边小栏目-->
			<div class="blog-main-right">
				<div class="blogerinfo shadow">
					<?php if ($member) { ?>

					<div class="blogerinfo-figure">
						<a href="<?php echo MEMBER_URL; ?>">
							<img src="<?php echo $member['avatar_url']; ?>" />
						</a>
					</div>
					<p class="blogerinfo-nickname"><?php echo $member['username']; ?></p>
					<p class="blogerinfo-location"><?php echo $member['groupname']; ?></p>
					<hr />
					<div class="blogerinfo-contact">
						<a   href="<?php echo MEMBER_URL; ?>">会员中心</a>
						<a  href="<?php echo dr_member_url('login/out'); ?>">退出登录</a>
					</div>
					<?php } else { ?>
					<div class="blogerinfo-figure">
						<form class="form-horizontal" id="myform" role="form" style="margin-top: 10px">
							<div class="form-body">
								<div class="form-group">
									<div class="col-md-12">
										<input type="text" name="data[username]" placeholder="账号" class="form-control">

									</div>
								</div>
								<div class="form-group">
									<div class="col-md-12">
										<input type="password" name="data[password]" placeholder="密码" class="form-control">

									</div>
								</div>
								<div class="form-group">
									<div class="col-md-12">
										<button onclick="dr_submit()" type="button" class="btn green">登录账号</button>
										<a href="<?php echo dr_member_url('register/index'); ?>" class="btn red">注册账号</a>
									</div>
								</div>
							</div>
						</form>
					</div>
					<script type="text/javascript">
                        function dr_submit() {
                            var post = $("#myform").serialize();
                            $.ajax({type: "POST",dataType:"json", url: "<?php echo dr_member_url('login/index'); ?>", data: post,
                                success: function(data) {
                                    if (data.status) {
                                        dr_tips('登录成功，即将为您跳转....', 3, 1);
                                        setTimeout('window.location.href="'+data.backurl+'"', 2000);
                                        var sync_url = data.syncurl;
                                        // 发送同步登录信息
                                        for(var i in sync_url){
                                            $.ajax({
                                                type: "GET",
                                                async: false,
                                                url:sync_url[i],
                                                dataType: "jsonp",
                                                success: function(json){ },
                                                error: function(){ }
                                            });
                                        }
                                    } else {
                                        dr_tips(data.code);
                                    }
                                },
                                error: function(HttpRequest, ajaxOptions, thrownError) {
                                    alert(HttpRequest.responseText);
                                }
                            });
                        }
					</script>
					<?php } ?>

				</div>
				<div></div><!--占位-->



				<div class="blog-module shadow">
					<div class="blog-module-title">站内搜索</div>
					<div class="blogroll" style="padding: 10px 0">
						<form method="get" action="/index.php" class="form-horizontal" role="form" >
							<input type="hidden" name="c" value="search">
							<div class="input-group">
								<span class="input-group-btn">
									<select name="mid" class="form-control input-xsmall" style="margin-right: 10px">
										<?php $rt = $this->list_tag("action=cache name=module"); if ($rt) extract($rt); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { ?>
										<option value="<?php echo $t['dirname']; ?>"><?php echo $t['name']; ?></option>
										<?php } } ?>
									</select>
								</span>
								<input name="keyword" type="text" class="form-control">
								<span class="input-group-btn">
									<button class="btn blue" type="submit">搜索</button>
								</span>
							</div>
						</form>
					</div>
				</div>

				<div class="blog-module shadow">
					<div class="blog-module-title">热文排行</div>
					<ul class="fa-ul blog-module-ul">
						<?php $rt = $this->list_tag("action=module module=news order=hits num=16"); if ($rt) extract($rt); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { ?>
						<li><i class="fa-li fa fa-hand-o-right"></i><a href="<?php echo $t['url']; ?>"><?php echo dr_strcut($t['title'], 35); ?></a></li>
						<?php } } ?>
					</ul>
				</div>

				<div class="blog-module shadow">
					<div class="blog-module-title">tag标签</div>
					<ul class="blogroll">
						<?php $rt = $this->list_tag("action=tags order=rand num=30"); if ($rt) extract($rt); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { ?>
						<li><a href="<?php echo $t['url']; ?>"><?php echo $t['name']; ?></a></li>
						<?php } } ?>
					</ul>
				</div>

				<div class="blog-module shadow">
					<div class="blog-module-title">友情链接</div>
					<ul class="blogroll">
						<?php $link = @explode(PHP_EOL, dr_block(3));  if (is_array($link)) { $count=count($link);foreach ($link as $t) {  list($url, $name)=explode('|', $t); ?>
						<li><a target="_blank" href="<?php echo $url; ?>"><?php echo $name; ?></a></li>
						<?php } } ?>
					</ul>
				</div>
			</div>
			<div class="clear"></div>
		</div>
	</div>
</div>
<?php if ($fn_include = $this->_include("footer.html")) include($fn_include); ?>