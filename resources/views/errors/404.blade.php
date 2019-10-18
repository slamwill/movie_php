<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>网页无法访问</title>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="theme-color" content="#fac328">
		<meta name="renderer" content="webkit">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>

<style>
body, div, dl, dt, dd, ul, ol, li, h1, h2, h3, h4, h5, h6, pre, code, form, textarea, select, optgroup, option, fieldset, legend, p, blockquote, th, td {
	margin:0;
	padding:0;
	font-size:14px;
	font-family: "Microsoft YaHei" ! important;
	letter-spacing:1px;
}


.error-404 {
	background-color: black;
}
.errorImage {
	float:left;
	width:300px;
	height:151px;
	background:url('/images/error404.png') 0 0 no-repeat;
}

.qnote-header {
  	display: flex;
  	justify-content: center;
  	align-items: flex-end;
	margin-top: 10%;
}
.content {
    color: #fff;
    margin-bottom: 10px;
    margin: 0 auto;
	width: 170px;
    /* border: 1px red solid; */
	line-height: 24px;
	margin-bottom: 20px;
}
.content-top {
  	display: flex;
  	justify-content: center;
	align-items: baseline;
	flex-direction:column;
	margin-bottom: 10px;
}
.content-bottom {
  	display: flex;
	justify-content:space-around;
	flex-direction:column;
}

.footer-in {
	height: 20px;
}
.footer {
  	display: flex;
  	justify-content: center;
  	align-items: baseline;
  	width: 100%;
	height: 30vh;
}
.alink {
	display: inline-block;
    border: 1px solid #ffaf00;
    padding: 8px 35px;
    border-radius: 20px;
    color: #fff;
    text-decoration: none;
	margin-bottom: 20px;
}

</style>
	<body class="error-404">
		<div class="qnote-header">
			<div class="errorImage">
			</div>
		</div>
		<div class="content">
			<div class="content-top">
				<p class="title">你所访问的页面不存在了</p>
			</div>
			<div class="content-bottom">
				<p>可能的原因：</p>
				<p>1.输入了错误的地址</p>
				<p>2.点击的链接已过期</p>
			</div>			
		</div>
		<div class="footer">
			<div class="footer-in">
				<p>
					<a class="alink" href="javascript:history.go(-1);">返回上一级页面</a>
				</p>
				<p>
					<a class="alink" href="/">返回到网站首页</a>
				</p>
			</div>
		</div>
	</body>
</html>