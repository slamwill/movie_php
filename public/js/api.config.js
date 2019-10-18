/*公用提示信息*/
var SERVICE_NAME = 'serviceAg';
var GLOBALS_MSG = {
	LoginFailedException : "用户名或密码错误",
	UnknowException : "系统内部错误，请联系客服",
	GameIsClosedException : "游戏关闭维护中",
    "NullVCodeException":"验证码不能为空",
    "NullUserNameException":"用户名不能为空",
    "UserNameExistedException":"该用户名已被注册",
    "NullPasswordException":"密码不能为空",
    "InvalidPasswordFormatException":"密码格式错误",
    "InvalidPhoneNumberFormatException":"手机号码格式错误",
    "PhoneNumberExistedException":"手机号已被注册",
    "CheckCodeTimeOutException":"验证码已过期",
    "VCodeErrorException":"验证码错误",
	"InvalidCheckCodeException":"验证码错误",
	"InvalidUserNameFormatException":"用户昵称输入不合法!",
};
var PromotionsTypes = {
	"10": "首次优惠",
	"11": "手机绑定优惠卷",
	"12": "Email绑定优惠卷",
	"13": "真实姓名优惠卷",
	"20": "会员存款",
	"21": "全勤奖励",
	"22": "假日赠送",
	"23": "激励奖金",
	"24": "QQ签名奖励",
	"25": "百家乐顺子奖",
	"26": "体育串关嘉奖",
	"27": "AG过关奖励",
	"28": "百家乐连赢",
	"29": "生日送礼",
	"30": "免费赠送活动",
	"31": "推荐好友",
	"32": "积分兑换",
	"33": "积分抽奖"
};

/*以下为对接config.js，如有更新，下面直接替换*/
/*公用提示信息*/
var A_MSG = {nodata:"无数据",showdetail:"查看详情",evershow:"不在显示？",apiErr:"API 接口异常",showRemitDetail:"查看汇款详情",confirmRemit:"确认汇款",
	nopay:"未支付",unknownexception:"系统内部错误，请联系客服",browser360:"由于360浏览器功能限制，请按 Ctrl+D 手动收藏！",remitMoney:"我已汇款",rescindOrder:"撤销该订单",
	check:"审核",nocheck:"未审核",recentlyrecord:"近期记录",browsernotsupport:"\n\t对不起，您的浏览器不支持此操作！\n\n\t请您使用菜单栏或ctrl+D收藏本站。",
	acctnotnull:"账号不能为空",pswnotnull:"密钥不能为空",rescindSuccess:"撤销成功",paySuccess:"提交成功，等待管理员审核",confirmRescind:"确定要撤销该订单吗？",
	notsupportsethome:"您的浏览器不支持，请按照下面步骤操作：<br/>1.点击浏览器右上角“设置”按钮。<br/>2.点选“打开特定网页或一组网页”，点击“设置网页”。<br/>3.在添加新网页后输入：&url，点击其他地址后“X”，最后点击“确定”。",
	sethome:"此操作被浏览器拒绝！\n请在浏览器地址栏输入“about:config”并回车\n然后将 [signed.applets.codebase_principal_support]的值设置为'true',双击即可。",
	notnull:"不能为空",mustbeurl:"必须是url",submit:"提交",submiting:"正在提交...",tranText:"转账",toBank:"前往银行汇款",history:"历史记录",tryuser:"试玩账号,不能进入会员中心",
	notgreaterdate:"开始时间不能大于结束时间",datenotnull:"时间不能为空",wangluofanmang:"网络繁忙!",notSupportForGameHK:"该浏览器或模式不支持HK厅，请使用360浏览器、遨游浏览器、猎豹浏览器、搜狗浏览器的极速模式，或Google浏览器"};

/*登录提示信息*/
var LOGIN_MSG = {username:"用户名",usernamenotnull:"用户名不能为空，请输入用户名。",
		verifycodenotnull:"验证码不能为空，请输入验证码。",usenameorpswerr:"用户名或密码错误",
		verifycodeerr:"验证码错误",noright:"你没有登录后台的权限",login:"登录",logining:"登录中...",
		submit:"提交",submiting:"正在提交...",islogined:"您已登陆",clogout:"您确定要退出吗？",
		account:"输入账号 ",psw:"新密码",epsw:"输入新密码",vcode:"验证码",cipsw:"输入确认密码",cpsw:"确认密码", 
		oipsw:"输入原密码",opsw:"原密码", resetPwdSuccess:"密码重置成功",agreeForRule:"必须同意开户协议",
		10:"会话超时，你当前ip是:", 20:"您被系统踢出",pswnotnull:"密码不能为空，请输入密码。",mPwd:"重置密码",
		changea:"看不清楚？点击换另一张",logina:"登&nbsp;录",noaccount:"还没有账户？",reg:"直接开户",
		freeplay:"免费试玩",loginerr:"登录失败",nologin:"您还未登陆，或登录已过期",userlogin:"会员登录",
		userFreeze:"您的帐号已被冻结,请联系客服!",err403:"您无权限访问，或登录已过期",need:"您需要登录，才能继续进行操作"};

/*注册提示信息*/
var REG_MSG = {usernamenotnull:"用户名不能为空",usernameforletter:"用户名必须以字母开头",oldpwdsamenewpwd:"新密码不能和原始密码相同",
		usernameerr:"用户名非法,不能包含特殊字符",usenamelen:"用户名的长度在6-12位之间",usenamelen4_10:"用户名的长度在4-10位之间",
		oldpwderr:"原始密码错误",usenamelen4_8:"用户名的长度在4-8位之间",
		pswnotnull:"密码不能为空",cpswnotnull:"确认密码不能为空",usernamereged:"该账户名已经被注册",loginPassNotNull:"请输入登录密码",tryPlay:"进入免费试玩游戏厅",
		enternumlen:"密码请输入6-12位数字或字母组合",pswnotsame:"两次密码输入不一致",payPswnotnull:"支付密码必须设置",nameMustBeChinese:"姓名必是中文或中文长度不对，请重新输入",
		qqnotnull:"QQ号码输入不能为空",namenotnull:"姓名输入不能为空",nameformaterr:"姓名只能包含汉字字母",nullmobile:"手机号码不能为空",
		mobileerr:"手机号码输入不合法!",PhoneNumberExistedException:"此手机号码已被注册",InvalidPhoneNumberFormatException:"手机号码输入不符合规范",
		NullQQNumberException:"QQ号码输入不能为空",QQNumberFormatException:"QQ号码输入不符合规范",UserNameExistedException:"用户名存在",InvalidSubcompanyException:"注册失败,请联系客服!"};

var REG_ERR = {UserNameExistedException:"用户名存在"};

/*重置密码错误消息*/
var RESET_PWD_ERR = {NullPasswordException:"用户输入密码为空",InvalidPasswordFormatException:"新密码不符合规范，请输入数字字母 长度6-12",
		PasswordNotRightException:"用户旧密码输入不正确",Forbidden:"无访问权限",
		SameOldPasswordNewPasswordException:"新密码和原密码不能相同"};

/*PT返回错误信息*/
var PT_ERROR={"GameIntegrationApiInvokeFailedException":"调用接口失败,请联系客服!",
		"UserNotExistedInGameException":"用户不存在,请联系客服!","InvalidAmountException":"请输入最多两位小数的金额!",
		GameIsMaintainingException:"游戏厅维护中 ",GameConfigNotExitedException:"游戏厅配置不存在",OperationIsPendingException:"后台处理中,请不要重复提交!"};

var DEPOSIT_TYPE = {10:"易宝支付",11:"TH支付",12:"MO宝支付",13:"TH微信在线支付",14:"易宝微信在线支付",15:"联通在线支付",16:"金付卡支付"}; /*存款类型 ,10 易宝支付*/
var ACTIVITY_TYPE = {"-1":"无活动",10:"首存优惠",at:"活动类型",ac:"优惠金额"}; /*活动类型： -1为无活动 10 首存优惠*/

var DEPOSIT_STATUS = {10:"未付款",20:"申请中",30:"已到账",40:"失败"}; /*存款状态 ,10 订单已提交，未付款 20 支付成功，待审核 30 支付成功，已到帐 40 失败*/

var DRAW_STATUS = {0:"申请中",1:"已通过",2:"不通过",3:"待处理"}; /*提款状态  0申请中,1通过2不通过 3 待处理*/

