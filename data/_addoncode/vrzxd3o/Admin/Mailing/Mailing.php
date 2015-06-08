<?php
defined('is_running') or die('Not an entry point...');

gpPlugin::incl('EasyNewsLetter.php');

class EasyNewsLetter_Mailing extends EasyNewsLetter {

	private $subject; // string
	private $message; // string
	private $failed; // array
	private $draft; // array

	public function __construct() {

		global $addonDataFolder;

		parent::__construct();

		$this->sessionStart();
		$this->getAddresses();

		// Ajax call (load draft)
		if ($this->doLoadDraft()) {
			if ($this->isValidDraft()) {
				$this->loadDraft();
			}
			die();
		}

		// Ajax call (save draft)
		if ($this->doSaveDraft()) {
			if ($this->isValidMailing(true)) {
				$this->saveDraft();
			} 
			die();
		}

		// Ajax call (send draft)
		if ($this->doSendDraft()) {
			if ($this->isValidMailing(true)) {
				$this->SendDraft();
			} 
			die();
		}

		// Sending failed - recovery 
		if ($this->unfinishedMailingFound()) {
			$this->showUnfinishedMailingForm();
			return;
		}

		// Send the newsletter
		if ($this->doSendMailing()) { 
			if ($this->isValidMailing()) {
				$this->sendMailing();
				return;
			} 
		}

		// Writing/Sending form
		$this->MailingForm();

	}

	/////////////////////////////////////////////////////////////////////
	// PRIVATE METHODS
	/////////////////////////////////////////////////////////////////////

	private function doSendDraft() {

		return isset($_POST['cmd']) && ($_POST['cmd'] == 'send_draft');

	}

	private function doLoadDraft() {

		return isset($_POST['cmd']) && ($_POST['cmd'] == 'load_draft');

	}

	private function doSaveDraft() {

		return isset($_POST['cmd']) && ($_POST['cmd'] == 'save_draft');

	}

	private function saveDraft() {

		global $addonDataFolder, $langmessage;

		$subject = $this->cleanText($this->subject);
		$message = $this->cleanText($this->message);
		
		$draft = array(	'subject' => $subject,
				'message' => $message
				);

		$filename = $addonDataFolder."/draft.php";
		$data = array();

		if (gpFiles::SaveArray($filename,'draft',$draft) === false) {
			$data['msg'] = $langmessage['OOPS'];
			$data['error'] = 1;
		} else {
			$data['msg'] = $langmessage['SAVED'];
			$data['error'] = 0;
		}

		$this->sendAsJson($data);

	}

	private function loadDraft() {

		global $addonDataFolder;

		$draft = array_map('htmlspecialchars_decode', $this->draft);
		
		$data = array();
		$data['draft'] = $draft;
		$data['msg'] = 'The draft has been loaded!';
		$data['error'] = 0;

		$this->sendAsJson($data);

	}

	protected function isValidDraft() {
		
		global $addonDataFolder;

		$filename = $addonDataFolder."/draft.php";
		
		if(!file_exists($filename)) {
			$data = array();
			$data['msg'] = 'The draft file does not exist!';
			$data['error'] = 1;
			$this->sendAsJson($data);
			return false;
		}

		include_once($filename);

		if(!isset($draft) || empty($draft)) {
			$data = array();
			$data['msg'] = 'The draft file is empty!';
			$data['error'] = 1;
			$this->sendAsJson($data);
			return false;
		}

		$this->draft = $draft;
		return true;
	}

	private function sendDraft() {

		global $langmessage;

		$data = array();

		$subject = '[Test] ' . $this->subject;

		if ($this->sendEmail($this->config['from_email'], $subject, $this->message, false) === false) {
			$data['msg'] = $langmessage['OOPS'].' (Send Failed)';
			$data['error'] = 1;
		} else {
			$data['msg'] = $langmessage['message_sent'];
			$data['error'] = 0;
		}

		$this->sendAsJson($data);

	}

