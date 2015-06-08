<?php
defined('is_running') or die('Not an entry point...');

gpPlugin::incl('EasyNewsLetter.php');

class EasyNewsLetter_Subscription extends EasyNewsLetter {

	private $email; // string
	private $key; // string

	public function __construct() {

		parent::__construct();

		$this->getAddresses();
		
		if ($this->doSubscribe()) { 
			if ($this->isValidSubscribe()) {
				$this->subscribe();
				return;
			} 
		}

		if ($this->doSubscribeValidation()) { 
			if ($this->isValidSubscribeValidation()) {
				$this->subscribeValidation();
			}
			return; 
		}

		if ($this->doUnsubscribe()) { 
			if ($this->isValidUnsubscribe()) {
				$this->Unsubscribe();
			} 
			return;
		}
		
		$this->SubscribeForm();
	}

	private function doSubscribe() { 

		return isset($_POST['cmd']) && ($_POST['cmd'] == 'subscribe');

	}

	private function isValidSubscribe(){

		global $langmessage;

		if( !common::verify_nonce('newsletter_post',$_POST['newsletter_nonce'],true) ){
			message($langmessage['OOPS'].'(Invalid Nonce)');
			return false;
		}
		
		if(!isset($_POST['nl_email'])) {
			message(gpOutput::SelectText('No mail address given.'));
			return false;
		}

		$nl_email = $_POST['nl_email'];

		if (mb_strlen($nl_email, 'UTF-8') > 254) {
			message($langmessage['OOPS'].'(Robot Detected)');
			return false;
		}
			
		if(empty($nl_email) || $nl_email == gpOutput::SelectText('your@email.here') || !$this->isValidEmail($nl_email)) {
			$nl_email = htmlentities($nl_email);
			message(sprintf(gpOutput::SelectText('The given mail address %s is invalid.'), $nl_email));
			return false;
		}

		if(array_key_exists($nl_email, $this->addresses)) {
			message(sprintf(gpOutput::SelectText('The given mail address %s is already registered.'), $nl_email));
			return false;
		}

		$this->email = $nl_email;

		return true;

	}
	
	private function subscribe(){
					
		global $addonDataFolder, $linkPrefix, $langmessage;

		$nl_email = $this->email;

		while (($key = $this->makeRandomKey()) === false);

		$this->addresses[$nl_email]['key'] = $key;
		$this->addresses[$nl_email]['datetime'] = time();
		$this->addresses[$nl_email]['sent'] = 0;

		$footer	= gpOutput::SelectText('You received this e-mail because you subscribed the newsletter from %1$s.')
			. "\r\n"
			. gpOutput::SelectText('If you want to unsubscribe, please click the following link:')
			. "\r\n"
			. 'http://%1$s%2$s/EasyNewsLetter?cmd=unsubscribe&nl_email=%3$s&key=%4$s'
			;

		if ($this->config['double_optin_validate']) {
			$this->addresses[$nl_email]['activated'] = 0;
			$subject = gpOutput::SelectText('Thank you for subscribing!');
			$body	= gpOutput::SelectText('Thank you for subscribing our newsletter.')
				. "\r\n"
				. gpOutput::SelectText('Please click on the activation link to validate your subscription:')
				. "\r\n"
				. 'http://%1$s%2$s/EasyNewsLetter?cmd=validate&nl_email=%3$s&key=%4$s'
				;
			$message = sprintf($body, $_SERVER['SERVER_NAME'], $linkPrefix,	urlencode($nl_email), urlencode($key))
				. "\r\n\r\n--\r\n"
				. sprintf($footer, $_SERVER['SERVER_NAME'], $linkPrefix, urlencode($nl_email), urlencode($key))
				;
		} else {
			$this->addresses[$nl_email]['activated'] = 1;
			$subject = gpOutput::SelectText('Thank you for subscribing!');
			$message = gpOutput::SelectText('Thank you for subscribing our newsletter.')
				. "\r\n\r\n--\r\n"
				. sprintf($footer, $_SERVER['SERVER_NAME'], $linkPrefix,
					urlencode($nl_email), urlencode($key))
				;
		}

		$filename = $addonDataFolder."/addresses.dump";
		gpFiles::SaveArray($filename,'addresses', $this->addresses);
		
		if($this->sendEmail($nl_email, $subject, $message) === false) {
			echo '<h2>'.gpOutput::SelectText('Error').'</h2>';
			echo '<p>'.sprintf(gpOutput::SelectText('An error was encountered while sending an email to %s.'), $nl_email).'</p>';
		} else {
			echo '<h2>'.gpOutput::SelectText('Thank You!').'</h2>';
			if ($this->config['double_optin_validate']) {
				echo '<p>', sprintf(gpOutput::SelectText('An activation link has been sent to %s.'), $nl_email), '</p>';
				echo '<p>', gpOutput::SelectText('Note that you must activate your subscription by clicking on the activation link to receive the newsletters.'), '</p>';
			} else {
				echo '<p>', sprintf(gpOutput::SelectText('The newsletter will be sent to %s.'), $nl_email), '</p>';
			}
		}

	}

	private function doSubscribeValidation() { 

		return isset($_GET['cmd']) && ($_GET['cmd'] == 'validate');

	} 


