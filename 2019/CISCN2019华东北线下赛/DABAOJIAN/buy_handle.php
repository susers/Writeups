<?php
	include_once('check_login.php');
	header('Content-Type: application/json');
	function get_win_num(){
		$win_nums = "";
		for($j=0;$j<6;$j++){
			$win_nums .= random_int(0,9);
		}
		return $win_nums;
	}
	$data = json_decode(file_get_contents('php://input'), true);
	function buy($data){
		check_login();
		$money = $_SESSION['money'];
		if($money < 5){
			die("钱数低于最低要求！");
		}
		$numbers = $data['numbers'];
		$win_nums = get_win_num();
		$same_counter = 0;

		for($i=0; $i<6; $i++){
			if($numbers[$i] == $win_nums[$i]){
				$same_counter++;
			}
		}

		if($same_counter == 2){
			$win_money = 10;
		}elseif ($same_counter == 3) {
			$win_money = 50;
		}elseif ($same_counter == 4) {
			$win_money = 500;
		}elseif ($same_counter == 5) {
			$win_money = 2000;
		}elseif ($same_counter == 6) {
			$win_money = 50000;
		}else {
			$win_money = 0;
		}

		$money -= 5;
		$money += $win_money;
		$_SESSION['money'] = $money;
		$response_text = array('status'=>'ok','numbers'=>$numbers, 'win_nums'=>$win_nums, 'money'=>$money, 'win_money'=>$win_money, 'same_counter'=> $same_counter, 'data' => $data);
		$date = json_encode($response_text);
		echo $date;
	}
	buy($data);
?>