	private function unfinishedMailingFound() {

		if (!isset($_SESSION['EasyNewsLetter'])) {
			return false;
		}

		if (!isset($_SESSION['EasyNewsLetter']['email_list'])) {
			unset($_SESSION['EasyNewsLetter']);
			return false;
		}

		if (empty($_SESSION['EasyNewsLetter']['email_list'])) {
			unset($_SESSION['EasyNewsLetter']);
			return false;
		}

		// Store session data
		$this->subject = $_SESSION['EasyNewsLetter']['subject'];
		$this->message = $_SESSION['EasyNewsLetter']['message'];
		$this->email_list = $_SESSION['EasyNewsLetter']['email_list'];

		// Clean session data
		unset($_SESSION['EasyNewsLetter']);

		return true;

	}

	private function showUnfinishedMailingForm() {

		global $addonPathCode;

		$this->setCss();		
		$tmpl = $addonPathCode.'/Admin/Mailing/UnfinishedMailingForm_Tmpl.php';
		include($tmpl);

	}

	private function doSendMailing() {

		return isset($_POST['cmd']) && ($_POST['cmd'] == 'send_mailing');

	}

	private function isValidMailing($test = false) {

		if (!$test && empty($this->addresses)) {
			message('Sorry but there is no subscriber yet.');
			return false;
		}

		if (!$test && !count(array_filter($this->addresses, array($this, '_callbackIsActivated')))) {
			message('Sorry but there is no active subscriber.');
			return false;
		}

		if(!isset($_POST['subject']) || !isset($_POST['message'])) {
			if ($test === true) {
				$data = array();
				$data['msg'] = 'Invalid Data for sending.';
				$data['error'] = 1;
				$this->sendAsJson($data);
			} else {
				message('Invalid Data for sending.');
			}
			return false;
		}

		if(empty($_POST['subject']) || empty($_POST['message'])) {
			if ($test === true) {
				$data = array();
				$data['msg'] = 'Invalid Data for sending.';
				$data['error'] = 1;
				$this->sendAsJson($data);
			} else {
				message('Invalid Data for sending.');
			}
			return false;
		}
		
		$this->subject = $_POST['subject'];
		$this->message = $_POST['message'];

		return true;

	}

	private function sendMailing() {

		global $linkPrefix, $addonPathCode, $addonDataFolder;

		// May not always work especially if this function is disabled
		// In this case, the "session-based recovery system" should help...
		@set_time_limit(0);

		// Extra stuff
		$this->sendMailToEmailFrom();
		$this->draftAction();

		$subject = $this->subject;
		$message = $this->message; 

		// Save mailing data in session
		$_SESSION['EasyNewsLetter']['subject'] = $subject;
		$_SESSION['EasyNewsLetter']['message'] = $message;
		
		$email_list = $this->getEmailList();

		$failed = array();
		
		foreach($email_list as $index => $email) {

			// Email not found
			if (!isset($this->addresses[$email])) {
				continue;
			}

			// Subscription not activated
			if (!$this->addresses[$email]['activated']) {
				continue;
			}

			// Email data 
			$key = $this->addresses[$email]['key'];
			$server = $_SERVER['SERVER_NAME'];
			$enc_email = urlencode($email);
			$enc_key =  urlencode($key);

			$mailfooter	= gpOutput::SelectText('You received this e-mail because you subscribed the newsletter from %1$s.')
					 . "\r\n"
					. gpOutput::SelectText('If you want to unsubscribe, please click the following link:') 
					. "\r\n"
					. 'http://%1$s%2$s/EasyNewsLetter?cmd=unsubscribe&nl_email=%3$s&key=%4$s'
					;

			$message = $this->message
					. "\r\n--\r\n" 
					. sprintf($mailfooter, $server, $linkPrefix, $enc_email, $enc_key)
					;

			// Send email and keep track of not sent emails
			if($this->sendEmail($email, $subject, $message, false) === false) {
				$failed[] = $email;
			} else {
				// Increment 'sent' counter
				$this->addresses[$email]['sent']++;
				$filename = $addonDataFolder."/addresses.dump";
				gpFiles::SaveArray($filename,'addresses',$this->addresses);
			}

			//if (!rand(0,4)) die();

			// Save mailing current context in session
			unset($email_list[$index]);
			$_SESSION['EasyNewsLetter']['email_list'] = array_merge($email_list, $failed);
			$this->sessionWriteCloseAndRestart();

		}

		$this->failed = $failed;

		$this->setCss();	
		$tmpl = $addonPathCode.'/Admin/Mailing/MailingSent_Tmpl.php';
		include($tmpl);

	}

