<?php defined('is_running') or die('Not an entry point...');

gpPlugin::incl('EasyNewsLetter.php');

class EasyNewsLetter_EditConfig extends EasyNewsLetter {

	// Constructor
	function EasyNewsLetter_EditConfig() {

		// Parent Class Constructor
		parent::__construct();

		$this->_loadCssAndJsFiles();

		if ($this->_doSaveConfig()) { 
			$this->_updateConfig();
			if ($this->_isValidConfig()) {
				$this->_saveConfig();
			} 
		}

		$this->_editConfig();

	}

	/////////////////////////////////////////////////////////////////////
	// PRIVATE METHODS
	/////////////////////////////////////////////////////////////////////


	private function _loadCssAndJsFiles() {

		global $addonPathData, $addonPathCode;

		// Css
		$file = $addonPathData.'/EasyNewsLetter.css';

		if (file_exists($file)) {
			$form_css = file_get_contents($file);
			$this->config['form_css'] = $form_css;
		} else {
			$this->config['form_css'] = '';
		}

		// Js
		$file = $addonPathData.'/EasyNewsLetter.js';

		if (file_exists($file)) {
			$form_js = file_get_contents($file);
			$this->config['form_js'] = $form_js;
		} else {
			$this->config['form_js'] = '';
		}

	}

	private function _isValidConfig() {

		if (!isset($_POST['from_email']) || empty($_POST['from_email'])) {
			message('The \'Email Address - From\' field should not be empty!');
			return false;
		}

		$from_email = $_POST['from_email'];
		if (!$this->isValidEmail($from_email)) {
			$from_email = htmlentities($from_email);
			message(sprintf('The email address %s is not valid!', $from_email));
			return false;
		} 

		return true;

	}

	function _updateConfig() {

		$cfg = array();

		$cfg['from_name'] = trim(strval($_POST['from_name']));
		$cfg['from_email'] = trim(strval($_POST['from_email']));
		$cfg['send_to_email_from'] = intval($_POST['send_to_email_from']);
		$cfg['on_mailing_draft'] = trim(strval($_POST['on_mailing_draft']));
		$cfg['double_optin_validate'] = intval($_POST['double_optin_validate']);
		$cfg['email_list_paginate'] = intval($_POST['email_list_paginate']);
		$cfg['form_css'] = trim(strval($_POST['form_css']));
		$cfg['form_js'] = trim(strval($_POST['form_js']));
		$cfg['hide_import_form'] = intval($_POST['hide_import_form']);

		$this->config = $cfg;
	
	}

	function _doSaveConfig() {

		return isset($_POST['save_config']);

	}

	function _editConfig() {

		global $addonPathCode, $page;

		// Css
		$css	= '<style type="text/css">'
			. '.EasyNewsLetter_EditConfig h2{'
			. '	margin-bottom: 20px'
			. '}'
			. '.EasyNewsLetter_EditConfig div{'
			. '	float: right'
			. '}'
			. '.EasyNewsLetter_EditConfig form{'
			. '	clear: both'
			. '}'
			. '.EasyNewsLetter_EditConfig label{'
			. '	display: block;'
			. '	width: 300px;'
			. '	float: left'
			. '}'
			. '.EasyNewsLetter_EditConfig textarea{'
			. '	width: 300px;'
			. '	height: 100px;'
			. '}'
			. '.EasyNewsLetter_EditConfig fieldset{'
			. '	-webkit-border-radius: 8px;'
			. '	-moz-border-radius: 8px;'
			. '	border-radius: 8px;'
			. '	padding: 10px;'
			. '	border:1px solid #ccc;'
			. '	margin: 0 0 10px 0;'
			. '	position: relative'
			. '}'
			. '.EasyNewsLetter_EditConfig img{'
			. '	vertical-align:top'
			. '}'
			. '</style>'
			;
		$page->head .= $css;

		// We load the editConfig.php template
		include($addonPathCode.'/Admin/EditConfig/EditConfig_Tmpl.php');

	}

	function _saveConfig() {

		global $config, $addonPathData, $langmessage;

		$cfg_file = $addonPathData.'/config.php';	
		$cfg = $this->config;

		$return_1 = gpFiles::SaveArray($cfg_file,'cfg',$cfg);

		$form_css = $this->config['form_css'];
		$file = $addonPathData.'/EasyNewsLetter.css';
		$return_2 = true;

		if ($form_css) {
			$return_2 = gpFiles::Save($file, $form_css);
		} elseif (file_exists($file) && is_file($file)) {
			$return_2 = unlink($file);
		}

		$form_js = $this->config['form_js'];
		$file = $addonPathData.'/EasyNewsLetter.js';
		$return_3 = true;

		if ($form_js) {
			$return_3 = gpFiles::Save($file, $form_js);
		} elseif (file_exists($file) && is_file($file)) {
			$return_3 = unlink($file);
		}

		if (($return = $return_1 && $return_2 && $return_3) === false) {
			message($langmessage['OOPS']);
		} else {
			message($langmessage['SAVED']);
		}

	}

}

