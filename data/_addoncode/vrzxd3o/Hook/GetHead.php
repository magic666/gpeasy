<?php defined('is_running') or die('Not an entry point...');

class EasyNewsLetter_GetHead {

	public function __construct(){
		global $addonPathData, $addonFolderName, $page;

		// Custom css file
		$file = $addonPathData.'/EasyNewsLetter.css';

		if (file_exists($file)) {
			$page->css_user[] = '/data/_addondata/'.$addonFolderName.'/EasyNewsLetter.css';
		} 

		// Custom js file
		$file = $addonPathData.'/EasyNewsLetter.js';

		if (file_exists($file)) {
			$page->head_js[] = '/data/_addondata/'.$addonFolderName.'/EasyNewsLetter.js';
		} 

	}

}


