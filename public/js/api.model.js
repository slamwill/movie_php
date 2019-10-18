var api = {};
api.config = {
	url : '',
	subCompany : 'xierdun',
	//subCompany : 'xierdun',
};

api.init = function () {
	//$.ajaxSetup({ cache: false });


};

api.validateCheckCode = function (captcha) {

	var result = $.ajax({
		url: api.config.url + '/serviceAg/rest/user/validateCheckCode',
		type: 'POST',
		contentType:'application/json; charset=utf-8',
		cache : false,
		dataType:'json',
		async: false,
		data: captcha,
		success: function (response) {
			return response;
		},
		error: function(response){
			return response;
		}
	});

	return result.responseJSON;


};

api.checkLoginPassCode = function (userName, vcode) {
	var data = {
		userName : userName,
		vcode : vcode
	};
	var result = $.ajax({
		url: api.config.url + '/serviceAg/rest/user/checkLoginPassCode',
		type: 'POST',
		contentType:'application/json; charset=utf-8',
		cache : false,
		dataType:'json',
		async: false,
		data: JSON.stringify(data),
		success: function (response) {
			return response;
		},
		error: function(response){
			return response;
		}
	});

	return result.responseJSON;


};



api.loginPassCreateCode = function (userName, validMethod) {
	var data = {
		userName : userName,
		validMethod : validMethod
	};
	var result = $.ajax({
		url: api.config.url + '/serviceAg/rest/user/loginPassCreateCode',
		type: 'POST',
		contentType:'application/json; charset=utf-8',
		cache : false,
		dataType:'json',
		async: false,
		data: JSON.stringify(data),
		success: function (response) {
			return response;
		},
		error: function(response){
			return response;
		}
	});

	return result.responseJSON;


};



api.resetLoginPassword = function (userName, vcode, newPassword) {
	var crypt = new JSEncrypt();
	crypt.setPublicKey(api.pubkey);	
	var data = {
		userName : userName,
		vcode : vcode,
		newPassword : crypt.encrypt(newPassword),
		fingerPrint : new Fingerprint().get()
	};
	var result = $.ajax({
		url: api.config.url + 'serviceAg/rest/userCentralService/resetLoginPassword',
		type: 'POST',
		contentType:'application/json; charset=utf-8',
		cache : false,
		dataType:'json',
		async: false,
		data: JSON.stringify(data),
		success: function (response) {
			return response;
		},
		error: function(response){
			return response;
		}
	});

	return result.responseJSON;


};


api.resetLoginPassCheck = function (account) {

	var result = $.ajax({
		url: api.config.url + '/serviceAg/rest/user/resetLoginPassCheck',
		type: 'POST',
		contentType:'application/json; charset=utf-8',
		cache : false,
		dataType:'json',
		async: false,
		data: account,
		success: function (response) {
			return response;
		},
		error: function(response){
			return response;
		}
	});

	return result.responseJSON;


};

api.register = function (userName, password, code, cellPhoneNumber) {
	var data = {
		userName : userName,
		password : password,
		vcode : code,
		parentUserName : api.config.subCompany,
		cellPhoneNumber : cellPhoneNumber,
		currency : 1
	};
	var result = $.ajax({
		url: api.config.url + '/serviceAg/rest/user/3000',
		type: 'POST',
		contentType:'application/json; charset=utf-8',
		cache : false,
		dataType:'json',
		async: false,
		data: JSON.stringify(data),
		/*
		success: function (response) {
			if (response.errorMessage) {
				app.error(response.errorMessage);
			}
			if (response.status == 1) {
				av.register(data.userName, data.password);
			}

		}*/
		success: function (response) {
			return response;
		},
		error: function(response){
			return response;
		}
	});
	return result;
	//027說"你就不要輸入錯誤的驗證碼"，我方立馬放棄對話
	//return result.responseJSON;

};

api.register = function (userName, password, code) {
	var data = {
		userName : userName,
		password : password,
		checkCode : code,
		parentUserName : api.config.subCompany,
		currency : 1
	};

	var result = $.ajax({
		url: api.config.url + '/serviceAg/rest/user/1000',
		type: 'POST',
		contentType:'application/json; charset=utf-8',
		cache : false,
		dataType:'json',
		async: false,
		data: JSON.stringify(data),
		/*
		success: function (response) {
			if (response.errorMessage) {
				app.error(response.errorMessage);
			}
			if (response.status == 1) {
				av.register(data.userName, data.password);
			}

		}*/
		success: function (response) {
			return response;
		},
		error: function(response){
			return response;
		}
	});

	return result.responseJSON;


};

api.regCreateCode = function (cellPhoneNumber) {
	var data = {
		cellPhoneNumber : cellPhoneNumber,
		parentUserName : api.config.subCompany,
		currency : 1
	};

	var result = $.ajax({
		url: api.config.url + '/serviceAg/rest/user/regCreateCode',
		type: 'POST',
		contentType:'application/json; charset=utf-8',
		cache : false,
		dataType:'json',
		async: false,
		data: JSON.stringify(data),
		/*
		success: function (response) {
			if (response.errorMessage) {
				app.error(response.errorMessage);
			}
			if (response.status == 1) {
				av.register(data.userName, data.password);
			}

		}*/
		success: function (response) {
			return response;
		},
		error: function(response){
			return response;
		}
	});

	return result.responseJSON;


};

/*
api.loginProcess = function (userName, password) {
	var data = {
		userName : userName,
		password : password,
		subCompany : api.config.subCompany,
	};

	$.ajax({
		url: api.config.url + '/serviceAg/rest/loginProcess/login',
		type: 'POST',
		contentType:'application/json; charset=utf-8',
		cache : false,
		dataType:'json',
		//async: false,
		data: JSON.stringify(data),
		success: function (response) {
			if (response.status == 1) {
				av.login(data.userName, data.password);
			}
			else {
				app.error(response.errorMessage);
			}
		}
	});
};*/
api.login = function (userName, password) {
	var data = {
		userName : userName,
		password : password,
		subCompany : api.config.subCompany,
	};

	var result = $.ajax({
		url: api.config.url + '/serviceAg/rest/loginProcess/login',
		type: 'POST',
		contentType:'application/json; charset=utf-8',
		cache : false,
		dataType:'json',
		async: false,
		data: JSON.stringify(data),
		success: function (response) {
			return response;
		},
		error: function(response){
			return response;
		}
	});
	return result.responseJSON;
};

