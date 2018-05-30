<?php



/**
 * SMTP邮件发送类
 */

class Dmail {
    
	public  $error;
	private $config;
	
    /**
	 * 样式配置文件
	 */
	public function set($config) {
	    $this->config = array(
			'port' => $config['port'],
			'auth' => 1,
			'from' => $config['from'],
	        'server' => $config['host'],
			'mailsend' => 2,
			'mailusername' => 1,
			'maildelimiter'	=> 1,
			'auth_username'	=> $config['user'],
			'auth_password'	=> $config['pass'],
	    );
	}
	
    public function send($toemail, $subject, $message, $fname = '') {
	
		$mail = $this->config;
		if (!$mail['server']) {
            return FALSE;
        }
		
	    $cfg['server']  = $cfg['port'] = $cfg['auth'] = $cfg['from'] = $cfg['auth_username'] = $cfg['auth_password'] = '';
	    $cfg['charset'] = $charset = 'utf-8';
	    $cfg['server']  = $mail['server'];
	    $cfg['port'] = $mail['port'];
	    $cfg['auth'] = $mail['auth'] ? 1 : 0;
	    $cfg['from'] = $mail['from'];
	    $cfg['auth_username'] = $mail['auth_username'];
	    $cfg['auth_password'] = $mail['auth_password'];
		unset($mail);

        $mailusername = 1;
	    $maildelimiter = "\r\n"; //换行符
	    $cfg['port'] = $cfg['port'] ? $cfg['port'] : 25;

        $email_from = '=?'.$cfg['charset'].'?B?'.base64_encode($fname ? $fname : SITE_NAME)."?= <".$cfg['from'].">";
        $email_to = preg_match('/^(.+?) \<(.+?)\>$/',$toemail, $mats) ? ($mailusername ? '=?'.$cfg['charset'].'?B?'.base64_encode($mats[1])."?= <$mats[2]>" : $mats[2]) : $toemail;
	    $email_subject = '=?'.$cfg['charset'].'?B?'.base64_encode(preg_replace("/[\r|\n]/", '', $subject)).'?=';
	    $email_message = chunk_split(base64_encode(str_replace("\n", "\r\n", str_replace("\r", "\n", str_replace("\r\n", "\n", str_replace("\n\r", "\r", $message))))));

	    $host = $_SERVER['HTTP_HOST'];
	    $headers = "From: $email_from{$maildelimiter}X-Priority: 3{$maildelimiter}X-Mailer: $host {$maildelimiter}MIME-Version: 1.0{$maildelimiter}Content-type: text/html; charset=".$cfg['charset']."{$maildelimiter}Content-Transfer-Encoding: base64{$maildelimiter}";
	
        if(!$fp = @fsockopen($cfg['server'], $cfg['port'], $errno, $errstr, 30)) {
		    $this->runlog($cfg['server'].' - '.$cfg['auth_username'].' - '.$toemail, "CONNECT - Unable to connect to the SMTP server");
		    return FALSE;
	    }
		
	    stream_set_blocking($fp, true);
	    $lastmessage = fgets($fp, 512);
	    if(substr($lastmessage, 0, 3) != '220') {
			$this->runlog($cfg['server'].' - '.$cfg['auth_username'].' - '.$toemail, "CONNECT - $lastmessage");
		    return FALSE;
	    }
		
	    fputs($fp, ($cfg['auth'] ? 'EHLO' : 'HELO')." uchome\r\n");
	    $lastmessage = fgets($fp, 512);
	    if(substr($lastmessage, 0, 3) != 220 && substr($lastmessage, 0, 3) != 250) {
			$this->runlog($cfg['server'].' - '.$cfg['auth_username'].' - '.$toemail, "HELO/EHLO - $lastmessage");
		    return FALSE;
	    }
		
	    while(1) {
		    if(substr($lastmessage, 3, 1) != '-' || empty($lastmessage)) {
			    break;
		    }
		    $lastmessage = fgets($fp, 512);
	    }
		
	    if($cfg['auth']) {
		    fputs($fp, "AUTH LOGIN\r\n");
		    $lastmessage = fgets($fp, 512);
		    if(substr($lastmessage, 0, 3) != 334) {
				$this->runlog($cfg['server'].' - '.$cfg['auth_username'].' - '.$toemail, "AUTH LOGIN - $lastmessage");
			    return FALSE;
		    }
		    fputs($fp, base64_encode($cfg['auth_username']) . "\r\n");
		    $lastmessage = fgets($fp, 512);
	        if(substr($lastmessage, 0, 3) != 334) {
				$this->runlog($cfg['server'].' - '.$cfg['auth_username'].' - '.$toemail, "USERNAME - $lastmessage");
			    return FALSE;
		    }
		    fputs($fp, base64_encode($cfg['auth_password']) . "\r\n");
		    $lastmessage = fgets($fp, 512);
		    if(substr($lastmessage, 0, 3) != 235) {
				$this->runlog($cfg['server'].' - '.$cfg['auth_username'].' - '.$toemail, "PASSWORD - $lastmessage");
			    return FALSE;
		    }
		    $email_from = $cfg['from'];
	    }

	    fputs($fp, "MAIL FROM: <".preg_replace("/.*\<(.+?)\>.*/", "\\1", $email_from).">\r\n");
	    $lastmessage = fgets($fp, 512);
	    if(substr($lastmessage, 0, 3) != 250) {
		    fputs($fp, "MAIL FROM: <".preg_replace("/.*\<(.+?)\>.*/", "\\1", $email_from).">\r\n");
		    $lastmessage = fgets($fp, 512);
		    if(substr($lastmessage, 0, 3) != 250) {
				$this->runlog($cfg['server'].' - '.$cfg['auth_username'].' - '.$toemail, "MAIL FROM - $lastmessage");
			    return FALSE;
		    }
	    }

	    fputs($fp, "RCPT TO: <".preg_replace("/.*\<(.+?)\>.*/", "\\1", $toemail).">\r\n");
	    $lastmessage = fgets($fp, 512);
	    if(substr($lastmessage, 0, 3) != 250) {
		    fputs($fp, "RCPT TO: <".preg_replace("/.*\<(.+?)\>.*/", "\\1", $toemail).">\r\n");
		    $lastmessage = fgets($fp, 512);
				$this->runlog($cfg['server'].' - '.$cfg['auth_username'].' - '.$toemail, "RCPT TO - $lastmessage");
		    return FALSE;
	    }
	    fputs($fp, "DATA\r\n");
	    $lastmessage = fgets($fp, 512);
	    if(substr($lastmessage, 0, 3) != 354) {
				$this->runlog($cfg['server'].' - '.$cfg['auth_username'].' - '.$toemail, "DATA - $lastmessage");
		    return FALSE;
	    }
	    $headers .= 'Message-ID: <'.gmdate('YmdHs').'.'.substr(md5($email_message.microtime()), 0, 6).rand(100000, 999999).'@'.$_SERVER['HTTP_HOST'].">{$maildelimiter}";

	    fputs($fp, "Date: ".gmdate('r')."\r\n");
	    fputs($fp, "To: ".$email_to."\r\n");
	    fputs($fp, "Subject: ".$email_subject."\r\n");
	    fputs($fp, $headers."\r\n");
	    fputs($fp, "\r\n\r\n");
	    fputs($fp, "$email_message\r\n.\r\n");
	    $lastmessage = fgets($fp, 512);
	    if(substr($lastmessage, 0, 3) != 250) {
			$this->runlog($cfg['server'].' - '.$cfg['auth_username'].' - '.$toemail, "END - $lastmessage");
		    return FALSE;
	    }
	    fputs($fp, "QUIT\r\n");
	    return TRUE;
    }
	
	public function error() {
		return $this->error;
	}
	
	private function runlog($server, $msg) {
		$this->error = $msg;
		@file_put_contents(WEBPATH.'cache/mail_error.log', date('Y-m-d H:i:s').' ['.$server.'] '.str_replace(array(chr(13), chr(10)), '', $msg).PHP_EOL, FILE_APPEND);
	}
	
}