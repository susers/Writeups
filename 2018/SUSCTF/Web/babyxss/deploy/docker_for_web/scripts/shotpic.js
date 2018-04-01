/*
 * File Name: shotpic.js
 * Author: Image
 * mail: malingtao1019@163.com
 * Blog:http://blog.imagemlt.xyz
 * Created Time: 2018年03月19日 星期一 12时52分13秒
*/
var system=require('system');
var url=system.args[1];
var output=system.args[2];
var page=require('webpage').create();
var flag=phantom.addCookie({
	"domain":"127.0.0.1",
	"expires":"Fri, 01 Jan 2038 00:00:00 GMT",
	"expiry":2145916800,
	"httponly":false,
	"name":"flag",
	"path":"/",
	"secure":false,
	"value":"Susctf{s1mpl3_x55}"
});
page.viewportSize={'width':1024,'height':960};
page.onConsoleMessage=function(msg,line,source){
	console.log(msg);
	if(msg=="page loaded"){
		page.render(output);
		phantom.exit();
	}
}
page.open(url,function(status){
	if(status!=='success'){
		console.log('Unable to load the address!');
		phantom.exit();
	}
	else{
		window.setTimeout(function(){
			page.render(output);
			phantom.exit();
		},10000);
	}
})