/*存款错误提示 */
var DEPOSIT_ERR = {LessThanMinDepositAmountException:"小于最低存款限额",InvalidAmountException:"小数点多余2位",
		NullYeePayBankCodeException:"银行通道代码为空",InvalidYeePayBankCodeException:"不合法的银行通道代码",
		LessThanMinDepositTimeIntervalException:"您需要等待&，才能继续存款操作",sec:"秒",minu:"分",
		LessThanFirstDepositPromotionMinAmountException:"获得首存优惠金额,首次存款需要大于1000元!",
		LessThanMinDepositAmountException:"小于最低存款限额",
		CannotFindYeePayAccountException:"找不到网站银行信息,请联系管理员!",SubCompanyBankCardNotExistedException:"子公司银行卡不存在",
		CanntFindPromotionConfigException:"无法提供首存优惠服务,请联系管理员!",CancelLastDepositOrderException:"您有未支付的订单，请先到充值记录撤销上一笔未支付的订单",
		HasPenddingDepositOrderException:"您有存款信息待审核,请等待审核后再提交新的存款!",
		"TryPlayerDrawlDepositErrorException":"您是试用会员无法提款/存款操作,请注册为真实会员!"}; 

/*提款审核 */
var DRAW_ERR = {UserNullOrNotLoginException:"用户为空或者没登录",DrawalOrderIsNullException:"提款订单不存在",
		DrawalOrderHasBeenAuditedException:"提款订单已审核",OperationNotAllowedException:"审核权限不足",
		AduitDrawalParamException:"提交参数为空",DrawalStatusNullOrErrorException:"订单状态为空或者类型不匹配",BalanceNotEnoughException:"账户余额不足",
		LessThanMinDepositAmountException:"小于最低存款限额",InvalidAmountException:"小数点多余2位"}; 

var YEEPAY_STATUS = {1:"启用",0:"停用",s:"成功",f:"失败",emsg:"您确定启用该易宝账号信息吗?",dmsg:"您确定停用该易宝账号信息吗?"}; /*易宝账号启用状态*/

var YEEPAY_ACTION = {company:{add:{url:"/"+ SERVICE_NAME +"/rest/yeePayAccountService/yeePayAccount/company",type:"POST",errmsg:"添加失败",msg:"添加成功"},edit:{url:"/"+ SERVICE_NAME +"/rest/yeePayAccountService/yeePayAccount/company/{subCompanyId}",type:"PUT",errmsg:"更新失败",msg:"更新成功"}},
		             subCompany:{add:{url:"/"+ SERVICE_NAME +"/rest/yeePayAccountService/yeePayAccount",type:"POST",errmsg:"添加失败",msg:"添加成功"},edit:{url:"/"+ SERVICE_NAME +"/rest/yeePayAccountService/yeePayAccount",type:"PUT",errmsg:"更新失败",msg:"更新成功"}}
                    };/*易宝添加修改配置*/

var CRRENCY_TYPE = {CNY:"人民币",THB:"泰铢"}; /*货币类型*/

var TRANSFER_TYPE = {1:"转入",2:"转出"};  /*转账类型*/
var TRANSFER_STATUS = {"-2":"待定",1:"成功",2:"失败",0:"失败"};  /*转账状态*/

/*重置信息异常提示*/
var RESET_UPD={"PhoneNumberValidCodeNullException":"输入的验证码已失效或不正确!"};

/* 传统存款类型 */
var TRANDITION_DEPOSIT_TYPE =  {
		10:"易宝支付",11:"TH支付",12:"MO宝支付",13:"TH微信在线支付",14:"易宝微信在线支付",15:"联通在线支付",16:"金付卡支付",20:"网银转账",
		17:"国盛在线支付",18:"国盛微信支付",19:"国盛支付宝支付",21:"柜台转帐",22:"ATM转帐",1101:"启付支付",
		23:"电话转账",24:"手机银行转账",1102:"银盛支付",
		30:"管理员存款",31:"传统存款",
		40:"代理转账平台",50:"推广代充",
		60:"支付宝存款",70:"微信存款"
	};

/* 传统存款消息*/
var TRANDITION_DEPOSIT_MSG =  {
		remitBank:"收款银行",acctBank:"开户行",
		remitAcct:"收款账号",depositDate:"存款日期",
		remiter:"收款人",owerBank:"所属银行",
		city:"城市",remark:"汇款附言",
		depositType:"存款方式",orderId:"订单号"
	};

var BANK =  {
		ZHI_FU_BAO:"支付宝账户",WEI_XING:"微信支付账户",
		/*PBC:"中国人民银行",*/CCB:"中国建设银行",
		ABC:"中国农业银行",ICBC:"中国工商银行",
		BOC:"中国银行",CMB:"招商银行",
		CIB:"兴业银行",BOB:"北京银行",
		BCM:"交通银行",CEB:"中国光大银行",
		CCB2:"中信银行",GDB:"广东发展银行",
		SDB:"深圳发展银行",SPDB:"上海浦东发展银行",
		CDB:"国家开发银行",HSBC:"汇丰银行",
		HXB:"华夏银行",EB:"恒丰银行",
		CMBC:"中国民生银行",PSBC:"邮政银行",
		BOS:"上海银行",BOP:"平安银行",
		RCB:"农商银行",BOGZ:"贵州银行",
		BOCD:"成都银行",BOHK:"汉口银行",
		BONMG:"内蒙古银行",/*新增*/ CHBHCNBT:"渤海银行",
	    GLBKCNBG:"桂林银行",NJCBCNBN:"南京银行",
	    BOJSCNBN:"江苏银行",CSCB_DEBIT:"长沙银行",
	      BKNBCN2N:"宁波银行",HZCBCN2H:"杭州银行",
	      GDHBCN22:"广东华兴银行",BTCBCNBJ:"包商银行",
	      BKSHCNBJ:"河北银行",ZJCBCN2N:"浙商银行",
	      BGBKCNBJ:"北部湾银行"
	};

// 银行对应的网址
var BANK_URL =  {
		PBC:"http://www.pbc.gov.cn/",CCB:"http://www.ccb.com/",
		ABC:"http://www.abchina.com/cn/",ICBC:"http://www.icbc.com.cn",
		BOC:"http://www.bank-of-china.com/",CMBC:"http://www.cmbc.com.cn/",
		CIB:"http://www.cib.com.cn/",BOB:"http://www.bccb.com.cn/",
		BCM:"http://www.cmbchina.com/",CEB:"http://www.cebbank.com/",
		CCB2:"http://bank.ecitic.com/",GDB:"http://www.cgbchina.com.cn/",
		SDB:"http://bank.pingan.com/",SPDB:"http://www.spdb.com.cn/",
		CDB:"http://www.cdb.com.cn/",HSBC:"http://www.hsbc.com.cn/",
		HXB:"http://www.hxb.com.cn/",EB:"http://www.egbank.com.cn/",
		CMB:"http://www.cmbchina.com/"
	};


// 易宝在线充值专用
var BANK2 =  {
		"ICBC-NET-B2C":"工商银行","CMBCHINA-NET-B2C":"招商银行",
		"ABC-NET-B2C":"中国农业银行","CCB-NET-B2C":"中国建设银行",
		"BCCB-NET-B2C":"北京银行","BOCO-NET-B2C":"交通银行",
		"CIB-NET-B2C":"兴业银行","NJCB-NET-B2C":"南京银行",
		"CMBC-NET-B2C":"中国民生银行","CEB-NET-B2C":"光大银行",
		"BOC-NET-B2C":"中国银行","PINGANBANK-NET":"平安银行",
		"CBHB-NET-B2C":"渤海银行","HKBEA-NET-B2C":"东亚银行",
		"NBCB-NET-B2C":"宁波银行","SDB-NET-B2C":"深圳发展银行",
		"GDB-NET-B2C":"广发银行","SHB-NET-B2C":"上海银行",
		"SPDB-NET-B2C":"上海浦东发展银行","POST-NET-B2C":"中国邮政",
		"BJRCB-NET-B2C":"北京农村商业银行","CZ-NET-B2C":"浙商银行",
		"HZBANK-NET-B2C":"杭州银行"};

// 省份地区
var PROVINCE =  {
		11:"北京市",43:"湖南省",
		12:"天津市",44:"广东省",
		13:"河北省",45:"广西壮族自治区",
		14:"山西省",46:"海南省",
		15:"内蒙古自治区",50:"重庆市",
		21:"辽宁省",51:"四川省",
		22:"吉林省",52:"贵州省",
		23:"黑龙江省",53:"云南省",
		31:"上海市",54:"西藏自治区",
		32:"江苏省",61:"陕西省",
		33:"浙江省",62:"甘肃省",
		34:"安徽省",63:"青海省",
		35:"福建省",64:"宁夏回族自治区",
		36:"江西省",65:"新疆维吾尔自治区",
		37:"山东省",71:"台湾省",
		41:"河南省",81:"香港特别行政区",
		42:"湖北省",82:"澳门特别行政区"
};

