<!doctype html>
<html>
	<head>
		<meta charset="UTF-8" />
		<title><?php echo $this->pageTitle;?></title>
		<meta name="robots" content="noindex,nofollow">
		<link href="<?php echo AdminAsset;?>/css/admin_login.css?v20130702" rel="stylesheet" />
		<script>
			if (window.parent !== window.self) {
					document.write = '';
					window.parent.location.href = window.self.location.href;
					setTimeout(function () {
							document.body.innerHTML = '';
					}, 0);
			}
		</script>
	</head>
<body>
	<div class="wrap">
		<h1><a href="javascript:void(0)"></a></h1>
		<form method="post" name="login" action="<?php echo HostName_Backed;?>/login/ShowVerify?a=login" autoComplete="off">
			<div class="login">
				<ul>
					<li>
						<input class="input" id="J_admin_name" required name="User[username]" type="text" placeholder="帐号名" title="帐号名" />
					</li>
					<li>
						<input class="input" id="admin_pwd" type="password" required name="User[password]" placeholder="密码" title="密码" />
					</li>
									</ul>
				<button type="submit" name="submit" class="btn">登录</button>
			</div>
		<input type="hidden" name="csrf_token" value="e36a3a6df80a7519"/></form>
	</div>

<script>
var GV = {
	JS_ROOT : "<?php echo AdminAsset;?>/js/dev/",//js目录
	JS_VERSION : "20130702",																										//js版本号
	TOKEN : 'e36a3a6df80a7519'	//token ajax全局
};
</script>
<script src="<?php echo AdminAsset;?>/js/dev/wind.js?v20130702"></script>
<script src="<?php echo AdminAsset;?>/js/dev/jquery.js?v20130702"></script>
<script src="<?php echo AdminAsset;?>/js/dev/pages/admin/common/common.js?v20130702"></script>
<script>
;(function(){
	document.getElementById('J_admin_name').focus();

	getVerifyTemp({wrap : $('#J_verify_code')});

	function getVerifyTemp(options){
		//验证码模板
		var _this = this,
			wrap = options.wrap,				//验证码容器
			afterClick = options.afterClick,	//点击换一个后回调
			clone = options.clone;				//获取失败后恢复内容
		if(!wrap.length) {
			return;
		}

		wrap.html('<span class="tips_loading">验证码loading</span>');
		var url = '<?php echo HostName_Backed;?>/login/showVerify';
		$.post(url, {
			csrf_token : GV.TOKEN
		}, function(data){
			if(data.state == 'success') {
				wrap.html(data.html);
			}else if(data.state == 'fail') {
				if(clone) {
					//恢复原代码
					wrap.html(clone.html());
				}else{
					//重试
					wrap.html('<a href="#" role="button" id="J_verify_update_a">重新获取</a>');
				}

				alert(data.message);
				/*_this.resultTip({
					error : true,
					elem : $('#J_verify_update_a'),
					follow : true,
					msg : data.message
				});*/
			}

		}, 'json');

		wrap.off('click').on('click', '#J_verify_update_a', function(e){
			//换一个
			e.preventDefault();

			if(wrap.find('.tips_loading').length) {
				//防多次点击
				return false;
			}

			var clone = wrap.clone();
			wrap.html('<span class="tips_loading">验证码loading</span>');
			getVerifyTemp({
				wrap : wrap,
				clone : clone
			});

			if(afterClick) {
				afterClick();
			}
		}).on('click', '#J_verify_update_img', function(e){
			//点击图片
			$('#J_verify_update_a').click();
		});
	}
})();
</script>
</body>
</html>