api.logout = function (){
	var result = $.ajax({
		url: api.config.url + '/serviceAg/rest/logoutProcess/logout',
		type: 'POST',
		contentType:'application/json; charset=utf-8',
		cache : false,
		dataType:'json',
		async: false,
		success: function (response) {
			return response;
		},
		error: function(response){
			return response;
		}
	});
	return result.responseJSON;
};
api.hasUserLogin = function () {

	var result = $.ajax({
		url: api.config.url + '/serviceAg/rest/loginProcess/hasUserLogin',
		type: 'POST',
		contentType:'application/json; charset=utf-8',
		//cache : false,
		dataType:'json',
		async: false,
		success: function (response) {
			return response;
		},
		error: function(response){
			return response;
		}
	});
	return result.responseJSON;
};



api.checkLoginPass = function (password) {
	var crypt = new JSEncrypt();
	crypt.setPublicKey(api.pubkeyPayPass);
	var data = {
		fingerPrint : new Fingerprint().get(),
		oldPassword : crypt.encrypt(password)
	};

	var result = $.ajax({
		url: api.config.url + '/serviceAg/rest/userCentralService/checkLoginPass',
		type: 'POST',
		contentType:'application/json; charset=utf-8',
		cache : false,
		dataType:'json',
		async: false,
		data: JSON.stringify(data),
		success: function (response) {
			return response;
		},
		error: function(response){
			return response;
		}
	});
	//console.log(result.responseJSON);
	return result.responseJSON;
};

api.changePassword = function (newPassword, oldPassword) {
	var data = {
		newPassword : newPassword,
		oldPassword : oldPassword
	};

	var result = $.ajax({
		url: api.config.url + '/serviceAg/rest/userCentralService/password',
		type: 'PUT',
		contentType:'application/json; charset=utf-8',
		cache : false,
		dataType:'json',
		async: false,
		data: JSON.stringify(data),
		success: function (response) {
			return response;
		}
	});
	return result.responseJSON;
};

api.checkCode = function (){
	return api.config.url + '/serviceAg/checkCode?' + $.now();
};


api.yeepayDepositProcess = function (data) {
	var result = $.ajax({
		url: api.config.url + '/serviceAg/rest/yeepayDepositProcess',
		type: 'POST',
		contentType:'application/json; charset=utf-8',
		cache : false,
		dataType:'json',
		async: false,
		data: JSON.stringify(data),
		success: function (response) {
			return response;
		},
		error: function(response){
			return response;
		}
	});
	return result.responseJSON;
};


api.eachYeepayDepositProcess = function (payment, map, params) {
	for(var i=0;i<payment.length;i++){
		var data = map[payment[i]];
		$.extend(data, params);
		var result = $.ajax({
			url: api.config.url + '/serviceAg/rest/yeepayDepositProcess',
			type: 'POST',
			contentType:'application/json; charset=utf-8',
			cache : false,
			dataType:'json',
			async: false,
			data: JSON.stringify(data),
			success: function (response) {
				return response;
			},
			error: function(response){
				return response;
			}
		});
		//console.log(result.responseJSON);
		if (result.responseJSON.status == 1) {
			return $.extend(result.responseJSON, {paymentCode : payment[i]});
		}
	}
	if (result){
		return $.extend(result.responseJSON, {paymentCode : payment[i]});
	}
	else {
		return {status:0,errorMessage:'请刷新页面后重新提交',paymentCode : 0};
	}
	//return {status:0,errorMessage:'请刷新页面后重新提交',paymentCode : 0};
};




api.depositMethods = function () {
	var result = $.ajax({
		url: api.config.url + '/serviceAg/rest/subCompanyBankCardService/depositMethods',
		type: 'GET',
		contentType:'application/json; charset=utf-8',
		cache : false,
		dataType:'json',
		async: false,
		success: function (response) {
			return response;
		},
		error: function(response){
			return response;
		}
	});
	return result.responseJSON;
};

api.bankCardUser = function () {
	var result = $.ajax({
		url: api.config.url + '/serviceAg/rest/subCompanyBankCardService/bankCard/user',
		type: 'GET',
		contentType:'application/json; charset=utf-8',
		cache : false,
		dataType:'json',
		async: false,
		success: function (response) {
			return response;
		},
		error: function(response){
			return response;
		}
	});
	return result.responseJSON;
};

api.traditionalDepositProcess = function (data) {
		
	var result = $.ajax({
		url: api.config.url + '/serviceAg/rest/traditionalDepositProcess',
		type: 'POST',
		async:true,
		contentType:'application/json; charset=utf-8',
		cache : false,
		dataType:'json',
		async: false,
		data: JSON.stringify(data),
		success: function (response) {
			return response;
		},
		error: function(response){
			return response;
		}
	});
	return result.responseJSON;

};
api.balance = function () {

	var result = $.ajax({
		url: api.config.url + '/serviceAg/rest/userCentralService/balance',
		type: 'GET',
		contentType:'application/json; charset=utf-8',
		cache : false,
		dataType:'json',
		async: false,
		success: function (response) {
			return response;
		},
		error: function(response){
			return response;
		}
	});
	return result.responseJSON;

};

api.balanceNoSync = function () {

	$.ajax({
		url: api.config.url + '/serviceAg/rest/userCentralService/balance',
		type: 'GET',
		contentType:'application/json; charset=utf-8',
		cache : false,
		dataType:'json',
		async: true,
		success: function (response) {
			$('.wallet-center-text').html(app.f(response));
		}
	});

};



api.balanceDeposit = function (data) {

	var result = $.ajax({
		url: api.config.url + '/serviceAg/rest/application/balance/deposit',
		type: 'PUT',
		contentType:'application/json; charset=utf-8',
		cache : false,
		dataType:'json',
		async: false,
		data: JSON.stringify(data),
		success: function (response) {
			return response;
		},
		error: function(response){
			return response;
		}
	});
	return result.responseJSON;

};
api.balanceWithdrawal = function (data) {

	var result = $.ajax({
		url: api.config.url + '/serviceAg/rest/application/balance/withdrawal',
		type: 'PUT',
		contentType:'application/json; charset=utf-8',
		cache : false,
		dataType:'json',
		async: false,
		data: JSON.stringify(data),
		success: function (response) {
			return response;
		},
		error: function(response){
			return response;
		}
	});
	return result.responseJSON;

};

