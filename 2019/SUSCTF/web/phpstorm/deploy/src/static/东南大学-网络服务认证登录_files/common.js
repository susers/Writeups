function addToFavorite(){
	try{
		window.external.AddFavorite(location.href,document.title);
	}
	catch(e){
		alert('请按 Ctrl+D 收藏本页');
	}
}

function isIPAddress(ip) {
	var reg=/^(([01]?[\d]{1,2})|(2[0-4][\d])|(25[0-5]))(\.(([01]?[\d]{1,2})|(2[0-4][\d])|(25[0-5]))){3}$/

	return reg.test(ip);
}

function isPort(port) {
	return ((port<65536) && (port>0));
}

function isVariable(variable) {
	var reg=/^\w+([-+.]\w+)*$/

	return reg.test(variable);
}

function isMacAddress(mac) {
	var reg = /^[a-f\d]{2}:[a-f\d]{2}:[a-f\d]{2}:[a-f\d]{2}:[a-f\d]{2}:[a-f\d]{2}$/;

	return reg.test(mac);
}

function isPositiveInteger(variable) {
	var reg = /^[0-9]*[1-9][0-9]*$/;

	return reg.test(variable);
}

function isPositiveNumber(variable) {
	var reg = /^([1-9]\d*\.\d*|0\.\d+|[1-9]\d*|0)$/;

	return reg.test(variable);
}

//校验用户名
function validUsername(username) {
	//1·由字母a～z(不区分大小写)、数字0～9、点、减号或下划线组成
	//2·用户名长度为1～32个字符
	var reg = /^[a-zA-Z0-9_\.\-]{1,32}$/; 

	return reg.test(username);
}

//校验密码
function validPassword(password) {
	//1 可以全数字
	//2 可以全字母
	//3 可以全特殊字符(~!@#$%^&*.)
	//4 三种的组合
	//5 可以是任意两种的组合
	//6 长度6-32
	var reg = /^[\@A-Za-z0-9\!\#\$\%\^\&\*\.\~\-\_]{6,32}$/;

	return reg.test(password);
}

//校验金额
function validMoney(money) {
	//1.非负整数输入，如0、100等
	//2.两位小数的非负浮点数输入
	var reg = /^(([1-9]\d{0,9})|0)(\.\d{1,2})?$/;

	return reg.test(money);
}

//校验手机号码
function validPhone(phone) {
	var reg = /^1[3|4|5|8][0-9]\d{8}$/;
	
	return reg.test(phone);
}

//校验邮箱
function validEmail(email) {
	var reg = /^([a-z0-9_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})$/;

	return reg.test(email);
}

//校验MAC地址
function validMac(mac) {
	var reg = /[A-Fa-f0-9]{2}:[A-Fa-f0-9]{2}:[A-Fa-f0-9]{2}:[A-Fa-f0-9]{2}:[A-Fa-f0-9]{2}:[A-Fa-f0-9]{2}/;

	return reg.test(mac);
}