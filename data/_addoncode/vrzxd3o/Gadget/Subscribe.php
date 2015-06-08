<?php
defined('is_running') or die('Not an entry point...');

class EasyNewsLetter_Subscribe_Gadget {
	public function __construct(){

		global $page, $addonFolderName, $addonPathCode;

		$tmpl = '/Gadget/Subscribe_Tmpl.php';

		// Custom user-defined template
		$html_current = $page->theme_dir . '/' . $page->theme_color . '/html/' . $addonFolderName . '/' . $tmpl;
		if (file_exists($html_current)) {
			include($html_current);
			return;
		}

		// Default hard-coded template
		$tmpl = $addonPathCode.$tmpl;
		include($tmpl);

	}

}
