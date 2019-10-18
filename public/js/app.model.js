var app = {};
app.text = {
	all : '全部',
	nextStep : '下一步',
	previousStep : '上一步'
};
app.chatClient = function (){
	window.open('https://chat.livechatvalue.com/chat/chatClient/chatbox.jsp?companyID=88800&configID=47868&jid=4064946942&s=1', 'chatClient');	
};


app.clickUserMenu = function (url){

	if (app.isMobile()){
		var target = $('a.user-pjax[href="' + url + '"]');
		target.click();
		$('.mobile-user-top-tabs').find('a[data-target=".'+target.data('group')+'"]').click();
	}
	else {
		$('#user-left-menu-item').find('a[href="' + url + '"]').click();
	}
};
app.f = function (original){
	return (Math.round(original*100)/100).toFixed(2);
};
app.isEmail = function (email) {
	var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	return regex.test(email);
}
app.isMobile = function (){
	return /android|webos|iphone|ipad|ipod|blackberry|iemobile|opera mini/i.test(navigator.userAgent.toLowerCase());
}
app.error = function (message){

	var opt = {
		icon : 2,
		move : false,
		resize : false
	};

	if (typeof message === "object"){
		message.cancel = message.yes;
		$.extend(opt, message);
	}
	else {
		opt.content = message;
	}	
	return layer.open(opt);
};

app.success = function (message){

	var opt = {
		icon : 1,
		move : false,
		resize : false
	};

	if (typeof message === "object"){
		message.cancel = message.yes;
		$.extend(opt, message);
	}
	else {
		opt.content = message;
	}	
	layer.open(opt);

};
app.alert = function (message){
	return zeroModal.alert(message);
};
app.confirm = function (msg, callback){
	layer.confirm(msg, {icon: 3, title:'提示' ,move : false, resize : false}, function(index){
		layer.close(index);
		return callback();
	});

};
app.logout = function (){
	var msg = '是否确定登出?';
	layer.confirm(msg, {icon: 3, title:'提示' ,move : false, resize : false}, function(index){
		layer.close(index);
		//api.logout();
		document.getElementById('logout-form').submit();
	});

	event.preventDefault();
};

app.append = {
	select : function (target, data){
		$.each(typeof data ==='function' ? data() : data , function (key, val){
			$(target).append($('<option>', { 
				value: key,
				text : val 
			}));
		});
	},
};
app.countdown = function (target, seconds, callback){
	$(target).countdown(new Date().getTime() + seconds * 1000,function ( event ){
		if (event.type == 'finish') {
			callback();
		}
		else {
			$(this).html(event.strftime('%M:%S'));
		}
	});

};
app.loading = {
	target : '',
	open : function (){
		$.blockUI({ 
			message: $('<div class="sk-rotating-plane"></div>'), 
			css: {
				top:'30%',
				color: '#fff',
				background: 'none',
				border:'0px',
				opacity: .9
			}
		});
		//app.loading.target = zeroModal.loading(3);	
	},
	close : function (){
		$.unblockUI();
		//zeroModal.close(app.loading.target);		
	}

};
app.api = {
	watchs : function (avkey){
		$.ajax({
			method: 'post',
			url: '/api/collection/watchs',
			data: { 'avkey': avkey ,'_token': $('meta[name="csrf-token"]').attr('content') },
			success: function (response) {
				//console.log(response);
			
			}
		});
	},
	syncBalance : function (data){
		data._token = $('meta[name="csrf-token"]').attr('content');
		$.ajax({
			method: 'post',
			url: '/api/user/syncBalance',
			data: data,
			success: function (response) {
				//console.log(response);
			}
		});
	},

	syncBind : function (data){
		data._token = $('meta[name="csrf-token"]').attr('content');
		$.ajax({
			method: 'post',
			url: '/api/user/syncBind',
			data: data,
			success: function (response) {
				//console.log(response);
			}
		});
	},
	touchSwitchMenu : function (val){
		$.ajax({
			method: 'GET',
			url: '/api/touchSwitchMenu/' + val,
			//data: data,
			success: function (response) {
				//console.log(response);
			}
		});
	
	},
	resetPassword : function (data){
		data._token = $('meta[name="csrf-token"]').attr('content');
		$.ajax({
			method: 'post',
			url: '/api/user/resetPassword',
			data: data,
			cache : false,
			dataType:'json',
			async: false,
			success: function (response) {
				//console.log(response);
			}
		});
	},
};

app.confirmLogin= function (){
	var msg = '請先登入會員! 您是否要登入?';

	layer.confirm(msg, {icon: 3, title:'提示' ,move : false, resize : false}, function(index){
		window.location.href = '/login';
		layer.close(index);
	});
	/*
	zeroModal.confirm( msg , function (){
		window.location.href = '/login';
	});*/
};
app.search = function (keyword){
	return window.location.href = '/search/' + keyword;
};


