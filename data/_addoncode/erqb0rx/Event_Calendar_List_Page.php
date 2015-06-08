<?php

defined('is_running') or die('Not an entry point...');

class Event_Calendar_List_Page
{
	function Event_Calendar_List_Page()
	{
		global $addonPathCode, $addonPathData;
		
		include_once $addonPathCode.'/Event_Calendar_Common.php';
		
		$configfile = '/config_data';
		$event_settings = array();
		if(file_exists($addonPathData.$configfile))
		{
			require($addonPathData.$configfile);
		}
		
		if($event_settings['show_event_list_page_tile'] == true)
		{
			echo '<div><h2 style="text-align: center;"><u>'.$event_settings['event_list_page_tile'].'</u></h2></div>';
		}
		
		echo '<div align="center"><p><table cellpadding="5" cellspacing="5" border="0" style="width:100%"><tbody>';
		// Termine eintragen
		$eventdata = Calendar_Get_Array();
		$firstrun = true;

		foreach($eventdata as $singleevent)
		{
			$actual_date = getdate();
			if($singleevent[1] >= new DateTime($actual_date['mday'].'-'.$actual_date['mon'].'-'.$actual_date['year']))
			{
				echo '<tr>';
				echo '<td style="text-align: left; vertical-align: middle; width:30%; height:28px">';
				if($firstrun == true)
				{
					echo '<b>';
				}
				if($singleevent[0] == $singleevent[1])
				{
					echo $singleevent[0]->format('d.m.Y');
				}
				else 
				{
					echo $singleevent[0]->format('d.m.Y').' - '.$singleevent[1]->format('d.m.Y');
				}
				if($firstrun == true)
				{
					echo '</b>';
				}
				echo '</td>';
				
				echo '<td style="text-align: left; vertical-align: middle; width:70%; height:28px">';
				if($firstrun == true)
				{
					echo '<b>';
				}
				if(strlen($singleevent[3]) > 8)
				{
					echo '<a href="'.$singleevent[3].'" ';
					if (strcontains($singleevent[3], 'http'))
					{
						echo 'target="_blank"';
					}
					echo '>';
				}
				echo $singleevent[2];
				if(strlen($singleevent[3]) > 8)
				{
					echo '</a>';
				}
				if($firstrun == true)
				{
					echo '</b>';
					$firstrun = false;
				}
				echo '</td>';
				echo '</tr>';
			}
		}
		echo '</tbody></table></p></div>';
	}
}
?>