api.gameBalance = function (gameId) {
	var data = {
		gameId : gameId
	};
		
	var result = $.ajax({
		url: api.config.url + '/serviceAg/rest/application/balance',
		type: 'POST',
		contentType:'application/json; charset=utf-8',
		cache : false,
		dataType:'json',
		async: false,
		data: JSON.stringify(data),
		success: function (response) {
			return response;
		},
		error: function(response){
			return response;
		}
	});
	return result.responseJSON;

};
api.gameBalanceNoSync = function (gameId) {
	var data = {
		gameId : gameId
	};
		
	$.ajax({
		url: api.config.url + '/serviceAg/rest/application/balance',
		type: 'POST',
		contentType:'application/json; charset=utf-8',
		cache : false,
		dataType:'json',
		async: true,
		data: JSON.stringify(data),
		success: function (response) {
			if (response.status == 1){
				$('.game-wallet-text').html(app.f(response.balance));
			}
		}
	});

};

api.bindPhoneCodes = function (phone) {
		
	var result = $.ajax({
		url: api.config.url + '/serviceAg/rest/messageMsgRestWrapper/bindPhoneCodes',
		type: 'POST',
		contentType:'application/json; charset=utf-8',
		cache : false,
		dataType:'json',
		async: false,
		data: phone,
		success: function (response) {
			return response;
		},
		error: function(response){
			return response;
		}
	});
	return result.responseJSON;

};

api.bindEmailCodes = function (email) {
		
	var result = $.ajax({
		url: api.config.url + '/serviceAg/rest/messageMsgRestWrapper/bindEmailCodes',
		type: 'POST',
		contentType:'application/json; charset=utf-8',
		cache : false,
		dataType:'json',
		async: false,
		data: email,
		success: function (response) {
			return response;
		},
		error: function(response){
			return response;
		}
	});
	return result.responseJSON;

};

api.submitDrawal = function (data) {
		
	var result = $.ajax({
		url: api.config.url + '/serviceAg/rest/userDrawalrocess/submitDrawal',
		type: 'POST',
		contentType:'application/json; charset=utf-8',
		cache : false,
		dataType:'json',
		async: false,
		data: JSON.stringify(data),
		success: function (response) {
			return response;
		}
	});
	return result.responseJSON[0];

};

api.checkMobilePhoneBindCode = function (code) {
		
	var result = $.ajax({
		url: api.config.url + '/serviceAg/rest/messageMsgRestWrapper/checkMobilePhoneBindCode',
		type: 'POST',
		contentType:'application/json; charset=utf-8',
		cache : false,
		dataType:'json',
		async: false,
		data: String(code),
		success: function (response) {
			return response;
		}
	});
	return result.responseJSON;

};

api.promotion = function (dateStart, dateEnd, page, pageSize, type) {
		
	var result = $.ajax({
		url: api.config.url + '/serviceAg/rest/promotionQueryService/promotion/' + dateStart + '/' + dateEnd + '/' + page + '/' + pageSize + '/' + type + '/-1',
		type: 'GET',
		contentType:'application/json; charset=utf-8',
		cache : false,
		dataType:'json',
		async: false,
		success: function (response) {
			return response;
		}
	});
	return {
		count : !result.getResponseHeader('count') ? 0 : parseInt(result.getResponseHeader('count')),
		data : result.responseJSON
	};

};

api.betRecord = function (dateStart, dateEnd, page, pageSize, type) {
	
	var srvurl;	
	
	if(type=="")
	{
		type=="1";
	}
	if(type=="1")
	{
		srvurl = api.config.url + '/serviceAg/rest/asianHallQueryService/betRecord/' + dateStart + '/' + dateEnd + '/' + page + '/' + pageSize ;
	}
	else if(type=="2")
	{
		srvurl = api.config.url + '/serviceAg/rest/agQueryService/betRecord/' + dateStart + '/' + dateEnd + '/' + page + '/' + pageSize 
	}
	else if(type=="4")
	{
		srvurl = api.config.url + '/serviceAg/rest/hkQueryService/betRecord/' + dateStart + '/' + dateEnd + '/' + page + '/' + pageSize 
	}
	else if(type=="5")
	{
		srvurl = api.config.url + '/serviceAg/rest/ctQueryService/betRecord/' + dateStart + '/' + dateEnd + '/' + page + '/' + pageSize 
	}
	else if(type=="6")
	{
		srvurl = api.config.url + '/serviceAg/rest/gdQueryService/betRecord/' + dateStart + '/' + dateEnd + '/' + page + '/' + pageSize 
	}
	else if(type=="8")
	{
		srvurl = api.config.url + '/serviceAg/rest/kenoQueryService/betRecord/' + dateStart + '/' + dateEnd + '/' + page + '/' + pageSize 
	}
	else if(type=="16")
	{
		srvurl = api.config.url + '/serviceAg/rest/xjQueryService/betRecord/' + dateStart + '/' + dateEnd + '/' + page + '/' + pageSize 
	}
	else if(type=="18")
	{
		srvurl = api.config.url + '/serviceAg/rest/agEgameQueryService/betRecord/' + dateStart + '/' + dateEnd + '/' + page + '/' + pageSize 
	}
	else if(type=="19")
	{
		srvurl = api.config.url + '/serviceAg/rest/dgLotteryQueryService/betRecord/' + dateStart + '/' + dateEnd + '/' + page + '/' + pageSize 
	}
	else if(type=="20")
	{
		srvurl = api.config.url + '/serviceAg/rest/dgStockQueryService/betRecord/' + dateStart + '/' + dateEnd + '/' + page + '/' + pageSize 
	}
	else if(type=="21")
	{
		srvurl = api.config.url + '/serviceAg/rest/agTexQueryService/betRecord/' + dateStart + '/' + dateEnd + '/' + page + '/' + pageSize 
	}
	else
	{
		srvurl = "";
	}
	
	var result = $.ajax({
		//url: api.config.url + '/serviceAg/rest/agQueryService/betRecord/' + dateStart + '/' + dateEnd + '/' + page + '/' + pageSize ,
		url: srvurl ,
		type: 'GET',
		contentType:'application/json; charset=utf-8',
		cache : false,
		dataType:'json',
		async: false,
		success: function (response) {
			return response;
		}
	});
	return {
		count : !result.getResponseHeader('count') ? 0 : parseInt(result.getResponseHeader('count')),
		data : result.responseJSON
	};

};

