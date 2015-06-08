<?php
defined('is_running') or die('Not an entry point...');

class admin_login extends display{
	var $pagetype = 'admin_display';

	function __construct($title){

		common::LoadComponents('gp-admin-css');

		$this->requested		= str_replace(' ','_',$title);
		$this->title			= $title;
		$this->get_theme_css 	= false;
		$_REQUEST['gpreq']		= 'admin';

		$this->head .= "\n".'<meta name="robots" content="noindex,nofollow" />';
		@header( 'X-Frame-Options: SAMEORIGIN' );
	}

	function RunScript(){}

	function GetGpxContent(){


		$this->head .= "\n<script type=\"text/javascript\">var IE_LT_8 = false;</script><!--[if lt IE 8]>\n<script type=\"text/javascript\">IE_LT_8=true;</script>\n<![endif]-->";
		$this->head_js[] = '/include/js/login.js';
		$this->head_js[] = '/include/js/md5_sha.js';
		$this->head_js[] = '/include/thirdparty/js/jsSHA.js';

		$this->css_admin[] = '/include/css/login.css';
		$_POST += array('username'=>'');

		$this->admin_js = true;
		includeFile('tool/sessions.php');
		gpsession::cookie('g',2);



		$this->BrowserWarning();
		$this->JavascriptWarning();

		echo '<div class="req_script nodisplay" id="login_container">';
		echo '<table><tr><td>';

		$cmd = common::GetCommand();
		switch($cmd){
			case 'send_password';
				if( $this->SendPassword() ){
					$this->LoginForm();
				}else{
					$this->FogottenPassword();
				}
			break;

			case 'forgotten':
				$this->FogottenPassword();
			break;
			default:
				$this->LoginForm();
			break;
		}

		echo '</td></tr></table>';
		echo '</div>';
	}


	function FogottenPassword(){
		global $langmessage;

		$_POST += array('username'=>'');
		$this->css_admin[] = '/include/css/login.css';


		echo '<div id="loginform">';
		echo '<form class="loginform" action="'.common::GetUrl('Admin').'" method="post">';

		echo '<p class="login_text">';
		echo '<input type="text" name="username" value="'.htmlspecialchars($_POST['username']).'" placeholder="'.htmlspecialchars($langmessage['username']).'"/>';
		echo '</p>';

		echo '<input type="hidden" name="cmd" value="send_password" />';
		echo '<input type="submit" name="aa" value="'.$langmessage['send_password'].'" class="login_submit" />';
		echo ' &nbsp; <label>'. common::Link('Admin',$langmessage['back']).'</label>';

		echo '</form>';
		echo '</div>';

	}

	function LoginForm(){
		global $langmessage;


		$_REQUEST += array('file'=>'');



		echo '<div id="loginform">';
			echo '<div id="login_timeout" class="nodisplay">Log in Timeout: '.common::Link('Admin','Reload to continue...').'</div>';

			echo '<form action="'.common::GetUrl('Admin').'" method="post" id="login_form">';
			echo '<input type="hidden" name="file" value="'.htmlspecialchars($_REQUEST['file']).'">';	//for redirection

			echo '<div>';
			echo '<input type="hidden" name="cmd" value="login" />';
			echo '<input type="hidden" name="login_nonce" value="'.htmlspecialchars(common::new_nonce('login_nonce',true,300)).'" />';
			echo '</div>';

			echo '<p class="login_text">';
			echo '<input type="text" name="username" value="'.htmlspecialchars($_POST['username']).'" placeholder="'.htmlspecialchars($langmessage['username']).'" />';
			echo '<input type="hidden" name="user_sha" value="" />';
			echo '</p>';

			echo '<p class="login_text">';
			echo '<input type="password" class="password" name="password" value="" placeholder="'.htmlspecialchars($langmessage['password']).'"/>';
			echo '<input type="hidden" name="pass_md5" value="" />';
			echo '<input type="hidden" name="pass_sha" value="" />';
			echo '<input type="hidden" name="pass_sha512" value="" />';
			echo '</p>';

			echo '<p>';
			echo '<input type="submit" class="login_submit" value="'.$langmessage['login'].'" />';
			echo ' &nbsp; ';
			echo common::Link('',$langmessage['cancel']);
			echo '</p>';

			echo '<p>';
			echo '<label>';
			echo '<input type="checkbox" name="remember" '.$this->checked('remember').'/> ';
			echo '<span>'.$langmessage['remember_me'].'</span>';
			echo '</label> ';

			echo '<label>';
			echo '<input type="checkbox" name="encrypted" '.$this->checked('encrypted').'/> ';
			echo '<span>'.$langmessage['send_encrypted'].'</span>';
			echo '</label>';
			echo '</p>';

			echo '<div>';
			echo '<label>';
			$url = common::GetUrl('Admin','cmd=forgotten');
			echo sprintf($langmessage['forgotten_password'],$url);
			echo '</label>';
			echo '</div>';


			echo '</form>';
		echo '</div>';
	}

