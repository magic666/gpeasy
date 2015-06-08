<?php

defined('is_running') or die('Not an entry point...');

class Event_Calendar_Yearly_View_Page
{
	function Event_Calendar_Yearly_View_Page()
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
		$mycal->setLinkTyp(3);
		if($event_settings['language'] != 1)
		{
			$mycal->setLanguage($event_settings['language']);
		}
		else
		{
			$mycal->setLanguage(1);
		}
		
		// Insert Events into Calendar
		$eventdata = Calendar_Get_Array();
		foreach($eventdata as $singleevent)
		{
			for ($i = 0; $singleevent[0]->format("U") + (86400 * $i) <= $singleevent[1]->format("U"); $i++)
			{
				$date_to_entry = new DateTime();
				$date_to_entry = clone $singleevent[0];
				$date_to_entry->modify('+'.$i.' day');
				$mycal->addEvent($date_to_entry->format("d-m-Y"), $singleevent[2], common::GetUrl('Event_Calendar_List_Page'));
			}
		}
		
		// Set first Date for Yearly View
		$mycal->setDateWithGET();

		// Standard request
		$page->head .= '<link rel="stylesheet" type="text/css" href="'.$addonRelativeCode.'/Event_Calendar_Style.css'.'" />';

		if (!common::LoggedIn())
		{
			$page->head_js[] = '/include/js/main.js';
		}

		echo "<div id='Event_Calendar_Yearly_View_Page'>";

		if($event_settings['show_event_yearly_view_page_tile'] == true)
		{
			echo '<div><h2 style="text-align: center;"><u>'.$event_settings['event_yearly_view_page_tile'].'</u></h2></div>';
		}

		$actual_date = getdate();
		
		for ($yearcount = 0; $yearcount <= 1; $yearcount++)
		{
			echo '<div><h2 style="text-align: center;">'.($actual_date['year'] + $yearcount).'</h2></div>';
			
			echo '<div align="center"><p><table style="width:100%;border-spacing: 10px;border:10px solid transparent;">';
			
			for ($row = 0; $row <= 3; $row++)
			{
				echo '<tr>';
				for ($column = 1; $column <= 3; $column++)
				{
					echo '<td style="text-align: center; vertical-align: top; width:33%;border-spacing: 10px;border:10px solid transparent;">';
					
					$mycal->setDate($actual_date['mday'].'-'.($column + ($row * 3)).'-'.($actual_date['year'] + $yearcount));
					
					echo $mycal->showCAL($addonRelativeCode);
					
					echo '</td>';
				}
				echo '</tr>';
			}
			echo '</table></p></div>';
		}
		
		echo "</div>";
	}
}
?>