/*错误消息提示*/
var ERROR = {invalidorder:"无效订单号",invalidordersta:"无效订单状态",
		notallow:"非法操作",unknowerr:"系统内部错误，请联系客服",
		forbidden:"无访问权限"};


/*创建用户信息错误*/
var CREATE_USERINFO_ERROR={"UserBasicInfoExistedException":"用户基本信息已创建!","NullUserNameException ":"用户昵称不能为空!",
							"InvalidUserNameFormatException":"用户昵称输入不合法!","NullPhoneNumberException":"用户手机号码不能为空!",
							"InvalidPhoneNumberFormatException":"手机号码输入不规范!","PhoneNumberExistedException":"此手机号码已经被注册!","InvalidPasswordFormatException":"支付密码输入格式有误!",
							"UserBasicInfoNotExistedException":"用户基本资料不存在!","NickNameNoChangeException":"昵称输入无变化!",
							"PhoneNumberValidCodeNullException":"手机验证码为空或已失效,请重新获取!",
							"PayPasswordSameLoginPasswordException":"尊敬的会员为了您的账户安全,支付密码不能与登录密码一致!",
							"EmailFormatException":"Email输入不规范","NullEmailException":"Email输入不能为空",
							"NullQQNumberException":"QQ号码输入不规范","QQNumberFormatException":"QQ号码输入不规范","PasswordNotRightException":"当前登录密码输入不正确!","NullPasswordException":"密码输入为空!"};

/*创建和修改银行信息错误*/
var CREATE_BANK_ERROR={"UserNullOrNotLoginException":"用户不存在或没登录!","UserBankInfoNotExistedException":"银行信息不存在!",
		"NullBankNameException":"银行名输入为空!","InvalidBankNameFormatException":"银行输入不符合规范!",
		"NullBankProvinceException":"开户省份不能为空!","InvalidBankProvinceException":"开户省份不符合规范!",
		"NullBankCityException":"开户城市不能为空","NullBankAreaException":"开户区县不能为为空",
		"NullBankAddressException:":"开户地址不能为空!","InvalidBankAddressFormatException":"开户地址输入不符合规范!",
		"NullTrueNameException":"开户名不能为空!","InvalidTrueNameFormatException":"开户名输入不规范!",
		"NullBankCardNumberException":"银行卡号为空!","InvalidBankCardNumberFormatException":"银行卡号输入不规范!",
		"BankCardNumberExistedException":"银行卡号已存在!","PhoneNumberValidCodeNullException":"手机验证码为空或已失效,请重新获取!",
        "SubCompanyNotExistedException":"创建银行信息异常,请联系客服","Forbidden":"无访问权限!","NullCheckCodeException":"验证码不正确或已过期!","PasswordNotRightException":"当前登录密码输入不正确!"};

/*修改pwd 和 payPwd*/
var UPD_PWD_ERROR={"NullPasswordException":"密码输入为空!","InvalidPasswordFormatException":"密码输入格式不正确!",
				 "PasswordNotRightException":"当前登录密码输入不正确!","SameOldPasswordNewPasswordException":"新密码不能和旧密码相同!",
				 "NullPaymentPasswordException":"支付密码输入不能为空!","UserNullOrNotLoginException":"用户为空或者没登录!",
				 "PhoneNumberValidCodeNullException":"手机验证码为空,请重新获取!","ParamNullException":"输入参数为空!",
				 "NullCheckCodeException":"验证码不正确或已过期!","CheckCodeTypeErrorException":"请输入正确的验证码!",
				 "CheckCodeTimeOutException":"验证码已失效,请重新获取!","UserBasicInfoNotExistedException":"请先完善会员资料!"};

/*Draw */
var DRAWAL = {"UserNullOrNotLoginException":"用户为空或者没登录,请先登录!","UserBankInfoNotExistedException":"请先完善您的银行信息!",
			  "NullBankCityException":"完善您的提款银行开户省市信息!",
			  "DrawalParamNullException":"提款金额,或者支付密码为空!","InvalidPayPasswordException":"支付密码错误,请重新输入!",
			  "BalanceNotEnoughException":"您的账户余额不足!","LessThanMinDepositAmountException":"最低提款余额为100.00",
			  "InvalidAmountException":"提款金额小数点后最多只能两位!","DrawalOrderHavePendingException":"您有待审核提现申请,请先等待申请通过!",
			  "NullPasswordException":"请输入支付密码!","NullPaymentPasswordException":"您的支付密码为空,请先完善!",
			  "PaymentPasswordNotSetException":"您的支付密码为空,请先完善!",
			  "FirstDepositPromotionLimitException":"未达到首存优惠提款限额!",
			  "UserBankInfoNotEnoughException":"银行资料信息不全,请完善银行资料",
			 
			  "yxxze":"您的有效下注额为：<strong>",
			  "WashChipConstraintException":",</strong>未达到洗码条件,有效下注金额需大于等于：<strong>",
			  "kcsxf":"</strong>,目前提款需扣除手续费：<strong>",
			  "qdtk":",</strong>确定要提款么?",
			  "TryPlayerDrawlDepositErrorException":"您是试用会员无法提款/存款操作,请注册为真实会员!",
			  
			  "kcsclj":"提款失败,原因是余额不足.<br>1、你当前余额是：<strong>",
			  "dqye":"</strong>, <br>2、提款申请金额：<strong>",
			  "kouchu":"</strong>需扣除首存赠送金：<strong>",
			  
			  "wddyxxz":"未达到首存优惠提款限额,有效下注额：<strong>",
			  "scyhxm":"</strong>,需达到首存优惠洗码限制额：<strong>",
			  "qdtk2":"</strong>,否则会扣除手续费, 确认要提款么?"};
			  /*1 洗码提示  2首存不够扣提示  3首存扣款提示*/

/* 财务管理  */
var ACCOUNT = {username:"用户名",name:"姓名",
		bankinfo:"用户银行卡信息",depositacct:"存入公司银行账户",
		transfertime:"转账时间",contact:"联系方式",mobile:"手机",
		email:"邮箱",deposittime:"申请存入时间",
		currtype:"货币类型",depositamount:"存款金额",
		fee:"产生手续费",drawamount:"申请提款金额",
		confirmdeposit:"您确认要审核通过该存款？",noconfirmdeposit:"您确认要审核不通过该存款？",
		confirmdraw:"您确认要审核通过该提款？",noconfirmdraw:"您确认要审核不通过该提款？",
		passcheck:"通过审核",nopasscheck:"审核不通过",
		pay:"通过打款",pending:"待处理",noconfirmpending:"确认要待处理该提款？",
		amount:"实际存入用户账户",explain:"说明"};

/* 游戏  */
var GAMGES = {opening:"即将开放，敬请期待...",mustlogin:"请先登录，再开始游戏",trymsg:"提示：&元试玩体验金已经到帐"};

/* 账户余额  */
var BLANCE = {showblance:"显示余额",querying:"正在查询",queryerr:"查询失败",gameblan:"游戏余额",gameErr:"维护"};

/*转账TYPE*/
var TRANSFER_TYPE = {1:"转入",2:"转出"};


/*弹窗消息提示*/
var ZXXBOX = {freeplay:"试玩账号无法充值，请注册真钱账户进行充值!",letsgo:"立即去",
		reg:"申请真钱账户",aomenmsg:"澳门娱乐场提示您",appblan:"应用划账",
		playd:"玩法说明",close:"关闭"};

