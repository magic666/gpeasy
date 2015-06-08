<?php
defined('is_running') or die('Not an entry point...');


class gpAjax{

	static function ReplaceContent($id,$content){
		gpAjax::JavascriptCall('WBx.response','replace',$id,$content);
	}

	static function JavascriptCall(){
		$args = func_get_args();
		if( !isset($args[0]) ){
			return;
		}

		echo array_shift($args);
		echo '(';
		$comma = '';
		foreach($args as $arg){
			echo $comma;
			echo gpAjax::quote($arg);
			$comma = ',';
		}
		echo ');';
	}

	static function quote(&$content){
		static $search = array('\\','"',"\n","\r",'<script','</script>');
		static $repl = array('\\\\','\"','\n','\r','<"+"script','<"+"/script>');

		return '"'.str_replace($search,$repl,$content).'"';
	}

	static function JsonEval($content){
		echo '{DO:"eval"';
		echo ',CONTENT:';
		echo gpAjax::quote($content);
		echo '},';
	}

	static function JsonDo($do,$selector,&$content){
		static $comma = '';
		echo $comma;
		echo '{DO:';
		echo gpAjax::quote($do);
		echo ',SELECTOR:';
		echo gpAjax::quote($selector);
		echo ',CONTENT:';
		echo gpAjax::quote($content);
		echo '}';
		$comma = ',';
	}


	/**
	 * Handle HTTP responses made with $_REQUEST['req'] = json (when <a ... data-cmd="gpajax">)
	 * Sends JSON object to client
	 *
	 */
	static function Response(){
		global $page;

		if( !is_array($page->ajaxReplace) ){
			die();
		}

		//gadgets may be using gpajax/json request/responses
		gpOutput::TemplateSettings();
		gpOutput::PrepGadgetContent();


		echo gpAjax::Callback($_REQUEST['jsoncallback']);
		echo '([';

		//output content
		if( !empty($_REQUEST['gpx_content']) ){
			switch($_REQUEST['gpx_content']){
				case 'gpabox':
					gpAjax::JsonDo('admin_box_data','',$page->contentBuffer);
				break;
			}
		}elseif( in_array('#gpx_content',$page->ajaxReplace) ){
			$replace_id = '#gpx_content';

			if( isset($_GET['gpreqarea']) ){
				$replace_id = '#'.$_GET['gpreqarea'];
			}

			ob_start();
			$page->GetGpxContent(true);
			$content = ob_get_clean();
			gpAjax::JsonDo('replace',$replace_id,$content);
		}

		//other areas
		foreach($page->ajaxReplace as $arguments){
			if( is_array($arguments) ){
				$arguments += array(0=>'',1=>'',2=>'');
				gpAjax::JsonDo($arguments[0],$arguments[1],$arguments[2]);
			}
		}

		//always send messages
		ob_start();
		echo GetMessages(false);
		$content = ob_get_clean();
		if( !empty($content) ){
			gpAjax::JsonDo('messages','',$content);
		}

		echo ']);';
		die();
	}



	/**
	 * Check the callback parameter, die with an alert if the test fails
	 *
	 */
	static function Callback($callback){
		if( !preg_match('#^[a-zA-Z0-9_]+$#',$callback) ){
			die('alert("Invalid Callback");');
		}
		return $callback;
	}


	/**
	 * Send a header for the javascript request
	 * Attempt to find an appropriate type within the accept header
	 *
	 */
	static function Header(){

		$accept = self::RequestHeaders('accept');
		$mime = 'application/javascript'; //default mime

		if( $accept && preg_match_all('#([^,;\s]+)\s*;?\s*([^,;\s]+)?#',$accept,$matches,PREG_SET_ORDER) ){
			$mimes = array('application/javascript','application/x-javascript','text/javascript');


			//organize by importance
			$accept = array();
			$i = 1;
			foreach($matches as $match){
				if( isset($match[2]) ){
					$accept[$match[1]] = $match[2];
				}else{
					$accept[$match[1]] = $i++;
				}
			}
			arsort($accept);

			//get matching mime
			foreach($accept as $part => $priority){
				if( in_array(trim($part),$mimes) ){
					$mime = $part;
					break;
				}
			}
		}

		//add charset
		header('Content-Type: '.$mime.'; charset=UTF-8');
	}


	/**
	 * Return a list of all headers
	 *
	 */
	static function RequestHeaders($which = false){
	    $headers = array();
	    foreach($_SERVER as $key => $value) {
	        if( substr($key, 0, 5) <> 'HTTP_' ){
	            continue;
	        }

	        $header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));

	        if( $which ){
				if( strnatcasecmp($which,$header) === 0){
					return $value;
				}
			}

