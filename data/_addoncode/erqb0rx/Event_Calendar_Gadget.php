<?php

defined('is_running') or die('Not an entry point...');

class Event_Calendar_Gadget
{
	function Event_Calendar_Gadget()
	{
		global $page, $addonFolderName, $addonRelativeCode;

		ob_start();
		$this->_Event_Calendar_Gadget();
		$html = ob_get_contents();
		ob_end_clean();

		// Ajax request
		if (isset($_REQUEST['gpreq']) && ($_REQUEST['gpreq'] == 'json'))
		{
			$page->ajaxReplace[] = array('inner','#Event_Calendar_Gadget',$html);
			return;
		}

		// Standard request
		$page->head .= '<link rel="stylesheet" type="text/css" href="'.$addonRelativeCode.'/Event_Calendar_Style.css'.'" />';

		if (!common::LoggedIn())
		{
			$page->head_js[] = '/include/js/main.js';
		}

		$page->head_js[] = $addonRelativeCode.'/Event_Calendar_Ajax.js';

		echo "<div id='Event_Calendar_Gadget'>";
		echo $html;
		echo "</div>";
	}

	function _Event_Calendar_Gadget()
	{
		global $page, $addonPathCode, $addonRelativeCode, $addonPathData;

		include_once $addonPathCode.'/Event_Calendar_Lib.php';
		include_once $addonPathCode.'/Event_Calendar_Common.php';

		$configfile = '/config_data';
		$event_settings = array();
		if(file_exists($addonPathData.$configfile))
		{
			require($addonPathData.$configfile);
		}

		$mycal = new calendarlib();
		$mycal->setDateWithGET();
		$mycal->setLinkTyp(4);
		if ($event_settings['language'] != 1)
		{
			$mycal->setLanguage($event_settings['language']);
		}
		else
		{
			$mycal->setLanguage(1);
		}

		// Termine eintragen
		$eventdata = Calendar_Get_Array();
		foreach ($eventdata as $singleevent)
		{
			for ($i = 0; $singleevent[0]->format("U") + (86400 * $i) <= $singleevent[1]->format("U"); $i++)
			{
				$date_to_entry = new DateTime();
				$date_to_entry = clone $singleevent[0];
				$date_to_entry->modify('+'.$i.' day');
				$mycal->addEvent($date_to_entry->format("d-m-Y"), $singleevent[2], common::GetUrl('Event_Calendar_List_Page'));
			}
		}

		// Header for Gadget
		$headertext = '';
		if ($event_settings['show_gadget_header'] == true)
		{
			$headertext = '<h4><div align="center">'.common::Link_Page('Event_Calendar_List_Page').'</div></h4>';
		}
		
		$rueckgabe = $mycal->showCAL($addonRelativeCode);

		// Footer for Gadget
		$footertext = '';
		if ($event_settings['show_gadget_footer'] == true)
		{
			$footertext = '<h4><div align="center">'.common::Link_Page('Event_Calendar_Yearly_View_Page').'</div></h4>';
		}
		
		printf(gpOutput::SelectText('%s %s %s'), $headertext, $rueckgabe, $footertext);
	}
}
?>