/*银行提示信息*/
var BANK_MSG = {notzero:"请输入不为零的数字",larger100:"最低存款额度为100",larger20:"最低存款额度为20",larger3000:"最高存款额度为3000",DrawLarger100:"最低提现额度为100",
		usebank:"在线存款高于限额10000,请使用银行转账",selebank:"请选择银行",selepaybank:"请选择支付银行",
		seleaccount:"请选择您的开户行",seledacct:"请选择存入银行",seleprovince:"请选择开户省份",seleCity:"请选择开户城市",seleArea:"请选择开户区县",deposittype:"请选择您的存款方式",
		eadress:"请输入开户地址!",ename:"请输入持卡人姓名!",firstdeposit:"首存达1000元,才可申请赠金",
		twod:"提款金额小数点后最多只能两位!",drawpwd:"请输入您的提款密码!",applysuccess:"申请成功!",amountmustnum2:"金额必须为数字",
		notsame:"出入两边不能一致",amountnotnull:"金额不能为空",inputamount:"请输入汇款金额",amountmustnum:"金额必须为正整数",
		larger1:"单次金额最低限额为 1",less5000000:"单次金额最高限额为 5000000",paypwdnotnull:"支付密码不能为空",
		ecard:"请输入银行卡号!",cardmustbenum:"银行卡号只能输入10到20位的数字!",savesuccess:"数据保存成功!",
		transforType:"请选择转账操作类型!",addbank:"请先完善您的银行信息才能提款!",remard:"汇款留言不能为空",
		seleprov:"请选择省份",selecity:"选择城市",selearea:"请选择地区",seleZfbAcct:"请选择支付宝账号",seleWxAcct:"请选择微信账号",
		scanCodeChecking:"正在检测二维码图片，请稍候再提交.....",inputZfbAcct:"请输入支付宝账号",inputWxAcct:"请输入微信账号",
		cannotPay:"暂不支持该金额充值",zfbnotConfig:"支付宝账号尚未配置",wxnotConfig:"微信账号尚未配置",
		tryplay:"您是试用会员无法提款/存款操作,请注册为真实会员!",evcode:"请填写短信验证码"};

/*重置消息提示*/
var RESET = {mobilecode:"请输入手机验证码!",seleone:"至少选择一项需要重置的内容!",
	ycode:"你的验证码是",codenotfound:"暂时无法获取验证码!",addyouInfo:"请先完善您的基本资料和银行资料 !"};

/*修改密码提示信息*/
var EDIT_PWD = {oldLoginPwd:"请输入当前登录密码!",newpwd:"新密码不能为空!",pwdlen:"密码输入有误,只能包含字母数字长度6-12位!",
		pwdnotsame:"两次新密码输入不一致!",loginpwd:"请输入当前登录密码!",
		newoldissame:"新密码和旧密码不能相同!",editsuccess:"密码修改成功!",
		payPassLen:"支付密码输入有误,只能包含字母数字长度4-6位"};

/*会员提示信息*/
var USER_MSG = {
		mobileerr:"手机号输入不合法!",savesuccess:"数据保存成功!",
		nicknotnull:"真实姓名不能为空!",nicklen:"真实姓名长度不符合!",
		drawpwdlen4_6:"支付密码输入有误,只能包含字母数字长度4-6位",
		drawpwdlen:"支付密码输入有误,只能包含字母数字长度4位",pwdnotsame:"两次密码输入不一致!",
		editnicksuccess:"修改成功!",mustbereset:"只能使用信息重置修改关键信息!",
		mobileNull:"手机号码输入不能为空!",emailError:"Email不符合规则!",qqError:"QQ不符合规则!",
		inputOldPwd:"请输入原密码!",newPwd:"新密码长度 6-12位英文+数字!",yuanPassErr:"原密码输入不正确!",
		loginPassErr:"登录密码输入不正确",
		pleaseAddMeninfo:"请先完善会员资料!",
		receiveNameNotnull:"收货人姓名不能为空!",receiveLen:"收货人姓名长度不符合!",
		receiveNameformaterr:"收货人姓名只能包含汉字字母",webChatNull:"微信账号不能为空!",
		contactMobileNull:"收货手机号码不能为空",contactMobileerr:"手机号输入不合法!",firstNameNull:"绑定真实姓名,姓不能为空!",lastNameNull:"绑定真实姓名,名字不能为空!",
		addressNull:"街道地址输入不能为空!",receiveSaveSuccess:"收货地址保存成功,请您放心,我们绝不会把您的信息透露给第三方!",
		firstNameNull:"绑定真实姓名,姓不能为空!",lastNameNull:"绑定真实姓名,名字不能为空!",
		trueNameFormaterr:"真实姓名格式错误,不能包含特殊字符!",promptLoginPwd:"请输入登入密码",payPwdErr:"基础记录修改成功，设置支付密码失败，请联系管理员处理"};

/*分页*/
var PAGE = {
		first:"首页",last:"尾页",
		prev:"上一页",next:"下一页",
		total:"共",page:"页",
		ok:"确定",num:"第",
		data:"条数据",gotop:"转到"};

var HALL_MSG={selectHall:"请选择游戏厅!",mustSeleOne:"转入转出，至少选择一种方式",seleHallIn:"请选择转入游戏厅",seleHallOut:"请选择转出游戏厅"};

var TRANSFOR_ALERT={1:"从中心钱包转入金额到游戏厅!",2:"从游戏厅转出金额到中心钱包!",sucess:"成功",tans1:"转入",tans2:"转出","balance":"当前钱包余额:","transferSuccess":"转账成功"};
/*转账返回信息*/
var TRANSFOR_ERROR={"NullPaymentPasswordException":"请输入支付密码!","InvalidPayPasswordException":"支付密码输入错误!",
		"NullPasswordException":"请输入支付密码!","PaymentPasswordNotSetException":"请先设置您的支付密码!",
		"TransferBeanParamException":"请输入转账金额和支付密码!","UserNullOrNotLoginException":"您未登录,请先登录!",
		"BalanceNotEnoughException":"您的余额不够,请重新输入!","GameIntegrationApiInvokeFailedException":"转账失败,请联系客服!",
		"UserNotExistedInGameException":"用户不存在,请联系客服!","InvalidAmountException":"请输入最多两位小数的金额!",
		TrailUserTransferMoneyException:"试玩用户不能转账",GameIsClosingException:"&即将关闭，请将余额转到中心钱包",
		"GameConfigNotExitedException":"转出金额失败,请联系客服!",GameIsMaintainingException:"游戏厅维护中 ",GameConfigNotExitedException:"游戏厅配置不存在"};


/*游戏类型*/
var GAMES = {"1":"FF现场厅","2":"AG旗舰厅","3":"BBIN厅","4":"HK厅","5":"CT厅",6:"GD厅",7:"012香港彩",8:"012快乐彩",9:"012时时彩B区",10:"PT电子游戏",11:"DS太阳城",12:"012香港彩",13:"012时时彩A区",14:"BBIN旗舰厅",15:"欧博娱乐城",16:"188体育",17:"IM体育",18:"MG电子游戏",19:"现场高频彩",20:"中国股票",21:"AG棋牌",22:"HB电子游戏",23:"PNG电子游戏",24:"OG视讯厅",25:"AV电子游戏",26:"皇冠体育",27:"VF厅",28:"LMG厅",30:"FF时时彩",31:"申博游戏厅",waiting:"您正在进入&，请稍候...",seleg:"请选择游戏厅"};


/*划账*/
var REMIT_ACCOUT = {"1":"划入","2":"划出"};

/*消息*/
var MGS_STATU = {10:"未读",20:"已读",selIds:"请选择操作记录!",msgRead:"阅读",delMsg:"删除",querying:"正在查询......"};


/*应用划账消息*/
var APP_BLANCE = {BalanceNotEnoughException:"余额不足",GameIntegrationApiInvokeFailedException:"游戏厅维护中",
		UserNotExistedInGameException:"用户在游戏厅不存在",InvalidAmountException:"金额格式不对，最多两位小数，大于0",GameIsClosingException:"&即将关闭，请将余额转到中心钱包",
		TrailUserTransferMoneyException:"试玩用户不能转账",GameNotFoundException:"无此游戏厅","PaymentPasswordNotSetException":"请先设置您的支付密码!",
		appsucc:"划账成功",op:"正在处理",GameIsMaintainingException:"游戏厅维护中 ",GameConfigNotExitedException:"游戏厅配置不存在",
		UnknowException:"系统内部错误，请联系客服",NullPasswordException:"必须输入支付密码",InvalidPayPasswordException:"支付密码错误"};

/*赠金类型*/
var PROMOTION_TYPE={10:"首存优惠"};

/*试玩错误消息*/
var TRY={tryacct:"试玩帐号",trypwd:"密码",rem:"请把账号密码记下来",agree:"你必须同意开户协议，才能继续操作"};

/*试玩错误消息*/
var TRY_ERR={NoTrialUserException:"无可用的试玩帐号",NullCheckCodeException:"验证码为空 ",InvalidCheckCodeException:"验证码错误 "};

