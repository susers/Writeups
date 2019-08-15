function buy(){
	$('#response').hide();
	var input = $('#numbers')[0];
	if(input.validity.valid){
		var numbers = input.value;
		$.ajax({
		  method: "POST",
		  url: "buy_handle.php",
		  dataType: "json",
		  contentType: "application/json",
		  data: JSON.stringify({numbers: numbers })
		}).done(function(resp){
			if(resp.status == 'ok'){
				show_result(resp);
			} else {
				alert(resp.msg);
			}
		})
	} else {
		alert('输入号码不合法鸭！');
	}
}

function show_result(resp){
	$('#prize').text(resp.prize);
	var numbers = resp.numbers; //用户买的号码
	var win_nums = resp.win_nums; //开奖的号码
	var money = resp.money; //用户的钱
	var same_counter = resp.same_counter;//相同的个数
	var win_money = resp.win_money;//当局奖金
	$('#win_nums').html(win_nums);
	$('#user_number').html(numbers);
	$('#same_counter').html(same_counter);
	$('#money').html(money);
	$('#win_money').html(win_money);
	$('#result').show();
}

$(document).ready(function(){
	$('#submit_but').click(buy);
	$('form').submit(function( event ) {
	  buy();
	  return false;
	});
})