api.capitalFlow = function (dateStart, dateEnd, page, pageSize, type) {
		
	var result = $.ajax({
		url: api.config.url + '/serviceAg/rest/capitalFlowQueryService/capitalFlowUser/' + dateStart + '/' + dateEnd + '/' + page + '/' + pageSize + '/' + type + '/-1/-1',
		type: 'GET',
		contentType:'application/json; charset=utf-8',
		cache : false,
		dataType:'json',
		async: false,
		success: function (response) {
			return response;
		}
	});
	return {
		count : !result.getResponseHeader('count') ? 0 : parseInt(result.getResponseHeader('count')),
		data : result.responseJSON
	};

};

api.message = function (page, pageSize) {
		
	var result = $.ajax({
		url: api.config.url + '/serviceAg/rest/messageService/message/' + page + '/' + pageSize + '/-1',
		type: 'GET',
		contentType:'application/json; charset=utf-8',
		cache : false,
		dataType:'json',
		async: false,
		success: function (response) {
			return response;
		}
	});
	return {
		count : !result.getResponseHeader('count') ? 0 : parseInt(result.getResponseHeader('count')),
		data : result.responseJSON
	};

};

api.messageCount = function () {
		
	var result = $.ajax({
		url: api.config.url + '/serviceAg/rest/messageService/messageCount' ,
		type: 'GET',
		contentType:'application/json; charset=utf-8',
		cache : false,
		dataType:'json',
		async: false,
		success: function (response) {
			return response;
		}
	});
	return result.responseJSON;
	
};

api.deleteAllMessage = function (data) {

	var result = $.ajax({
		url: api.config.url + '/serviceAg/rest/messageService/deleteAllMessage',
		contentType:'application/json; charset=utf-8',
		data: JSON.stringify(data),
		type: 'POST',
		cache : false,
		dataType:'json',
		async: false,
		success: function (response) {
			return response;
		}
	});
	return result.responseJSON;
};

api.messageRead = function (messageId) {

	$.ajax({
		url: api.config.url + '/serviceAg/rest/messageService/readMessage',
		contentType:'application/json; charset=utf-8',
		data:String(messageId),
		type: 'PUT',
		cache : false,
		dataType:'json',
		success: function (response) {
		}
	});
};

api.readScanCode = function () {

	var result =$.ajax({
		url: api.config.url + '/serviceAg/rest/subCompanyBankCardService/bankCard/user',
		type: 'GET',
		async:false,
		cache : false,
		dataType:'json',
		contentType:'application/json; charset=utf-8',
		success: function (response) {
			
			return response;
		}
	});
	
	return result.responseJSON;
};

api.queryDrawalOrder = function (dateStart, dateEnd, page, pageSize, type) {
		
	var result = $.ajax({
		url: api.config.url + '/serviceAg/rest/drawalQuery/queryDrawalOrder/' + dateStart + '/' + dateEnd + '/' + page + '/' + pageSize + '/' + type ,
		type: 'GET',
		contentType:'application/json; charset=utf-8',
		cache : false,
		dataType:'json',
		async: false,
		success: function (response) {
			return response;
		}
	});
	return {
		count : !result.getResponseHeader('count') ? 0 : parseInt(result.getResponseHeader('count')),
		data : result.responseJSON
	};

};

api.depositOrder = function (dateStart, dateEnd, page, pageSize, type) {
		
	var result = $.ajax({
		url: api.config.url + '/serviceAg/rest/queryService/depositOrder/' + dateStart + '/' + dateEnd + '/' + page + '/' + pageSize + '/' + type +'/-1' ,
		type: 'GET',
		contentType:'application/json; charset=utf-8',
		cache : false,
		dataType:'json',
		async: false,
		success: function (response) {
			return response;
		}
	});
	return {
		count : !result.getResponseHeader('count') ? 0 : parseInt(result.getResponseHeader('count')),
		data : result.responseJSON
	};

};

api.rescindOrder = function (orderId) {
	
	var result = $.ajax({
	        type: 'PUT',
			url: api.config.url + '/serviceAg/rest/traditionalDepositProcess/depositOrder/cancel',
			async: false,
			cache:false,
	        data: orderId+"",
	        dataType: 'json',
			contentType:'application/json; charset=utf-8',
	        success: function(response){
	        	
	        	return response;
		    }
	    });
		
	return result;
	
};

// 我已付款，通知后台审核
api.payOrder = function payOrder(id){
	
	var result = $.ajax({
        type: "PUT",
        url: api.config.url + '/serviceAg/rest/traditionalDepositProcess/depositOrder/confirm' ,
		async: false,
        cache:false,
        data:id+"",
        dataType: "json",
		contentType:'application/json; charset=utf-8',
        success: function (response) {
			return response;
		}
	});
	
	return result.responseJSON;
}

api.queryUserTransferMoney = function (dateStart, dateEnd, page, pageSize, type, status) {
		
	var result = $.ajax({
		url: api.config.url + '/serviceAg/rest/userCentralService/queryUserTransferMoney/' + dateStart + '/' + dateEnd + '/' + page + '/' + pageSize + '/' + type +'/' + status ,
		type: 'GET',
		contentType:'application/json; charset=utf-8',
		cache : false,
		dataType:'json',
		async: false,
		success: function (response) {
			return response;
		}
	});
	return {
		count : !result.getResponseHeader('count') ? 0 : parseInt(result.getResponseHeader('count')),
		data : result.responseJSON
	};

};

api.checkEmailBindCode = function (code) {
		
	var result = $.ajax({
		url: api.config.url + '/serviceAg/rest/messageMsgRestWrapper/checkEmailBindCode',
		type: 'POST',
		contentType:'application/json; charset=utf-8',
		cache : false,
		dataType:'json',
		async: false,
		data: code,
		success: function (response) {
			return response;
		}
	});
	return result.responseJSON;

};