	private function sendMailToEmailFrom() {

		if (!$this->config['send_to_email_from']) {
			return;
		}

		$subject = '[Copy] ' . $this->subject;

		$this->sendEmail($this->config['from_email'], $subject, $this->message);

	}

	private function draftAction() {

		global $addonDataFolder;;

		if (empty($this->config['on_mailing_draft'])) {
			return;
		}

		$filename = $addonDataFolder."/draft.php";

		if ($this->config['on_mailing_draft'] == 'update') {
			$subject = $this->cleanText($this->subject);
			$message = $this->cleanText($this->message);
			$draft = array(	'subject' => $subject, 'message' => $message);
			gpFiles::SaveArray($filename,'draft',$draft);
		} elseif ($this->config['on_mailing_draft'] == 'delete') {
			unlink($filename);
		}

	}

	private function getEmailList() {

		if (isset($_POST['email_list'])) {
			return (array) $_POST['email_list'];
		} else {
			return array_keys($this->addresses);	
		}

	}

	private function MailingForm() {

		global $addonPathCode, $page;

		$this->setCss();
		$this->setJs();
		
		$tmpl = $addonPathCode.'/Admin/Mailing/MailingForm_Tmpl.php';
		include($tmpl);

	}

	private function setJs() {

		global $addonPathCode, $page;

		$file = $addonPathCode.'/Js/drafting.js';

		if (!file_exists($file)) {
			return;	
		} 

		$js = file_get_contents($file);
		$nonce = common::new_nonce('post',true);
		$js = str_replace('###NONCE###', $nonce, $js);

		$page->jQueryCode .= $js; 

	}

	private function setCss() {

		global $page;

		// Css
		$css	= '<style type="text/css">'
			. '#drafting_wrapper,'
			. '#ticker_wrapper{'
			. '	width: 100%;'
			. '	text-align: right;'
			. '	height: 20px;'
			. '}'
			. '#drafting_wrapper a.disabled,'
			. '#drafting_wrapper a.disabled:hover,'
			. '#drafting_wrapper a.disabled:visited{'
			. '	color: #333;'
			. '}'
			. '.EasyNewsLetter_Form label,'
			. '.EasyNewsLetter_Unfinished label,'
			. '.EasyNewsLetter_Sent label{'
			. '	display: block;'
			. '	width: 150px;'
			. '	float: left'
			. '}'
			. '.EasyNewsLetter_Form input[type=text],'
			. '.EasyNewsLetter_Unfinished input[type=text],'
			. '.EasyNewsLetter_Sent input[type=text]{'
			. '	width: 300px !important;'
			. '}'
			. '.EasyNewsLetter_Form textarea,'
			. '.EasyNewsLetter_Unfinished textarea,'
			. '.EasyNewsLetter_Sent textarea{'
			. '	width: 300px;'
			. '}'
			. '.EasyNewsLetter_Form fieldset,'
			. '.EasyNewsLetter_Unfinished fieldset,'
			. '.EasyNewsLetter_Sent fieldset{'
			. '	-webkit-border-radius: 8px;'
			. '	-moz-border-radius: 8px;'
			. '	border-radius: 8px;'
			. '	padding: 10px;'
			. '	border:1px solid #ccc;'
			. '	margin: 0 0 10px 0;'
			. '	position: relative'
			. '}'
			. '</style>'
			;

		$page->head .= $css;

	}
	
	private function sendAsJson($arr){
		header('Content-Type: application/json');
		echo common::JsonEncode($arr);
	}

	private function cleanText($txt) {
		return htmlspecialchars(strip_tags($this->br2nl($txt)));
	}

	//http://www.php.net/manual/fr/function.nl2br.php#86678
	private function br2nl($string) {
	    return preg_replace('/\<br(\s*)?\/?\>/i', "\n", $string);
	}

	private function _callbackIsActivated($subscriber) {
		return ($subscriber['activated'] ? true : false);
	}

}