	private function isValidSubscribeValidation() {

		if (!$this->config['double_optin_validate']) {
			echo '<h2>'.gpOutput::SelectText('Error').'</h2>';
			echo '<p>'.gpOutput::SelectText('There is no need to activate your subscription on this site.').'</p>';
			return false;
		}
		
		if(isset($_GET['nl_email'])) {
			$nl_email = $_GET['nl_email'];
		} else {
			echo '<h2>'.gpOutput::SelectText('Error').'</h2>';
			echo '<p>'.gpOutput::SelectText('No mail address given.').'</p>';
			return false;
		}
		
		if(!array_key_exists($nl_email, $this->addresses)) {
			$nl_email = htmlentities($nl_email);
			echo '<h2>'.gpOutput::SelectText('Error').'</h2>';
			echo '<p>'.sprintf(gpOutput::SelectText('The given mail address %s does not exist.'), $nl_email).'</p>';
			return false;
		}

		if(isset($_GET['key'])) {
			$key = $_GET['key'];
		} else {
			echo '<h2>'.gpOutput::SelectText('Error').'</h2>';
			echo '<p>'.gpOutput::SelectText('No key given.').'</p>';
			return false;
		}

		if($this->addresses[$nl_email]['key'] !== $key) {
			$nl_email = htmlentities($nl_email);
			$key = htmlentities($key);
			echo '<h2>'.gpOutput::SelectText('Error').'</h2>';
			echo '<p>'.sprintf('The given key %s does not match with the e-mail address %s.', $key, $nl_email).'</p>';
			return false;
		}

		if($this->addresses[$nl_email]['activated'] == 1) {
			$nl_email = htmlentities($nl_email);
			echo '<h2>'.gpOutput::SelectText('Error').'</h2>';
			echo '<p>'.sprintf('The subscription for the e-mail address %s is already activated.', $nl_email).'</p>';
			return false;
		}		

		$this->email = $nl_email;
		$this->key = $key;

		return true;

	}

	private function subscribeValidation(){
					
		global $addonDataFolder, $linkPrefix, $langmessage;

		$nl_email = $this->email;

		$this->addresses[$nl_email]['activated'] = 1;

		$filename = $addonDataFolder."/addresses.dump";
		gpFiles::SaveArray($filename,'addresses', $this->addresses);

		$footer	= gpOutput::SelectText('You received this e-mail because you subscribed the newsletter from %1$s.')
			. "\r\n"
			. gpOutput::SelectText('If you want to unsubscribe, please click the following link:')
			. "\r\n"
			. 'http://%1$s%2$s/EasyNewsLetter?cmd=unsubscribe&nl_email=%3$s&key=%4$s'
			;

		$subject = gpOutput::SelectText('Thank you for subscribing!');
		$message = gpOutput::SelectText('Thank you for subscribing our newsletter.')
			. "\r\n--\r\n"
			. sprintf($footer, $_SERVER['SERVER_NAME'], $linkPrefix, urlencode($nl_email), urlencode($this->key))
			;
		
		if($this->sendEmail($nl_email, $subject, $message) === false) {
			$nl_email = htmlentities($nl_email);
			echo '<h2>'.gpOutput::SelectText('Error').'</h2>';
			echo '<p>'.sprintf(gpOutput::SelectText('An error was encountered while sending an email to %s'), $nl_email).'</p>';
		} else {
			$nl_email = htmlentities($nl_email);
			echo '<h2>'.gpOutput::SelectText('Thank You!').'</h2>';
			echo '<p>'.sprintf(gpOutput::SelectText('The newsletter will be sent to %s.'), $nl_email).'</p>';
		}

	}

	private function doUnsubscribe() { 

		return isset($_GET['cmd']) && ($_GET['cmd'] == 'unsubscribe');

	}

	private function isValidUnsubscribe() {
		
		if(isset($_GET['nl_email'])) {
			$nl_email = $_GET['nl_email'];
		} else {
			echo '<h2>'.gpOutput::SelectText('Error').'</h2>';
			echo '<p>'.gpOutput::SelectText('No mail address given.').'</p>';
			return false;
		}
		
		if(!array_key_exists($nl_email, $this->addresses)) {
			$nl_email = htmlentities($nl_email);
			echo '<h2>'.gpOutput::SelectText('Error').'</h2>';
			echo '<p>'.sprintf(gpOutput::SelectText('The given mail address %s does not exist.'), $nl_email).'</p>';
			return false;
		}

		if(isset($_GET['key'])) {
			$key = $_GET['key'];
		} else {
			echo '<h2>'.gpOutput::SelectText('Error').'</h2>';
			echo '<p>'.gpOutput::SelectText('No key given.').'</p>';
			return false;
		}

		if($this->addresses[$nl_email]['key'] !== $key) {
			$nl_email = htmlentities($nl_email);
			$key = htmlentities($key);
			echo '<h2>'.gpOutput::SelectText('Error').'</h2>';
			echo '<p>'.sprintf('The given key %s does not match with the e-mail address %s.', $key, $nl_email).'</p>';
			return false;
		}		

		$this->email = $nl_email;
		$this->key = $key;

		return true;

	}
	
	private function unsubscribe(){
		
		global $addonDataFolder;

		unset($this->addresses[$this->email]);

		$filename = $addonDataFolder."/addresses.dump";
		gpFiles::SaveArray($filename,'addresses',$this->addresses);

		echo '<h2>'.gpOutput::SelectText('What a pity!').'</h2>';
		echo '<p>'.sprintf(gpOutput::SelectText('The newsletter won\'t be sent to %s anymore.'), $this->email).'</p>';
	}

	private function SubscribeForm() {

		gpPlugin::incl('Gadget/Subscribe.php');
		$myGadget_Newsletter = new EasyNewsLetter_Subscribe_Gadget();

	}

}
?>