api.bindUserTrueName = function (name) {
		
	var result = $.ajax({
		url: api.config.url + '/serviceAg/rest/userCentralService/bindUserTrueName',
		type: 'POST',
		contentType:'application/json; charset=utf-8',
		cache : false,
		dataType:'json',
		async: false,
		data: name,
		success: function (response) {
			return response;
		}
	});
	return result.responseJSON;

};

api.getUserInfoBindPhone = function () {

	var result = $.ajax({
		url: api.config.url + '/serviceAg/rest/messageMsgRestWrapper/userInfoBind/11',
		type: 'GET',
		contentType:'application/json; charset=utf-8',
		cache : false,
		dataType:'json',
		async: false,
		success: function (response) {
			return response;
		}
	});
	return result.responseJSON;

};

api.getUserInfoBindEmail = function () {

	var result = $.ajax({
		url: api.config.url + '/serviceAg/rest/messageMsgRestWrapper/userInfoBind/12',
		type: 'GET',
		contentType:'application/json; charset=utf-8',
		cache : false,
		dataType:'json',
		async: false,
		success: function (response) {
			return response;
		}
	});
	return result.responseJSON;

};

api.getUserInfoBindName = function () {

	var result = $.ajax({
		url: api.config.url + '/serviceAg/rest/messageMsgRestWrapper/userInfoBind/13',
		type: 'GET',
		contentType:'application/json; charset=utf-8',
		cache : false,
		dataType:'json',
		async: false,
		success: function (response) {
			return response;
		}
	});
	return result.responseJSON;

};

api.gamesList = function () {

	var result = $.ajax({
		url: api.config.url + '/serviceAg/rest/applicationService/games/' + api.config.subCompany,
		type: 'GET',
		contentType:'application/json; charset=utf-8',
		cache : false,
		dataType:'json',
		async: false,
		success: function (response) {
			return response;
		}
	});
	return result.responseJSON;

};

api.webNotices = function () {

	var result = $.ajax({
		url: api.config.url + '/serviceAg/rest/userCentralService/queryNoticeInfos/' + api.config.subCompany,
		type: 'GET',
		contentType:'application/json; charset=utf-8',
		cache : false,
		dataType:'json',
		async: false,
		success: function (response) {
			return response;
		}
	});
	return result.responseJSON;

};


api.userBasicInfo = function () {

	$.ajax({
		url: api.config.url + '/serviceAg/rest/userCentralService/userBasicInfo',
		type: 'GET',
		cache : false,
		dataType:'json',
		success: function (response) {
			//console.log(response);
		}
	});
		

};

api.gamePlay = function (gameId, gameType, isMobile) {
	var data = {
		gameId : gameId,
		gameType : gameType,
		isMobile : isMobile,
		language : 'zh',
	};
	var result = $.ajax({
		url: api.config.url + '/serviceAg/rest/application/login?' + $.now(),
		type: 'POST',
		contentType:'application/json; charset=utf-8',
		cache : false,
		dataType:'json',
		async: false,
		data: JSON.stringify(data),
		success: function (response) {
			return response;
		}
	});
	return result.responseJSON;
};

api.getUserBasicInfo = function () {

	var result = $.ajax({
		url: api.config.url + '/serviceAg/rest/userCentralService/userBasicInfo?' + $.now(),
		type: 'GET',
		contentType:'application/json; charset=utf-8',
		cache : false,
		dataType:'json',
		async: false,
		success: function (response) {
			return response
			//console.log(response);
		}
	});
	return result.responseJSON;
};

api.postUserBasicInfo = function (data) {

	var result = $.ajax({
		url: api.config.url + '/serviceAg/rest/userCentralService/userBasicInfo',
		type: 'POST',
		contentType:'application/json; charset=utf-8',
		cache : false,
		dataType:'json',
		async: false,
		data: JSON.stringify(data),
		success: function (response) {
			return response;
		}
	});
	return result.responseJSON;
};

api.putUserBasicInfo = function (data) {

	var result = $.ajax({
		url: api.config.url + '/serviceAg/rest/userCentralService/userBasicInfo',
		type: 'PUT',
		contentType:'application/json; charset=utf-8',
		cache : false,
		dataType:'json',
		async: false,
		data: JSON.stringify(data),
		success: function (response) {
			return response;
		}
	});
	return result.responseJSON;
};

api.putUserBasicInfo.address = function (data) {

	var result = $.ajax({
		url: api.config.url + '/serviceAg/rest/userCentralService/userBasicInfo/address',
		type: 'PUT',
		contentType:'application/json; charset=utf-8',
		cache : false,
		dataType:'json',
		async: false,
		data: JSON.stringify(data),
		success: function (response) {
			return response;
		}
	});
	return result.responseJSON;
};

api.queryUserBankInfo = function (data) {

	var result = $.ajax({
		url: api.config.url + '/serviceAg/rest/userBankInfoService/queryUserBankInfo',
		type: 'GET',
		contentType:'application/json; charset=utf-8',
		cache : false,
		dataType:'json',
		async: false,
		data: JSON.stringify(data),
		success: function (response) {
			return response;
		}
	});
	return result.responseJSON;
};

api.postUserBankInfo = function (data) {

	var result = $.ajax({
		url: api.config.url + '/serviceAg/rest/userBankInfoService/userBankInfo',
		type: 'POST',
		contentType:'application/json; charset=utf-8',
		cache : false,
		dataType:'json',
		async: false,
		data: JSON.stringify(data),
		success: function (response) {
			return response;
		}
	});
	return result.responseJSON;
};

api.putUserBankInfo = function (data) {

	var result = $.ajax({
		url: api.config.url + '/serviceAg/rest/userBankInfoService/updUserBankInfo',
		type: 'PUT',
		contentType:'application/json; charset=utf-8',
		cache : false,
		dataType:'json',
		async: false,
		data: JSON.stringify(data),
		success: function (response) {
			return response;
		}
	});
	return result.responseJSON;
};

api.getModifyInfoCode = function (data) {

	var result = $.ajax({
		url: api.config.url + '/serviceAg/rest/messageMsgRestWrapper/modifyInfoCode',
		type: 'POST',
		contentType:'application/json; charset=utf-8',
		cache : false,
		dataType:'json',
		async: false,
		data: JSON.stringify(data),
		success: function (response) {
			return response;
		}
	});
	return result.responseJSON;
};