	function BrowserWarning(){
		global $langmessage;

		echo '<div id="browser_warning" class="nodisplay">';
		echo '<div><b>'.$langmessage['Browser Warning'].'</b></div>';
		echo '<p>';
		echo $langmessage['Browser !Supported'];
		echo '</p>';
		echo '<p>';
		echo '<a href="http://www.mozilla.com/">Firefox</a>';
		echo '<a href="http://www.google.com/chrome">Chrome</a>';
		echo '<a href="http://www.apple.com/safari">Safari</a>';
		echo '<a href="http://www.microsoft.com/windows/internet-explorer/default.aspx">Explorer</a>';

		echo '</p>';
		echo'</div>';
	}

	function JavascriptWarning(){
		global $langmessage;

		echo '<div class="without_script" id="javascript_warning">';
		echo '<p><b>'.$langmessage['JAVASCRIPT_REQ'].'</b></p>';
		echo '<p>';
		echo $langmessage['INCOMPAT_BROWSER'];
		echo ' ';
		echo $langmessage['MODERN_BROWSER'];
		echo '</p>';
		echo '</div>';
	}


	function Checked($name){

		if( strtoupper($_SERVER['REQUEST_METHOD']) !== 'POST' )
			return ' checked="checked" ';

		if( !isset($_POST[$name]) )
			return '';

		return ' checked="checked" ';
	}


	function SendPassword(){
		global $langmessage, $gp_mailer, $config;

		includeFile('tool/email_mailer.php');
		$users		= gpFiles::Get('_site/users');
		$username	= $_POST['username'];

		if( !isset($users[$username]) ){
			message($langmessage['OOPS']);
			return false;
		}

		$userinfo = $users[$username];



		if( empty($userinfo['email']) ){
			message($langmessage['no_email_provided']);
			return false;
		}

		$passwordChars	= str_repeat('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',3);
		$newpass		= str_shuffle($passwordChars);
		$newpass		= substr($newpass,0,8);
		$pass_hash		= gpsession::PassAlgo($userinfo);

		$users[$username]['newpass'] = common::hash($newpass,$pass_hash);
		if( !gpFiles::SaveData('_site/users','users',$users) ){
			message($langmessage['OOPS']);
			return false;
		}

		if( isset($_SERVER['HTTP_HOST']) ){
			$server = $_SERVER['HTTP_HOST'];
		}else{
			$server = $_SERVER['SERVER_NAME'];
		}

		$link = common::AbsoluteLink('Admin',$langmessage['login']);
		$message = sprintf($langmessage['passwordremindertext'],$server,$link,$username,$newpass);

		if( $gp_mailer->SendEmail($userinfo['email'], $langmessage['new_password'], $message) ){
			list($namepart,$sitepart) = explode('@',$userinfo['email']);
			$showemail = substr($namepart,0,3).'...@'.$sitepart;
			message(sprintf($langmessage['password_sent'],$username,$showemail));
			return true;
		}


		message($langmessage['OOPS'].' (Email not sent)');

		return false;
	}


}