var av = {};
av.loginPopup = function (){
	layer.open({
		type: 2,
		title: '',
		shadeClose: false,
		shade: 0.8,
		area: ['400px', '450px'],
		skin: 'layui-layer-rim',
		content: ['/login/popup','no'] 
	}); 
};
av.registerPopup = function (){
	layer.open({
		type: 2,
		title: '',
		shadeClose: false,
		shade: 0.8,
		area: ['400px', '450px'],
		skin: 'layui-layer-rim',
		content: ['/register/popup','no']
	}); 
};

av.login = function (name, password, captcha) {
	$.ajax({
		url: '/login',
		type: 'POST',
		//contentType:'application/json; charset=utf-8',
		cache : false,
		//dataType:'json',
		data: { '_token': $('meta[name="csrf-token"]').attr('content'), 'name' : name, 'password' : password, 'captcha' : captcha, 'remember' : 'on'},
		success: function (response) {
			if (response.url) {
				//alert( response.url);
				window.top.location.href = response.url;
			}
			else{			
				console.log(response);
			}
			
		},
		error: function(data){
			//var errors = data.responseJSON;
			//av.register(name,password);
			$('.refereshrecapcha').click();
			$.each(data.responseJSON.errors,function (key,value){		
				app.error(value[0]);
				return false;
			});
		}
	});
};

av.logout = function () {
	var result = $.ajax({
		url: '/logout',
		type: 'POST',
		//contentType:'application/json; charset=utf-8',
		cache : false,
		//dataType:'json',
		async: false,
		data: { '_token': $('meta[name="csrf-token"]').attr('content'), response: 'json' },
		success: function (response) {
			return response;
			//console.log(response);
		},
		error: function(data){
			var errors = data.responseJSON;
			app.error(errors.errors.name[0]);
		}
	});
	return result.responseJSON;
};
av.register = function (name, password, captcha) {
	var result = $.ajax({
		url: '/register',
		type: 'POST',
		//contentType:'application/json; charset=utf-8',
		cache : false,
		//dataType:'json',
		async: false,
		data: { '_token': $('meta[name="csrf-token"]').attr('content'), 'name' : name, 'password' : password, 'password_confirmation' : password,'captcha' : captcha},
		success: function (response) {
			return response;
		},
		error: function(response){
			$('.refereshrecapcha').click();
			return response;
		}

	});
	return result.responseJSON;

};

av.getAuthStatus = function (){
	return $('meta[name="auth-status"]').attr('content');
};