api.pubkey = `-----BEGIN PUBLIC KEY-----
	MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAgeijYUoha+DRl7U2P9h8
	+YyffrXtiAtrUUR2plRdqWWp+xWCO8NrczH+dVkVELAxz80b60L1QFwDm1fJa9Ka
	66plAIn8j+i9kXtRJGS29ghCVsshBwHsFHozqsp/GQVF7pjNnZo07WD7flktkKcr
	dkrRILdEx7DBqhGAlSEbOg5MOXfDiHbGJ3HQgatiSWcu5EWGWmfoBPm64xloY42R
	xp0bL+jO9zhxvOPa4BfXXt4Xu2C4Ik+rMmIUKym5bvQUr0k9pa3QMa13JJueI9Op
	n3/Y3qCiyTDAUqRx25faC4ORoQoFh7AgYZwX9WYkBM2eURCCXYNGxQRHz8pVlWHE
	HwIDAQAB
-----END PUBLIC KEY-----`;
api.pubkeyPayPass = `-----BEGIN PUBLIC KEY-----
	MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAgeijYUoha+DRl7U2P9h8
	+YyffrXtiAtrUUR2plRdqWWp+xWCO8NrczH+dVkVELAxz80b60L1QFwDm1fJa9Ka
	66plAIn8j+i9kXtRJGS29ghCVsshBwHsFHozqsp/GQVF7pjNnZo07WD7flktkKcr
	dkrRILdEx7DBqhGAlSEbOg5MOXfDiHbGJ3HQgatiSWcu5EWGWmfoBPm64xloY42R
	xp0bL+jO9zhxvOPa4BfXXt4Xu2C4Ik+rMmIUKym5bvQUr0k9pa3QMa13JJueI9Op
	n3/Y3qCiyTDAUqRx25faC4ORoQoFh7AgYZwX9WYkBM2eURCCXYNGxQRHz8pVlWHE
	HwIDAQAB
-----END PUBLIC KEY-----`;
api.pubkeyPassword = `-----BEGIN PUBLIC KEY-----
	MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAgeijYUoha+DRl7U2P9h8
	+YyffrXtiAtrUUR2plRdqWWp+xWCO8NrczH+dVkVELAxz80b60L1QFwDm1fJa9Ka
	66plAIn8j+i9kXtRJGS29ghCVsshBwHsFHozqsp/GQVF7pjNnZo07WD7flktkKcr
	dkrRILdEx7DBqhGAlSEbOg5MOXfDiHbGJ3HQgatiSWcu5EWGWmfoBPm64xloY42R
	xp0bL+jO9zhxvOPa4BfXXt4Xu2C4Ik+rMmIUKym5bvQUr0k9pa3QMa13JJueI9Op
	n3/Y3qCiyTDAUqRx25faC4ORoQoFh7AgYZwX9WYkBM2eURCCXYNGxQRHz8pVlWHE
	HwIDAQAB
-----END PUBLIC KEY-----`;

/*
$(function (){
	api.init();	
	var login = api.hasUserLogin();
	api.isLogin = login.status ? true : false;
	var status = av.getAuthStatus();
	if (status == 'member'){
		if (!api.isLogin) {
			av.logout();
			window.location.reload();
		}
	}
	else {
		if (api.isLogin) api.logout();
	}
});

*/










//支付渠道列表
var  onlineCode ={1:"yeePay",2:"thPay",3:"mopay",4:"thWeixinPay",5:"yeePayWeiXin",
        6:"ktPay",7:"ffPay",8:"gsPay",9:"gsWeixinPay",10:"gsZhifubaoPay",
        11:"qfPay",12:"ysPay",13:"yhPay",14:"ddbPay",15:"zfPay",16:"ydPay",
        17:"jhPay",18:"hcPay",19:"htPay",20:"xjPay",22:"jhzPay",23:"qyfPay",24:"yinHePay",25:"gcPay"
        ,26:"gPay",27:"tgPay",28:"xrPay",29:"oltPay",30:"cPay",31:"smaPay",32:"gwPay",33:"jbPay"};