	        $headers[$header] = $value;
	    }
	    if( !$which ){
			return $headers;
		}
	}

	static function InlineEdit($section_data){

		$section_data += array('type'=>'','content'=>'');


		$scripts = array();
		$scripts[] = '/include/js/inline_edit/inline_editing.js';


		$type = 'text';
		if( !empty($section_data['type']) ){
			$type = $section_data['type'];
		}
		switch($type){

			case 'gallery':
				$scripts = gpAjax::InlineEdit_Gallery($scripts);
			break;

			case 'include':
				$scripts = gpAjax::InlineEdit_Include($scripts);
			break;

			case 'text';
				$scripts = gpAjax::InlineEdit_Text($scripts);
			break;

			case 'image';
				echo 'var gp_blank_img = '.gpAjax::quote(common::GetDir('/include/imgs/blank.gif')).';';

				$scripts[] = '/include/js/jquery.auto_upload.js';
				$scripts[] = '/include/js/inline_edit/image_common.js';
				$scripts[] = '/include/js/inline_edit/image_edit.js';
			break;
		}

		$scripts = gpPlugin::Filter('InlineEdit_Scripts',array($scripts,$type));

		self::SendScripts($scripts);


		//replace resized images with their originals
		if( isset($section_data['resized_imgs']) && is_array($section_data['resized_imgs']) && count($section_data['resized_imgs']) ){
			includeFile('tool/editing.php');
			$section_data['content'] = gp_edit::RestoreImages($section_data['content'],$section_data['resized_imgs']);
		}

		//create the section object that will be passed to gp_init_inline_edit
		$section_object = common::JsonEncode($section_data);


		//send call to gp_init_inline_edit()
		echo ';if( typeof(gp_init_inline_edit) == "function" ){';
		echo 'gp_init_inline_edit(';
		echo gpAjax::quote($_GET['area_id']);
		echo ','.$section_object;
		echo ');';
		echo '}else{alert("gp_init_inline_edit() is not defined");}';
	}

	/**
	 * Send content of all files in the $scripts array to the client
	 *
	 */
	static function SendScripts($scripts){
		global $dataDir, $dirPrefix;

		self::Header();
		Header('Vary: Accept,Accept-Encoding');// for proxies


		$scripts = array_unique($scripts);

		//send all scripts
		foreach($scripts as $script){

			//absolute paths don't need $dataDir
			$full_path = $script;
			if( strpos($script,$dataDir) !== 0 ){

				//fix addon paths that use $addonRelativeCode
				if( !empty($dirPrefix) && strpos($script,$dirPrefix) === 0 ){
					$script = substr($script,strlen($dirPrefix));
				}
				$full_path = $dataDir.$script;
			}

			if( !file_exists($full_path) ){
				if( common::LoggedIn() ){
					$msg = 'Admin Notice: The following file could not be found: \n\n'.$full_path;
					echo 'if(isadmin){alert('.json_encode($msg).');}';
				}
				continue;
			}

			echo ';';
			//echo "\n/**\n* $script\n*\n*/\n";
			readfile($full_path);
		}
	}

	static function InlineEdit_Text($scripts){
		includeFile('tool/editing.php');

		// autocomplete
		echo gp_edit::AutoCompleteValues(true);


		// ckeditor basepath and configuration
		$options = array(
						'extraPlugins' => 'sharedspace',
						'sharedSpaces' => array( 'top' => 'ckeditor_top', 'bottom' =>' ckeditor_bottom' )
						);

		$ckeditor_basepath = common::GetDir('/include/thirdparty/ckeditor_34/');
		echo 'CKEDITOR_BASEPATH = '.gpAjax::quote($ckeditor_basepath).';';
		echo 'var gp_ckconfig = '.gp_edit::CKConfig( $options, 'json', $plugins ).';';


		// extra plugins
		echo 'var gp_add_plugins = '.json_encode( $plugins ).';';


		// scripts
		$scripts[] = '/include/thirdparty/ckeditor_34/ckeditor.js';
		$scripts[] = '/include/js/ckeditor_config.js';
		$scripts[] = '/include/js/inline_edit/inlineck.js';

		return $scripts;
	}

	static function InlineEdit_Include($scripts){
		$scripts[] = '/include/js/inline_edit/include_edit.js';
		return $scripts;
	}

	static function InlineEdit_Gallery($scripts){
		$scripts[] = '/include/js/jquery.auto_upload.js';
		$scripts[] = '/include/js/inline_edit/image_common.js';
		$scripts[] = '/include/js/inline_edit/gallery_edit_202.js';
		return $scripts;
	}

}