/*资金明细流向类型*/
var CAPITAL_FLOW_TYPE = {1010:"易宝存款",1020:"网银转账",1021:"柜台转帐",1022:"ATM转帐",1023:"电话转账",
		 1024:"手机银行转账",1030:"管理员存款",1031:"银行卡转账",1040:"代理转账平台",2000:"取款",2001:"取款失败,退款",2002:"管理员扣款",
		 2003:"推广代充扣款",3000:"转入游戏厅",3001:"转出游戏厅",
		 4000:"首存优惠",4001:"扣除首存优惠礼金",4002:"取款失败,退还扣除的首存优惠礼金",5000:"管理员添加",
		 6000:"洗码赠金",7000:"平台赠金",8040:"代理转账平台",150:"转入游戏厅取消，退款",7001:"平台赠金:全勤奖励",
		 7002:"平台赠金:假日赠送", 7003:"平台赠金:激励奖金",7004:"平台赠金:QQ签名奖励",7005:"平台赠金:百家乐顺子奖",
		 7006:"平台赠金:体育串关奖",7007:"平台赠金:AG过关奖励",7008:"平台赠金:百家乐连赢",7009:"平台赠金:生日送礼",
		 7010:"平台赠金:免费赠送活动",7011:"平台赠金:推荐好友",7012:"平台增金:积分兑换",
		 8052:"推广代充",9001:"下注",9002:"取消下注",9003:"游戏厅结算"};

var CAPITAL_FLOW = {chenggong:"成功"};


/*游戏厅配置*/
var GAMES_HALL={AsianHall:"FF现场厅",Ag:"AG旗舰厅","Bbin":"BBIN厅",Hk:"Hk厅",Ct:"CT厅",Gd:"GD厅",Lottery:"012香港彩",Keno:"012快乐彩",Amp:"012时时彩B区",Pt:"PT电子游戏",IGCasino:"DS太阳城",IGLottery:"012香港彩",IGSsc:"012时时彩A区","Bbin2":"BBIN旗舰厅","Xj":"188体育",Im:"IM体育",AllBet:"欧博娱乐城",AgEgame:"MG电子游戏",DgLottery:"现场高频彩",DgStock:"中国股票",AgTex:"AG棋牌",AgEgameHB:"HB电子游戏",AgEgamePNG:"PNG电子游戏",Og:"OG视讯厅",Hg:"皇冠体育",Vf:"VF厅",IGLive:"LMG厅",FfSsc:"FF时时彩",Tgp:"申博游戏厅"};

/*游戏ID*/
var GAMES_ID = {1:"FF现场厅",2:"AG旗舰厅",3:"BBIN厅",4:"HK厅",5:"CT厅",6:"GD厅",7:"012香港彩",8:"012快乐彩",9:"012时时彩B区",10:"PT电子游戏",11:"DS太阳城",12:"012香港彩",13:"012时时彩A区",14:"BBIN旗舰厅",15:"欧博娱乐城",16:"188体育",17:"IM体育",18:"MG电子游戏",19:"现场高频彩",20:"中国股票",21:"AG棋牌",22:"HB电子游戏",23:"PNG电子游戏",24:"OG视讯厅",25:"AV电子游戏",26:"皇冠体育",27:"VF厅",28:"LMG厅",30:"FF时时彩",31:"申博游戏厅"};


/*游戏类型*/
var GAME_TYPE = {"BACCARAT":"百家乐","DRAGON_TIGER":"龙虎","ROULETTE":"轮盘","BACCARAT_INSURANCE":"保险百家乐",
			"SICBO":"骰宝","XOC_DIA":"色碟","CBAC":"包桌百家乐","LINK":"连环百家乐","FT":"番摊","PKBJ":"视讯扑克",
			"FRU":"水果拉霸","DZPK":"德州扑克","GDMJ":"广东麻将"};

/*1 首存活动与洗码限制 2首存活动  3 洗码限制*/ 
var DRAWAL_EXCEPTION = {1:"由于你参与以下活动限制(点确定强制提款)：<br> 1、不满足存款1倍洗码,需要扣除30%手续费;<br>2、不满足活动赠送提款,扣除首存赠送金.",
		2:"由于你参与以下活动限制(点确定强制提款)：<br> 不满足活动赠送提款,扣除首存赠送金.",
		3:"由于你参与以下活动限制(点确定强制提款)：<br> 不满足存款1倍洗码,需要扣除30%手续费."};


/*洗码管理*/
var WASHCODE = {
		type:{10:"存取款洗码限制"},status:{1:"开启",0:"关闭"},edit:"修改",
		emsg:"您确定激活该配置吗?",dmsg:"您确定关闭该配置吗?",s:"成功",f:"失败",
		action:{
			add:{url:"/{serviceName}/rest/washChipConfigService/washChipConstraintConfig/{company}",type:"POST",errmsg:"添加失败",msg:"添加成功"},
			edit:{url:"/{serviceName}/rest/washChipConfigService/washChipConstraintConfig/{company}",type:"PUT",errmsg:"更新失败",msg:"更新成功"}
		   }
	};

/*返佣管理*/
var COMMISSION = {
		gameId:{1:"FF现场厅",2:"AG游戏厅"},commiLevel:"返佣级别",lowestAmount:"最低下注额",commiRate:"返佣比率",
		casino:"真人游戏",dz:"电子游戏",lotto:"彩票",sport:"体育",pk:"扑克",
		gameType:{2:"真人游戏",3:"电子游戏",4:"彩票",5:"棋牌",6:"体育"},mustnum:"值必须为数字",
		autoSettlement:{"true":"是","false":"否"},edit:"修改",
		emsg:"您确定激活该配置吗?",dmsg:"您确定关闭该配置吗?",s:"成功",f:"失败",notnull:"值不能为空",
		commissionDatenotnull:"返佣时间不能为空",os:"操作成功",of:"操作失败",
		exception:{CommissionDateException:"返佣日期不能大于昨天的日期",
			CommissionConfigNotExistedException:"游戏配置不存在",
			CommissionConfigExistedException:"配置已经存在",
			CommissionHadDoneException:"已经反佣",
			NamingException:"内部异常",InterruptedException:"内部异常",
			InterruptedException:"内部异常"},
		action:{
			add:{url:"/{serviceName}/rest/commissionConfigService/commissionConfig",type:"POST",errmsg:"添加失败",msg:"添加成功"},
			edit:{url:"/{serviceName}/rest/commissionConfigService/commissionConfig",type:"PUT",errmsg:"更新失败",msg:"更新成功"}
		   }
	};


/*订单状态错误信息*/
var ORDER_STATUS_ERR = {DepositOrderStatusErrorException:"订单状态不正确",OperationNotAllowedException:"非法操作"};

var EXTRA_CHARGE_TYPE = {10:"洗码手续费：",20:"首存手续费：",30:"银行手续费："};


//BBIN游戏种类+结果组合 防止多个游戏重复, 视频游戏的结果为开牌结果  //彩票X未结算
var BBIN_RESULT = {"1N":"无结果","1C":"注销","1W":"赢","1L":"输","1LW":"赢半","1LL":"输半","10":"平手","1D":"未接受",
				   "1F":"非法注销","1X":"未结算","1S":"等待中",
				   "12W":"赢","12L":"输","12N":"平手","120":"无结果","12N2":"注销","12X":"未结算",
				   "5-1":"注销","51":"赢","5200":"输","5X":"未结算",
				   "15-1":"注销","151":"赢","15200":"输","15X":"未结算"};

var BBIN_GAME_KIND = {1:"体育",3:"真人视频",5:"电子游戏",12:"彩票",15:"3D厅"};