app.resizeIframe = function (target){
    var theFrame = $(target, parent.document.body);
    theFrame.each(function () {
        $(this).height($(document.body).height());
    });
}
app.mobileCodeCountDown = function (sec, target){

	var _sec = sec || 59;
	var setTime = function (obj){
		if (_sec == 0) { 
			$(obj).attr("disabled", false).text("取得短信验证码");
			_sec = sec; 
			return;
		}
		else { 
			$(obj).attr("disabled", true).text("重新发送 (" + _sec + ")");
			_sec--; 
		} 
		setTimeout(function() {
			setTime(obj) 
		},1000);
	};
	setTime(target);

}
app.emailOrPhoneCodeCountDown = function (sec, target){

	var _sec = sec || 59;
	var setTime = function (obj){
		if (_sec == 0) { 
			$(obj).attr("disabled", false).text("取得验证码");
			_sec = sec; 
			return;
		}
		else { 
			$(obj).attr("disabled", true).text("重新发送 (" + _sec + ")");
			_sec--; 
		} 
		setTimeout(function() {
			setTime(obj) 
		},1000);
	};
	setTime(target);

}

app.highlightWords = function (keywords, element) {
    if(keywords) {
        var textNodes;
        //keywords = keywords.replace(/\W/g, '');
        var str = keywords.split(" ");
        $(str).each(function() {
            var term = this;
            var textNodes = $(element).contents().filter(function() { return this.nodeType === 3 });
            textNodes.each(function() {
              var content = $(this).text();
              var regex = new RegExp(term, "gi");
              content = content.replace(regex, '<span class="highlight">' + term + '</span>');
              $(this).replaceWith(content);
            });
        });
    }
}


app.player = function (){
	var player = videojs('video-hls', {
		'techOrder': ["hls","html5", "flash"],
		'controls': true,
		'fluid': true,
		'BigPlayButton': true,
		'language':'zh-TW',
		'controlBar': {
			children: [
				{ name: 'playToggle'},
				{ name: 'currentTimeDisplay'},
				{ name: 'progressControl'},
				{ name: 'durationDisplay'},
				{ name: 'MuteToggle'},
				{ name: 'volumeControl'},
				{ name: 'fullscreenToggle'},
			]
		}

	},function (){
		var player = this;
		
		player.hotkeys({
			volumeStep: 0.1,
			seekStep: 5,
			enableModifiersForNumbers: false
		});

	});
	return player;
};

/*
app.downloadConfirm = function (avkey){

	$.ajax({
		method: 'post',
		url: '/api/' + avkey + '/downloadConfirm',
		data: { '_token': $('meta[name="csrf-token"]').attr('content') },
		success: function (response) {
			if (response.login) {
				app.confirm('請先登入會員! 您是否要登入?',function (){
					window.location.href = response.login;
				});
			}
			else if (response.error) { 
				return app.error(response.error);
			}
			else if (response.success) {
				window.location.href = response.success;
			}
		}
	});

};*/
app.downloadConfirm = function (avkey){
	$.ajax({
		method: 'post',
		url: '/api/' + avkey + '/downloadConfirm',
		data: { '_token': $('meta[name="csrf-token"]').attr('content') },
		success: function (response) {
			if (response.login) {
				app.confirm('请先登录会员! 您现在是否要登录?',function (){
					window.location.href = response.login;
				});
			}
			else if(response.confirm) {
				app.confirm(response.confirm,function (){
					$.ajax({
						method: 'get',
						url: '/api/' + avkey + '/consume',
						//data: { '_token': $('meta[name="csrf-token"]').attr('content') },
						success: function (response) {
							if (response.login) {
								app.confirm('请先登录会员! 您现在是否要登录?',function (){
									window.location.href = response.login;
								});
							}
							else if (response.success) {
								app.downloadConfirm(avkey)
							}
							else {
								app.error(response.error);
							}
							//console.log(response);
						}
					});

				});
			}
			else if(response.recharge) {
				app.confirm(response.recharge,function (){

					window.location.href = response.url;
				});
			}
			else if (response.error) { 
				return app.error(response.error);
			}
			else if (response.success) {
				window.open(response.success,'_blank');
				//window.location.href = response.success;
			}
		}
	});


};

