<?php
defined('is_running') or die('Not an entry point...');

abstract class EasyNewsletter {

	protected $config; // array
	protected $addresses; // array 

	public function __construct(){
		$this->_loadConfig();
	}

	protected function _loadConfig() {

		global $addonPathData;

		$cfg_file = $addonPathData.'/config.php';

		if (file_exists($cfg_file)) {
			include($cfg_file);
		} else {
			$cfg = $this->_loadDefaults($cfg_file);
		}

		$this->config = $cfg;

	}

	private function _loadDefaults($cfg_file) {

		global $config;

		//use default addon configuration
		$cfg = array();

		$cfg['from_name'] = (isset($config['from_name']) ? $config['from_name'] : '');
		$cfg['from_email'] = (isset($config['from_address']) ? $config['from_address'] : '');
		$cfg['send_to_email_from'] = 0;
		$cfg['on_mailing_draft'] = ''; 
		$cfg['double_optin_validate'] = 0; 
		$cfg['email_list_paginate'] = 10; 
		$cfg['hide_import_form'] = 0;

		if (gpFiles::SaveArray($cfg_file,'cfg',$cfg)) {
			$message = gpOutput::SelectText('Default settings saved.');
			message($message);
		}

		return $cfg;

	}

	protected function getAddresses() {
		
		global $addonDataFolder;
		$filename = $addonDataFolder."/addresses.dump";
		
		if(file_exists($filename)) {
			include_once($filename);
		} else {
			$addresses = array();
		}

		$this->addresses = $addresses;
	}

	protected function sessionStart() {

		if (!isset($_SESSION)) {
			session_start();
		}

	}

	protected function adminTopNav() {

		global $page, $langmessage;

		$current = $page->title;

		echo '<h2 class="hmargin">';

		echo common::Link('EasyNewsLetter', 'Subscription');

		echo ' &#187; ';

		if ($current != 'Admin_EasyNewsLetter_EditConfig') {
			echo common::Link('Admin_EasyNewsLetter_EditConfig', $langmessage['configuration']);
		} else {
			echo $langmessage['configuration'];
		}

		echo ' <span>|</span> ';

		if ($current != 'Admin_EasyNewsLetter_EmailList') {
			echo common::Link('Admin_EasyNewsLetter_EmailList', 'Email List');
		} else {
			echo 'Email List';
		}

		echo ' <span>|</span> ';

		if ($current != 'Admin_EasyNewsLetter_Mailing') {
			echo common::Link('Admin_EasyNewsLetter_Mailing', 'Mailing Form');
		} else {
			echo 'Mailing Form';
		}

		echo '</h2>';

	}

	// http://stackoverflow.com/questions/10046570/php-save-session-when-using-session-write-close
	protected function sessionWriteCloseAndRestart() {

		session_write_close();

		ini_set('session.use_only_cookies', false);
		ini_set('session.use_cookies', false);
		
		//May be necessary in some situations
		//ini_set('session.use_trans_sid', false); 

		ini_set('session.cache_limiter', null);

		//Reopen the (previously closed) session for writing.
		session_start(); 

	}

	protected function sendEmail($to, $subject, $message, $msg = true){
		global $langmessage, $config, $gp_mailer;

		includeFile('tool/email_mailer.php');

		//subject
		$subject = strip_tags($subject);
		// http://stackoverflow.com/questions/4389676/php-email-header-subject-encoding-problem
		$subject = '=?UTF-8?B?'.base64_encode($subject).'?=';

		//message
		$tags = '<p><div><span><font><b><i><tt><em><i><a><strong><blockquote>';
		$message = strip_tags($message,$tags);

		$gp_mailer->AddCustomHeader('Content-type: text/plain; charset=utf-8');
 		
		if(!empty($this->config['from_email'])) {
			$gp_mailer->SetFrom($this->config['from_email'], $this->config['from_name']);
		}
		
		if( $gp_mailer->SendEmail($to, $subject, $message) ){
			if ($msg) message($langmessage['message_sent']);
			return true;
		}

		if ($msg) message($langmessage['OOPS'].' (Send Failed)');
		return false;
	}

	// http://www.php.net/manual/fr/function.mt-rand.php#32698
	protected function makeRandomKey($min_length = 16, $max_length = 48) { 

		$key = ''; 

		// build range and shuffle range using ASCII table 
		for ($i=0; $i<=255; $i++) { 
			$range[] = chr($i); 
		} 

		// shuffle our range 3 times 
		for ($i=0; $i<=3; $i++) { 
			shuffle($range); 
		} 

		// loop for random number generation 
		for ($i = 0; $i < mt_rand($min_length, $max_length); $i++) { 
			$key .= $range[mt_rand(0, count($range) - 1)]; 
		} 

		$return = base64_encode($key); 

		if (!empty($return)) { 
			return $return; 
		} else { 
			return false; 
		} 

	} 

	protected function isValidEmail($email){
		return (bool)preg_match('/^[^@]+@[^@]+\.[^@]+$/', $email);
	}

	protected function varDump($var, $die = true) {

		echo "<pre>";
		var_dump($var);
		echo "</pre>";
		
		if ($die) die();		

	}
	
}
?>