var BBIN_GAME_TYPE = {"BK":"篮球","BS":"棒球","F1":"其他","FB":"美足","FT":"足球",
		"FU":"指数","IH":"冰球","SP":"冠军","TN":"网球",
		
		"LT":"六合彩","D3":"3D彩","P3":"排列三","BT":"3D时时彩","T3":"上海时时彩",
		"CQ":"重庆时时彩","JX":"江西时时彩","TJ":"天津时时彩","GXSF":"广西十分彩",
		"GDSF":"广东十分彩","TJSF":"天津十分彩","BJKN":"北京快乐8","CAKN":"加拿大卑斯",
		"AUKN":"澳洲首都商业区","BBKN":"BB快乐彩","BJPK":"北京PK拾","GDE5":"广东11选5",
		"CQE5":"重庆11选5","JXE5":"江西11选5","SDE5":"山东十一运夺金","BBRB":"BB滚球王",
		"JSQ3":"江苏快3","AHQ3":"安徽快3","BBBO":"BB宾果",
		
		"15006":"3D玉蒲团","15016":"厨王争霸","15017":"连环夺宝","15018":"激情243",
		"15019":"倩女幽魂","15021":"全民狗仔","15023":"连连看",
		"15024":"2014世足赛",
		
		"3001":"百家乐","3002":"二八杠","3003":"龙虎门","3005":"三公","3006":"温州牌九",
		"3007":"轮盘","3008":"骰宝","3010":"德州扑克","3011":"色碟","3012":"牛牛",
		"3013":"赛本引","3014":"无限21点",
		
		"5001":"水果拉霸","5002":"扑克拉霸","5003":"筒子拉霸","5004":"足球拉霸","5011":"西游记",
		"5012":"外星争霸","5013":"传统","5014":"叢林","5015":"FIFA2010","5016":"史前叢林冒险",
		"5017":"星球大战","5018":"齐天大圣","5019":"水果乐园","5020":"热带风情","5021":"7PK",
		"5023":"七靶射击","5025":"法海斗白蛇","5026":"2012伦敦奥运","5027":"功夫龙","5028":"中秋月光派对",
		"5029":"圣诞派对","5030":"幸运财神","5034":"王牌5PK","5035":"加勒比扑克","5039":"鱼虾蟹",
		"5047":"尸乐园","5048":"特务危机","5049":"玉蒲团","5050":"战火佳人","5057":"明星97","5058":"疯狂水果盘",
		"5059":"马戏团","5060":"动物奇观五","5061":"超级7","5062":"龙在囧途","5070":"黄金大转轮",
		"5074":"钻石列车","5075":"圣兽传说","5076":"数字大转轮","5077":"水果大转轮","5078":"象棋大转轮",
		"5079":"3D数字大转轮","5080":"乐透转轮","5088":"斗大","5089":"红狗","5091":"三国拉霸",
		"5092":"封神榜","5093":"金瓶梅","5094":"金瓶梅2","5101":"欧式轮盘","5102":"美式轮盘","5103":"彩金轮盘",
		"5104":"法式轮盘","5115":"经典21点","5116":"西班牙21点","5117":"维加斯21点","5118":"奖金21点",
		"5131":"皇家德州扑克","5201":"火焰山","5202":"月光宝盒","5203":"爱你一万年","5204":"2014 FIFA",
		"5401":"天山侠侣传","5402":"夜市人生","5403":"七剑传说","5801":"海豚世界","5802":"阿基里斯",
		"5803":"阿兹特克宝藏","5804":"大明星","5805":"凯萨帝国","5806":"奇幻花园","5807":"东方魅力",
		"5808":"浪人武士","5809":"空战英豪","5810":"航海时代","5811":"狂欢夜","5821":"国际足球",
		"5822":"兔女郎","5823":"发大财","5824":"恶龙传说","5825":"金莲","5826":"金矿工","5827":"老船长",
		"5828":"霸王龙","5831":"高球之旅","5832":"高速卡车","5833":"沉默武士","5834":"异国之夜",
		"5835":"喜福牛年","5836":"龙卷风","5888":"JackPot"};

var DG_LOTTERY_KIND = {1:"真人视频",2:"彩票游戏",3:"体育游戏",4:"电子游戏"};

var DG_LOTTERY_STATUS = {0:"未开奖",1:"已开奖",2:"重对奖",3:"取消单"};


/*绑定手机 邮箱  和创建验证码 错误信息*/
var SEND_MSG_INFO = {"Success":"提示您：稍后您会收到一条验证短信，请在30分钟内完成验证,过期请重新发送!",
					"codeInputErr":"请输入6位验证码!",
					"ParamNullException":"参数不能为空!",
					"CreateCodeNoTimeException":"操作太频繁,请1分钟后再试!","PromotionIsClosedException":"您已经参与了该活动，无法重复参与!",
					"NullCheckCodeException":"验证码不正确或已过期!","CheckCodeTypeErrorException":"请输入正确的验证码!",
					"EmailBindSuccess":"邮箱绑定成功","NullActiveSmsConfigException":"发送失败,找不到激活的手机！",
					"CheckCodeTimeOutException":"验证码已失效,请重新获取!",
					"PromotionConfigNotExistedException":"该活动不存在或已关闭,请联系客服!",
					"EmailHaveUsedException":"该邮箱地址已被注册,不可重复使用!",
					"PhoneNumberExistedException":"该手机号码已被注册,不可重复使用!",
					"GameIntegrationApiInvokeFailedException":"手机短信发送失败请联系客服!",
					"UserBasicInfoNotExistedException":"邮箱或手机号码不存在,请先完善!",
					"NullEmailException":"请先完善您的邮箱信息!",
					"NullPhoneNumberException":"请先完善您的手机信息!",
					"UserTrueNameHaveBindException":"用户名字已绑定,无法重复绑定!",
					"UserEmailHaveBindException":"用户邮箱已绑定,无法重复绑定!",
					"UserMobilePhoneHaveBindException":"用户手机已绑定,无法重复绑定!",
					"checkValidMethod":"请选择接收验证码方式!",
					"uContactMethod1":"验证码已发至您邮箱中，若找不到请在垃圾邮箱翻翻哦~",
					"uContactMethod2":"验证码已发至您手机中，请查收~",
					"YPSmsReturnCode29Exception":"操作太频繁,同一个手机号同一个验证码模板,每30秒只能获取1条！",
					"YPSmsReturnCode22Exception":"操作太频繁,同一个手机号验证码类内容,每小时最多能获取3条 ！",
					"YPSmsReturnCode17Exception":"操作太频繁,同一个手机号验证码类内容,每天最多能获取到10条！"	,
					"DrawalOrderIsNullException":"要使用该彩金，您需要有一次成功提款！",
					"UserNotExistedException":"用户不存在!",
					"NullActiveEmailConfigException":"发送邮箱未激活,请联系客服！",
					"FAIL":"未成功接收验证码,请联系客服处理!"
					};

/*
 * 电话回拨错误信息
 */
var SEND_BACK_CALL_CODE ={
		"InvalidPhoneNumberFormatException":"非法手机号码","phoneNumberIsNot11":"请填写11为手机号码",
		"NullActiveSmsConfigException":"发送失败，找不到激活的手机","GameIntegrationApiInvokeFailedException":"超过每日发送限额",
		"YPSmsReturnCode17Exception":"同个手机号每天最多获得10条消息","YPSmsReturnCode22Exception":"同个手机号每小时最多能获取3条消息",
		"YPSmsReturnCode29Exception":"同个手机号每30秒只能获取1条","InvalidPhoneNumberFormatException":"手机号码格式不正确",
		"InvalidSubcompanyException":"分公司商户号不存在","UnknowException":"出错了,请联系客服",
	    "NullCheckCodeException":"验证码输入不正确，请重新输入","ParamNullException":"分公司或者手机号码为空","CheckCodeTimeOutException":"验证码已失效,请重新获取",
	     "InvalidSubcompanyException":"分公司商户号不存在","checkCodeIsNull":"请输入验证码","BackCallSubmitedException":"您有待处理的回拨申请，请等待客服联系"
}

//热门游戏 20
var PT_PC_HOT = ["gtsflzt","gtsfj","art","bob","dv2","dnr","fth","paw","gtslgms","gtsdrdv","fnfrj","ashfmf","drd","dt2","fxf","glr","gtscirsj","gtsdgk","gtsgoc","gtspor"];

//最新游戏 29
var PT_PC_NEW =["ashsbd","gtsir","gtsflzt","gtsfj","arc","thtk","paw","gtsdrdv","fcgz","nian_k","thtk","fnfrj","bib","bt","c7","cm","dlm","dt","eas","er","fff","fm","gos","gts50","gtshwkp","hlf","sib","ssp","zcjb"];

//特色游戏 40
var PT_PC_SPCAIL = ["ashfmf","gtscirsj","ashamw","fsc","gtswg","hsd","pbro","qbd","photk","lvb","dt","sib","ct","dlm","fbr","fow","hk","ts","gtsbayw","gtssprs","gtsjhw","car","gtsmrln","drd","irm3sc","irmn3sc","rom","pnp","rky","gtssmdm","kkgsc","mmy","glr","gtscbl","fdt","tps","al","gts5","ttwfs","ttc"];

//彩金池游戏 21
var PT_PC_WINNINGS = ["fcgz","nian_k","thtk","jb10p","bl","cifr","fnfrj","glr","grel","ms","bls","mj","pbj","qop","sc","wc","car","ghlj","str","wsffr","wv"];

//街机游戏 21
var PT_PC_ARCADE =   ["atw","bls","bowl","dctw","fsc","ghl","ghlj","head","hr","hsd","kgdb","kn","lwh","mro","pbro","pop","pso","qbd","rcd","rps","tps"];

//刮刮乐 14
var PT_PC_GGL = ["bbn","essc","fbm","irm3sc","irmn3sc","kkgsc","lom","pks","sbj","scs","sro","ssa","tclsc","wc"]; 