$(function (){
	//設定全域pjax timeout
	$.pjax.defaults.timeout = 10000;

    $(document).on('pjax:start', function() {
        NProgress.start();
    });

    $(document).on('pjax:end', function() {
        NProgress.done();
    });
	



	$('ul.nav-menu li.dropdown').hover(function() {
		$(this).find('.dropdown-menu').stop(true, true).delay(100).fadeIn(200);
	}, function() {
		$(this).find('.dropdown-menu').stop(true, true).delay(100).fadeOut(200);
	});

	$("[data-toggle='tooltip']").tooltip();


	$('.collection-videos').click(function (event){
		var info = $(this).data();
		event.preventDefault();
		event.stopPropagation();
		$.ajax({
			method: 'post',
			url: '/api/collection/videos',
			data: { 'avkey': info.avkey, '_token': $('meta[name="csrf-token"]').attr('content') },
			success: function (response) {
				if (response.status == 0) {
					toastr.error('錯誤訊息：' + response.msg);
				}
				else if (response.status == 1) {
					//layer.msg(info.title + ' (已收藏), 番號：'+info.avkey);
					layer.msg( '已新增至"我的收藏"');
					$('#favorite_videos').addClass('favorite');
				}
				else if (response.status == 2) {
					//layer.msg(info.title + ' (已移除), 番號：' + info.avkey);
					layer.msg( '已从"我的收藏"移除');
					$('#favorite_videos').removeClass('favorite');
					//$('#favorite_videos').html('<i class="fa fa-heart-o" aria-hidden="true"> 收藏影片</i>');
				}
				else if (response.status == -1){
					app.confirm('请先登录会员! 您现在是否要登录?',function (){
						window.location.href = response.url;
					});
				}
			}
		});

	});

	$('.collection-videos-small').click(function (event) {
		var info = $(this).data();
		event.preventDefault();
		event.stopPropagation();
		if (info.action == 'delete')
		{
			$(this).closest('li').remove();
		}

		$.ajax({
			method: 'post',
			url: '/api/collection/videos',
			data: { 'avkey': info.avkey, 'action': info.action, '_token': $('meta[name="csrf-token"]').attr('content') },
			success: function (response) {

				if (response.status == 0) {
					layer.error('錯誤訊息：' + response.msg);
				}
				else if (response.status == 1) {
					layer.msg( '已新增至"我的收藏"');
				}
				else if (response.status == 2) {
					layer.msg( '已从"我的收藏"移除');
				}
				else if (response.status == -1) {
					app.confirm('请先登录会员! 您现在是否要登录?', function () {
						window.location.href = response.url;
					});
				}
			}
		});

	});


	$('.header-search-icon a,.header-search-bar .cancel-header-search-bar').click(function (){
		$('.header-search-bar').toggle(0);
	});

	$('.header-search-bar .submit-header-search-bar').click(function (){
		var keyword = $('#search-text-mobile').val();
		app.search(keyword);
	});

	$('.header-search-bar .fa-times-circle').click(function (){
		$(this).hide().closest('.header-search-bar').removeClass('keyword');	
		$('#search-text-mobile').val('').focus();
	});
	$('#search-text-web').bind('keypres', function(e) {
		var keyword = $(this).val();
		if(e.keyCode==13 && keyword){
			app.search(keyword);
		}
	});

	$('#search-text-mobile').bind('keyup', function(e) {
		var keyword = $(this).val();
		if (keyword) {
			$(this).closest('.header-search-bar').addClass('keyword').find('.fa-times-circle').show();			
		}
		else{
			$(this).closest('.header-search-bar').removeClass('keyword').find('.fa-times-circle').hide();	
		}
		if(e.keyCode==13 && keyword){
			app.search(keyword);
		}
	});


	$('#search-web-submit').click(function (){
		var keyword = $('#search-text-web').val();
		if (keyword) {
			app.search(keyword);
		}
	});

	$('#search-mobile-submit').click(function (){
		var keyword = $('#search-text-mobile').val();
		if (keyword) {
			app.search(keyword);
		}
	});


	$('.check-login').on('click',function (){
		//console.log(api.isLogin, av.getAuthStatus());
		if (!api.isLogin || av.getAuthStatus() == 'guest'){
			app.confirmLogin();
			return false;
		}
	});

	var setTimeoutConst;
	//影片预看
	//$(document).on('mouseover touchmove','.videos-list .a-cover',function (){

	$(document).on('mouseover','.videos-list .a-cover',function (){
		if (app.isMobile()) return true;
		_this = this;
		var previewUrl = $(_this).data('preview');
		var progress = $(_this).find('.progress');
		var progressBar = progress.children('.progress-bar');
		if (progressBar.attr('aria-valuenow') == '0') progress.fadeIn(200);
		progressBar.animate({width:'100%'},500);

		var videoHtml = '<video autoplay muted preload="auto" width="100%" loop height="100%"><source src="'+previewUrl+'" type="video/mp4"></video>';
		//var videoHtml = '<video id="'+vid+'" width="100%" loop height="100%"></video>';
		$(_this).find('.preview-video').css({'z-index':'0'}).append(videoHtml);
		var lastVideo = $(_this).find("video")[0];
		$(lastVideo).on("play", function() {
			progressBar.css({width:'100%'}).attr('aria-valuenow',100);
			progress.fadeOut(200);
		});		
		lastVideo.defaultPlaybackRate = 2;
		lastVideo.load();
		return true;

	});
	$(document).on('mouseleave','.videos-list .a-cover',function (){
		if (app.isMobile()) return true;
		_this = this;
		$(_this).find('.preview-video').css({'z-index':'-1'}).html('');
		var progress = $(_this).find('.progress');
		var progressBar = progress.children('.progress-bar');
		progress.fadeOut(0);

	});
	
});