var payParam = {
	 quickPay:{ '6':{"depositType":15,"depositSubType":11,"amount": 0,"yeePayBankCode":"tlwxcode"},
		        '17':{"depositType":1107,"depositSubType":8,"amount": 0,"yeePayBankCode":"tlwxcode"},
		        '18':{"depositType":1108,"depositSubType":11,"amount": 0,"yeePayBankCode":"tlwxcode"},
	 			'20':{"depositType":1110,"depositSubType":11,"amount": 0,"yeePayBankCode":"tlwxcode"},
	 			'27':{"depositType":1117,"depositSubType":11,"amount": 0,"yeePayBankCode":"tlwxcode"},
	 			'30':{"depositType":1120,"depositSubType":11,"amount": 0,"yeePayBankCode":"tlwxcode"},
                '31':{"depositType":1121,"depositSubType":11,"amount": 0,"yeePayBankCode":"tlwxcode"},
                '32':{"depositType":1122,"depositSubType":11,"amount": 0,"yeePayBankCode":"tlwxcode"},
                '33':{"depositType":1123,"depositSubType":11,"amount": 0,"yeePayBankCode":"tlwxcode"}
              },
 quickPayCode:['6','17','18','20','27','30','31','32','33'],
	   online:{  
	   		     '1':{"depositType":10,"amount": 0,"yeePayBankCode":"tlwxcode"},
		         '2':{"depositType":11,"amount": 0,"yeePayBankCode":"tlwxcode"},
		   		 '6':{"depositType":15,"depositSubType":1,"amount": 0,"yeePayBankCode":"tlwxcode"},
		   		 '7':{"depositType":16,"depositSubType":1,"amount": 0,"yeePayBankCode":"tlwxcode"},
		         '8':{"depositType":17,"amount": 0,"yeePayBankCode":"tlwxcode"},
		        '12':{"depositType":1102,"depositSubType":1,"amount": 0,"yeePayBankCode":"tlwxcode"},
		        '14':{"depositType":1104,"depositSubType":1,"amount": 0,"yeePayBankCode":"tlwxcode"},
		        '15':{"depositType":1105,"depositSubType":1,"amount": 0,"yeePayBankCode":"tlwxcode"},
		        '16':{"depositType":1106,"depositSubType":1,"amount": 0,"yeePayBankCode":"tlwxcode"},
		        '17':{"depositType":1107,"depositSubType":1,"amount": 0,"yeePayBankCode":"tlwxcode"},
		        '18':{"depositType":1108,"depositSubType":1,"amount": 0,"yeePayBankCode":"tlwxcode"},
		        '19':{"depositType":1109,"depositSubType":1,"amount": 0,"yeePayBankCode":"tlwxcode"},
		        '20':{"depositType":1110,"depositSubType":1,"amount": 0,"yeePayBankCode":"tlwxcode"},
		        '23':{"depositType":1113,"depositSubType":1,"amount": 0,"yeePayBankCode":"tlwxcode"},
		        '25':{"depositType":1115,"depositSubType":1,"amount": 0,"yeePayBankCode":"tlwxcode"},
		        '27':{"depositType":1117,"depositSubType":1,"amount": 0,"yeePayBankCode":"tlwxcode"},
		        '30':{"depositType":1120,"depositSubType":1,"amount": 0,"yeePayBankCode":"tlwxcode"},
		        '31':{"depositType":1121,"depositSubType":1,"amount": 0,"yeePayBankCode":"tlwxcode"},
		        '32':{"depositType":1122,"depositSubType":1,"amount": 0,"yeePayBankCode":"tlwxcode"},
		        '33':{"depositType":1123,"depositSubType":1,"amount": 0,"yeePayBankCode":"tlwxcode"}
	          }	,
   onlineCode:['1','2','6','7','8','12','14','15','16','17','18','19','20','23','25','27','30','31','32','33'], 
       union:{
    	   		'17':{"depositType":1107,"depositSubType":9,"amount": 0,"yeePayBankCode":"tlwxcode"},
    	   		'20':{"depositType":1110,"depositSubType":7,"amount": 0,"yeePayBankCode":"tlwxcode"},
    	   		'23':{"depositType":1113,"depositSubType":7,"amount": 0,"yeePayBankCode":"tlwxcode"},
    	   		'24':{"depositType":1114,"depositSubType":7,"amount": 0,"yeePayBankCode":"tlwxcode"},
    	   		'27':{"depositType":1117,"depositSubType":7,"amount": 0,"yeePayBankCode":"tlwxcode"},
    	   		'28':{"depositType":1118,"depositSubType":7,"amount": 0,"yeePayBankCode":"tlwxcode"},
    	   		'29':{"depositType":1119,"depositSubType":7,"amount": 0,"yeePayBankCode":"tlwxcode"},
    	   		'31':{"depositType":1121,"depositSubType":7,"amount": 0,"yeePayBankCode":"tlwxcode"},
    	   		'32':{"depositType":1122,"depositSubType":7,"amount": 0,"yeePayBankCode":"tlwxcode"}
       		 },
    unionCode:['17','20','23','24','27','28','29','31','32'],
	   wechat:{  
	   			 '4':{"depositType":13,"depositSubType":2,"amount": 0,"yeePayBankCode":"WEIXIN"},
		         '6':{"depositType":15,"depositSubType":2,"amount": 0,"yeePayBankCode":"tlwxcode"},
		         '7':{"depositType":16,"depositSubType":2,"amount": 0,"yeePayBankCode":"tlwxcode"},
		         '9':{"depositType":18,"depositSubType":2,"amount": 0,"yeePayBankCode":"tlwxcode"},
		        '12':{"depositType":1102,"depositSubType":2,"amount": 0,"yeePayBankCode":"tlwxcode"},
		        '13':{"depositType":1103,"depositSubType":2,"amount": 0,"yeePayBankCode":"tlwxcode"},
		        '14':{"depositType":1104,"depositSubType":2,"amount": 0,"yeePayBankCode":"tlwxcode"},
		        '15':{"depositType":1105,"depositSubType":2,"amount": 0,"yeePayBankCode":"tlwxcode"},
		        '16':{"depositType":1106,"depositSubType":2,"amount": 0,"yeePayBankCode":"tlwxcode"},
		        '17':{"depositType":1107,"depositSubType":2,"amount": 0,"yeePayBankCode":"tlwxcode"},
		        '18':{"depositType":1108,"depositSubType":2,"amount": 0,"yeePayBankCode":"tlwxcode"},
		        '20':{"depositType":1110,"depositSubType":2,"amount": 0,"yeePayBankCode":"tlwxcode"},
		        '22':{"depositType":1112,"depositSubType":2,"amount": 0,"yeePayBankCode":"tlwxcode"},
		        '23':{"depositType":1113,"depositSubType":2,"amount": 0,"yeePayBankCode":"tlwxcode"},
		        '25':{"depositType":1115,"depositSubType":2,"amount": 0,"yeePayBankCode":"tlwxcode"},
		        '27':{"depositType":1117,"depositSubType":2,"amount": 0,"yeePayBankCode":"tlwxcode"},
		        '28':{"depositType":1118,"depositSubType":2,"amount": 0,"yeePayBankCode":"tlwxcode"},
		        '29':{"depositType":1119,"depositSubType":2,"amount": 0,"yeePayBankCode":"tlwxcode"},
		        '30':{"depositType":1120,"depositSubType":2,"amount": 0,"yeePayBankCode":"tlwxcode"},
		        '31':{"depositType":1121,"depositSubType":2,"amount": 0,"yeePayBankCode":"tlwxcode"}
	          },
   wechatCode:['4','6','7','9','12','13','14','15','16','17','18','20','22','23','25','27','28','29','30','31'],       
	   alipay:{ 
		         '6':{"depositType":15,"depositSubType":3,"amount": 0,"yeePayBankCode":"tlwxcode"},
		         '7':{"depositType":16,"depositSubType":3,"amount": 0,"yeePayBankCode":"tlwxcode"},
		        '10':{"depositType":19,"depositSubType":2,"amount": 0,"yeePayBankCode":"tlwxcode"},
		        '12':{"depositType":1102,"depositSubType":3,"amount": 0,"yeePayBankCode":"tlwxcode"},
		        '13':{"depositType":1103,"depositSubType":3,"amount": 0,"yeePayBankCode":"tlwxcode"},
		        '14':{"depositType":1104,"depositSubType":3,"amount": 0,"yeePayBankCode":"tlwxcode"},
		        '15':{"depositType":1105,"depositSubType":3,"amount": 0,"yeePayBankCode":"tlwxcode"},
		        '16':{"depositType":1106,"depositSubType":3,"amount": 0,"yeePayBankCode":"tlwxcode"},
		        '17':{"depositType":1107,"depositSubType":3,"amount": 0,"yeePayBankCode":"tlwxcode"},
		        '18':{"depositType":1108,"depositSubType":3,"amount": 0,"yeePayBankCode":"tlwxcode"},
		        '20':{"depositType":1110,"depositSubType":3,"amount": 0,"yeePayBankCode":"tlwxcode"},
		        '23':{"depositType":1113,"depositSubType":3,"amount": 0,"yeePayBankCode":"tlwxcode"},
		        '24':{"depositType":1114,"depositSubType":3,"amount": 0,"yeePayBankCode":"tlwxcode"},
		        '25':{"depositType":1115,"depositSubType":3,"amount": 0,"yeePayBankCode":"tlwxcode"},
		        '26':{"depositType":1116,"depositSubType":3,"amount": 0,"yeePayBankCode":"tlwxcode"},
		        '28':{"depositType":1118,"depositSubType":3,"amount": 0,"yeePayBankCode":"tlwxcode"},
		        '29':{"depositType":1119,"depositSubType":3,"amount": 0,"yeePayBankCode":"tlwxcode"},
		        '30':{"depositType":1120,"depositSubType":3,"amount": 0,"yeePayBankCode":"tlwxcode"},
		        '31':{"depositType":1121,"depositSubType":3,"amount": 0,"yeePayBankCode":"tlwxcode"}
	          },
   alipayCode:['6','7','10','12','13','14','15','16','17','18','20','23','24','25','26','28','29','30','31'],         
	       qq:{ 
    	    	 '6':{"depositType":15,"depositSubType":4,"amount": 0,"yeePayBankCode":"tlwxcode"},
	    	     '7':{"depositType":16,"depositSubType":4,"amount": 0,"yeePayBankCode":"tlwxcode"},
	    	    '12':{"depositType":1102,"depositSubType":4,"amount": 0,"yeePayBankCode":"tlwxcode"},
	    	    '14':{"depositType":1104,"depositSubType":4,"amount": 0,"yeePayBankCode":"tlwxcode"},
	    	    '15':{"depositType":1105,"depositSubType":4,"amount": 0,"yeePayBankCode":"tlwxcode"},
	    	    '16':{"depositType":1106,"depositSubType":4,"amount": 0,"yeePayBankCode":"tlwxcode"},
	    	    '17':{"depositType":1107,"depositSubType":6,"amount": 0,"yeePayBankCode":"tlwxcode"},
	    	    '18':{"depositType":1108,"depositSubType":4,"amount": 0,"yeePayBankCode":"tlwxcode"},
	    	    '19':{"depositType":1109,"depositSubType":4,"amount": 0,"yeePayBankCode":"tlwxcode"},
	    	    '20':{"depositType":1110,"depositSubType":4,"amount": 0,"yeePayBankCode":"tlwxcode"},
	    	    '22':{"depositType":1112,"depositSubType":4,"amount": 0,"yeePayBankCode":"tlwxcode"},
	    	    '23':{"depositType":1113,"depositSubType":4,"amount": 0,"yeePayBankCode":"tlwxcode"},
	    	    '25':{"depositType":1115,"depositSubType":4,"amount": 0,"yeePayBankCode":"tlwxcode"},
	    	    '27':{"depositType":1117,"depositSubType":4,"amount": 0,"yeePayBankCode":"tlwxcode"},
	    	    '28':{"depositType":1118,"depositSubType":4,"amount": 0,"yeePayBankCode":"tlwxcode"},
	    	    '29':{"depositType":1119,"depositSubType":4,"amount": 0,"yeePayBankCode":"tlwxcode"},
	    	    '30':{"depositType":1120,"depositSubType":4,"amount": 0,"yeePayBankCode":"tlwxcode"},
	    	    '31':{"depositType":1121,"depositSubType":4,"amount": 0,"yeePayBankCode":"tlwxcode"},
	    	    '33':{"depositType":1123,"depositSubType":4,"amount": 0,"yeePayBankCode":"tlwxcode"}
	          },
	   qqCode:['6','7','12','13','14','15','16','17','18','19','20','22','23','25','27','28','29','30','31','33'],
	    baidu:{
	    		'16':{"depositType":1106,"depositSubType":5,"amount": 0,"yeePayBankCode":"tlwxcode"},
	    		'20':{"depositType":1110,"depositSubType":5,"amount": 0,"yeePayBankCode":"tlwxcode"},
	    		'23':{"depositType":1113,"depositSubType":5,"amount": 0,"yeePayBankCode":"tlwxcode"}
	          },
	baiduCode:['16','20','23'],
	jdWallet:{
	   			'23':{"depositType":1113,"depositSubType":6,"amount": 0,"yeePayBankCode":"tlwxcode"},
	   			'25':{"depositType":1115,"depositSubType":6,"amount": 0,"yeePayBankCode":"tlwxcode"},
	   			'28':{"depositType":1118,"depositSubType":6,"amount": 0,"yeePayBankCode":"tlwxcode"},
	   			'31':{"depositType":1121,"depositSubType":6,"amount": 0,"yeePayBankCode":"tlwxcode"},
	   			'32':{"depositType":1122,"depositSubType":6,"amount": 0,"yeePayBankCode":"tlwxcode"}
			 },
jdWalletCode:['23','25','28','31','32'],
  };



api.uuid = function(len, radix) {
	var chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'.split('');
	var uuid = [],
		i;
	radix = radix || chars.length;

	if (len) {
		// Compact form
		for (i = 0; i < len; i++) uuid[i] = chars[0 | Math.random() * radix];
	} else {
		// rfc4122, version 4 form
		var r;

		// rfc4122 requires these characters
		uuid[8] = uuid[13] = uuid[18] = uuid[23] = '-';
		uuid[14] = '4';

		// Fill in random data. At i==19 set the high bits of clock sequence as
		// per rfc4122, sec. 4.1.5
		for (i = 0; i < 36; i++) {
			if (!uuid[i]) {
				r = 0 | Math.random() * 16;
				uuid[i] = chars[(i == 19) ? (r & 0x3) | 0x8 : r];
			}
		}
	}

	return uuid.join('');
}