//老虎机 117 
var PT_PC_LHJ =["gts52","savcas","frtln","hotktv","cashfi","furf","zeus","hrcls","athn","ftsis","aogs","ashsbd","gtsir","gtsfj","arc","art","bob","dv2","dnr","fth","paw","gtslgms","gtsdrdv","fcgz","nian_k","thtk","ah2","bl","photk","cifr","cnpr","ct","drd","evj","fbr","fmn","fnfrj","fow","grel","gs","gtscirsj","hb","hk","lvb","ms","pnp","pyrr","qop","sc","sib","wsffr","8bs","al","ashamw","ashfmf","bib","bt","c7","cm","dlm","dt","dt2","eas","er","fdt","fff","fm","foy","ttwfs","fxf","gc","glg","glr","gos","gts50","gts51","gtsaod","gtsatq","gtsbayw","gtscbl","gtsdgk","gtsgoc","gtshwkp","gtsjhw","gtsjzc","gtsmrln","gtspor","gtssmbr","gtssmdm","gtssprs","gtsstg","gtswg","gtswng","hh","hlf","jb","kkg","mcb","mmy","nk","op","pl","rky","sf","ssl","ssp","ta","tp","tr","ts","tst","ttc","ub","whk","wis","wlg","zcjb"];

//桌面&卡牌 35
var PT_PC_ZMKP = ["ash3brg","aogro","str","car","pbj","rom","wv","ba","bj21d_mh","bja","bjs","mpbjsd","bjuk_mh5","cheaa","cr","frr","frr_g","gts5","gtsro3d","romw","pfbj_mh5","pg","pon_mh5","rd","ro","ro_g","ro3d","rodz","rodz_g","rop","rop_g","rouk","s21","sb","tqp"];

//视频&扑克 13 
var PT_PC_SPPK =["jb10p","mj","af","af25","af4","dw","dw4","hljb","jb4","jb50","jp","po","tob"];

//赔付线 
var LINE_BET = {"gts52":3,"savcas":3,"frtln":3,"hotktv":3,"cashfi":"","furf":3,"zeus":4,"hrcls":4,"athn":"","ftsis":"","aogs":"","ashsbd":"","gtsir":"","gtsfj":3,"arc":3,"art":3,"bob":3,"dv2":3,"dnr":3,"fth":3,"paw":3,"gtslgms":3,"gtsdrdv":3,"fcgz":3,"nian_k":1,"thtk":2,"ah2":234,"bl":234,"photk":1,"cifr":234,"cnpr":234,"ct":1,"drd":"","evj":1,"fbr":234,"fmn":1,"fnfrj":234,"fow":234,"grel":1,"gs":1,"gtscirsj":234,"hb":234,"hk":1,"lvb":234,"ms":1,"pnp":567,"pyrr":234,"qop":1,"sc":1,"sib":1,"wsffr":1,"8bs":1,"al":1,"ashamw":8,"ashfmf":234,"bib":234,"bt":1,"c7":1,"cm":1,"dlm":234,"dt":234,"dt2":234,"eas":234,"er":1,"fdt":234,"fff":234,"fm":1,"foy":1,"ttwfs":"","fxf":234,"gc":1,"glg":234,"glr":234,"gos":1,"gts50":234,"gts51":9,"gtsaod":234,"gtsatq":234,"gtsbayw":234,"gtscbl":1,"gtsdgk":234,"gtsgoc":234,"gtshwkp":1,"gtsjhw":234,"gtsjzc":234,"gtsmrln":234,"gtspor":234,"gtssmbr":234,"gtssmdm":567,"gtssprs":234,"gtsstg":234,"gtswg":234,"gtswng":234,"hh":1,"hlf":234,"jb":1,"kkg":234,"mcb":234,"mmy":234,"nk":1,"op":1,"pl":1,"rky":234,"sf":1,"ssl":1,"ssp":234,"ta":1,"tp":1,"tr":1,"ts":567,"tst":567,"ttc":234,"ub":1,"whk":567,"wis":234,"wlg":234,"zcjb":1};
//所有游戏Type
var PT_PC_ALL =["gtsflzt","gtsfj","art","bob","dv2","dnr","fth","paw","gtslgms","gtsdrdv","fnfrj","ashfmf","drd","dt2","fxf","glr","gtscirsj","gtsdgk","gtsgoc","gtspor","ashsbd","gtsir","arc","thtk","fcgz","nian_k","bib","bt","c7","cm","dlm","dt","eas","er","fff","fm","gos","gts50","gtshwkp","hlf","sib","ssp","zcjb","ashamw","fsc","gtswg","hsd","pbro","qbd","photk","lvb","ct","fbr","fow","hk","ts","gtsbayw","gtssprs","gtsjhw","car","gtsmrln","irm3sc","irmn3sc","rom","pnp","rky","gtssmdm","kkgsc","mmy","gtscbl","fdt","tps","al","gts5","ttwfs","ttc","jb10p","bl","cifr","grel","ms","bls","mj","pbj","qop","sc","wc","ghlj","str","wsffr","wv","atw","bowl","dctw","ghl","head","hr","kgdb","kn","lwh","mro","pop","pso","rcd","rps","bbn","essc","fbm","lom","pks","sbj","scs","sro","ssa","tclsc","gts52","savcas","frtln","hotktv","cashfi","furf","zeus","hrcls","athn","ftsis","aogs","ah2","cnpr","evj","fmn","gs","hb","pyrr","8bs","foy","gc","glg","gts51","gtsaod","gtsatq","gtsjzc","gtssmbr","gtsstg","gtswng","hh","jb","kkg","mcb","nk","op","pl","sf","ssl","ta","tp","tr","tst","ub","whk","wis","wlg","ash3brg","aogro","ba","bj21d_mh","bja","bjs","mpbjsd","bjuk_mh5","cheaa","cr","frr","frr_g","gtsro3d","romw","pfbj_mh5","pg","pon_mh5","rd","ro","ro_g","ro3d","rodz","rodz_g","rop","rop_g","rouk","s21","sb","tqp","af","af25","af4","dw","dw4","hljb","jb4","jb50","jp","po","tob"];
//所有游戏name
var PT_PC_ALL_NAME = ["飞龙在天","跃龙门","北极宝藏","奖金熊","钻石谷专业版","海豚礁堡","财富山  ","三只小猪与狼   ","野外游戏 ","勇敢的大卫和拉神之眼","酷炫水果","月满梦圆","夜魔侠","沙漠财宝2","狐狸的宝藏","罗马角斗士","坎农队长的马戏图","龙之王国","圣诞幽灵","财富蓝海","辛巴达的黄金之旅","极地冒险","弓箭手","泰国神庙","翡翠公主","年年有余","湛蓝深海","百慕大三角","疯狂七","中国厨房","恋爱专家","沙漠财宝","惊喜复活节","开心假期（假日车站）","酷炫水果农场","古怪猴子","金色之旅","热力宝石","漂移之王专业版","万圣节财富","银弹","圣诞惊喜","招财进宝","狂野亚马逊","最终比分","疯狂的赌徒","德州扑克摊牌","弹珠轮盘","飞镖","紫热","爱之船","船长的宝藏","终极足球","奇迹森林/惊异之林","高速公路之王","时空过客","海岸救生队","人在江湖","约翰·韦恩","加勒比扑克","玛丽莲·梦露","钢铁侠2刮刮乐","钢铁侠3刮刮乐","奇迹轮盘","粉红豹","洛基传奇","莫史狄","金刚刮刮乐","木乃伊","牛仔与外星人","疯狂底特律七","幸运7","炼金实验室　","视频轮盘赌","2014年顶级王牌世界足球明星","顶级王牌-明星","10线对J高手","海滨嘉年华 /沙滩假日","全景电影","金色召集","魔幻吃角子老虎","巨型超级球 - 累积彩池","超级杰克","累积二十一点","金字塔女王","保险箱探宝","获奖者俱乐部刮刮乐","精灵喜罗/猜扑克牌彩池游戏","Stravaganza"," 玩转华尔街","野蛮海盗 ","环游世界","保龄猜球游戏","转骰子游戏","精灵喜罗/猜扑克牌游戏","硬币投掷赌博游戏","德比赛马日","国王赛马","基诺","旋转双赢／轮盘旋转赌博游戏","迷你轮盘赌","宾果","罚点球游戏","掷骰子赌博游戏","石头、剪刀、纸 游戏","甲壳虫宾果刮刮乐","东方神奇刮刮乐","狂热足球","爱情配对","法老王国刮刮乐","21点刮刮乐","经典老虎机刮刮乐","轮盘赌刮刮乐","圣诞刮刮乐","三个小丑刮刮乐","狂躁的海盗","大草原现金","幸运狮子","火热KTV","深海大赢家","神灵时代：激情四","神灵时代：奥林匹斯之王","神灵时代：奥林匹斯王子","神灵时代：智慧女神","神灵时代：命运姐妹","神灵时代","异形猎手","甜蜜派对","人人中头奖","水果狂","钻石谷","满满长夜","拉美西斯金字塔","8球老虎机","青春之泉","地妖之穴","黄金游戏","幸运熊猫","天真与诱惑","亚特兰蒂斯女王","爵士俱乐部","巴西桑巴","苏丹国之黄金","黄金之翼","鬼屋","丛林摇摆","金刚","钱先生","海王星王国","海洋公主","舞线(派对风景线)","苏丹的财富","转轴经典3","三个朋友(义勇三奇侠)","三倍利润"," 热带滚筒","网球明星","丛林巫师","白王","我心狂野","舞龙","三张扑克牌","神灵时代：轮盘","百家乐","决斗21点","美式21点","换牌21点","投降21点","英式21点","赌场Hold Em游戏","双色骰子游戏","法式轮盘","法式奖金轮盘赌","3D轮盘高级版","多轮式轮盘","完美二十一点","牌九扑克","英式21点","红狗","欧洲轮盘","欧式奖金轮盘赌","3D轮盘","美式轮盘","美式奖金轮盘赌","超级轮盘","奖金轮盘赌专家","俱乐部轮盘赌","超级二十一点","骰宝","龙舌兰扑克","A牌花牌","A牌花牌25线","A牌花牌4航","2点　百搭牌","4线　百搭2","两种皇家同花顺","4线　对J高手","50线　对J高手","小丑扑克／王牌扑克","对J高手","对十高手"];

var XIN_PC_TYPE=['513', '544', '546', '537', '517', '504', '503', '548', '520', '512', '514', '521', '511', '550', '547', '510', '518', '522', '519', '526', '532', '531', '541', '529', '523', '530', '533', '540', '516', '524', '507', '534', '528', '525', '527', '536', '542', '505', '501', '509', '502', '508', '539', '515', '543', '545', '535', '549'];

var XIN_PC_IMG=['SB06_ZH', 'SB33_ZH', 'FRU2_ZH', 'AV01_ZH', 'SB10_ZH', 'SLM2_ZH', 'SLM1_ZH', 'SB36_ZH', 'SB13_ZH', 'SB05_ZH', 'SB07_ZH', 'SB14_ZH', 'SB04_ZH', 'SB38_ZH', 'SB35_ZH', 'SB03_ZH', 'SB11_ZH', 'SB15_ZH', 'SB12_ZH', 'SB19_ZH', 'SB25_ZH', 'SB24_ZH', 'PKBB_ZH', 'SB22_ZH', 'SB16_ZH', 'SB23_ZH', 'SB26_ZH', 'PKBD_ZH', 'SB09_ZH', 'SB17_ZH', 'TGLW_ZH', 'SB27_ZH', 'SB21_ZH', 'SB18_ZH', 'SB20_ZH', 'SB29_ZH', 'SB31_ZH', 'SLM3_ZH', 'FRU_ZH', 'SB02_ZH', 'PKBJ', 'SB01', 'SB30', 'SB08_ZH', 'SB32_ZH', 'SB34_ZH', 'SB28_ZH', 'SB37_ZH'];

var XIN_PC_NAME =['日本武士', '糖果碰碰乐', '水果拉霸2', '性感女仆', '开心农场', '运财羊', '美女沙排', '捕鱼王者', '鬼马小丑', '甜一甜屋', '象棋老虎机', '机动乐园', '牧场咖啡', '竞技狂热', '欧洲列强争霸', '关东煮', '夏日营地', '惊吓鬼屋', '海底漫游', '空中战争', '土地神', '侏罗纪', '红利百搭', '埃及奥秘', '疯狂马戏团', '欢乐时光', '布袋和尚', '百搭二王', '西洋棋老虎机', '海洋剧场', '极速幸运轮', '正财神', '越野机车', '水上乐园', '摇滚狂迷', '偏财神', '天空守护者', '武圣传', '水果拉霸', '复古花园', '杰克高手', '太空漫游', '灵猴献瑞', '麻将老虎机', '齐天大圣', '冰河世界', '武财神', '上海百乐门'];

var AG_PC_TYPE=['YP807', 'YP802', 'YP814', 'YP810', 'YP801', 'YP813', 'YP820', 'YP808', 'YP804', 'YP803', 'YP806', 'YP818', 'YP805', 'YP817'];

var AG_PC_IMG=['pc_animals', 'pc_benz', 'PC_BirdsBeastsMultiplayer', 'pc_forestDance_multi', 'pc_forestDance_single', 'pc_fruit_multi', 'pc_fruit_single', 'pc_fruitParty', 'pc_guessNfun', 'pc_horseRacing', 'pc_texasCowboy', 'ygfs_icon_pc', 'yjfs_icon', 'ymbn_icon'];

var AG_PC_NAME =['飞禽走兽', '奔驰宝马', '飞禽走兽多人版', '森林舞会多人', '森林舞会', '水果拉霸多人版', '彩金水果拉霸', '水果派对', '猜猜乐', '极速赛马', '德州牛仔', '多宝水果拉霸', '水果拉霸', '百人牛牛'];

/*
 *  銀行資料修改錯誤訊息
 */
 var BANK_EDIT_CODE={
    "Success":"提示您：稍后您会收到一条验证短信，请在30分钟内完成验证,过期请重新发送!",
					"codeInputErr":"请输入6位验证码!",
					"ParamNullException":"参数不能为空!",
					"CreateCodeNoTimeException":"操作太频繁,请1分钟后再试!","PromotionIsClosedException":"您已经参与了该活动，无法重复参与!",
					"NullCheckCodeException":"验证码不正确或已过期!","CheckCodeTypeErrorException":"请输入正确的验证码!",
					"EmailBindSuccess":"邮箱绑定成功","NullActiveSmsConfigException":"发送失败,找不到激活的手机！",
					"CheckCodeTimeOutException":"验证码已失效,请重新获取!",
					"PromotionConfigNotExistedException":"该活动不存在或已关闭,请联系客服!",
					"EmailHaveUsedException":"该邮箱地址已被注册,不可重复使用!",
					"PhoneNumberExistedException":"该手机号码已被注册,不可重复使用!",
					"GameIntegrationApiInvokeFailedException":"手机短信发送失败请联系客服!",
					"UserBasicInfoNotExistedException":"手机号码不正确,请先完善!",
					"NullEmailException":"请先完善您的邮箱信息!",
					"NullPhoneNumberException":"请先完善您的手机信息!",
					"UserTrueNameHaveBindException":"用户名字已绑定,无法重复绑定!",
					"UserEmailHaveBindException":"用户邮箱已绑定,无法重复绑定!",
					"UserMobilePhoneHaveBindException":"用户手机已绑定,无法重复绑定!",
					"checkValidMethod":"请选择接收验证码方式!",
					"uContactMethod1":"验证码已发至您邮箱中，若找不到请在垃圾邮箱翻翻哦~",
					"uContactMethod2":"验证码已发至您手机中，请查收~",
					"YPSmsReturnCode29Exception":"操作太频繁,同一个手机号同一个验证码模板,每30秒只能获取1条！",
					"YPSmsReturnCode22Exception":"操作太频繁,同一个手机号验证码类内容,每小时最多能获取3条 ！",
					"YPSmsReturnCode17Exception":"操作太频繁,同一个手机号验证码类内容,每天最多能获取到10条！"	,
					"DrawalOrderIsNullException":"要使用该彩金，您需要有一次成功提款！",
					"FAIL":"未成功接收验证码,请联系客服处理!" 
 }



//以下為支付參數

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

/*
 * 限额参数  暂时只有alipay有两类限额
 * min:最小限额
 * max:最大限额
 * tips:限额提示
 * types:所包含的支付渠道，一类限额 + 二类限额 = alipayCode
 * openTypes 每类限额的渠道所开启的支付渠道 
 * 
 */
var  depositLimits = {
		  //支付宝
		  aliPay:[{min:20,max:2000,tips:"单笔存款限额(元):20-1999",types:['29'],openTypes:[]},
		          {min:20,max:3000,tips:"单笔存款限额(元):20-2999",types:['6','7','10','12','13','14','15','16','17','18','20','23','24','25','26','28','30','31'],openTypes:[]}]		
}                                                                     
