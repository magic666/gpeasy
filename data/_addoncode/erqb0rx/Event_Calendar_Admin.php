<?php

defined('is_running') or die('Not an entry point...');

class Event_Calendar_Admin
{

	function Event_Calendar_Admin()
	{
		global $langmessage, $addonPathData, $addonPathCode;
		
		include_once $addonPathCode.'/Event_Calendar_Lib.php';
		include_once $addonPathCode.'/Event_Calendar_Common.php';

		$datefile = '/date_data';
		$configfile = '/config_data';
		
		$datesdata = '';
		$event_settings = array();

		if (isset($_POST['sendbutton'])) //settings
		{
			if (is_numeric($_POST['language']))
			{
				$event_settings['language'] = (int)$_POST['language'];
			}
			
			$event_settings['event_list_page_tile'] = (string)$_POST['event_list_page_tile'];
			
			if (isset($_POST['show_event_list_page_tile']))
			{
				$event_settings['show_event_list_page_tile'] = true;
			}
			else
			{
				$event_settings['show_event_list_page_tile'] = false;
			}
			
			$event_settings['event_yearly_view_page_tile'] = (string)$_POST['event_yearly_view_page_tile'];
			
			if (isset($_POST['show_event_yearly_view_page_tile']))
			{
				$event_settings['show_event_yearly_view_page_tile'] = true;
			}
			else
			{
				$event_settings['show_event_yearly_view_page_tile'] = false;
			}
			
			if (isset($_POST['highlight_today']))
			{
				$event_settings['highlight_today'] = true;
			}
			else
			{
				$event_settings['highlight_today'] = false;
			}
			
			if (isset($_POST['show_gadget_header']))
			{
				$event_settings['show_gadget_header'] = true;
			}
			else
			{
				$event_settings['show_gadget_header'] = false;
			}
			
			if (isset($_POST['show_gadget_footer']))
			{
				$event_settings['show_gadget_footer'] = true;
			}
			else
			{
				$event_settings['show_gadget_footer'] = false;
			}
			
			gpFiles::SaveArray($addonPathData.$configfile, 'event_settings', $event_settings);
			
			if (isset($_POST['dates']))
			{
				$datesdata = (string)$_POST['dates'];
				gpFiles::Save($addonPathData.$datefile, $datesdata);
			}
			message($langmessage['SAVED']);
		}
		
		if( file_exists($addonPathData.$configfile) )
		{
			require($addonPathData.$configfile);
		}

		if( file_exists($addonPathData.$datefile) )
		{
			$datesdata = file_get_contents($addonPathData.$datefile);
		}
		
		echo '<form name="date_input" action="'.common::GetUrl('Admin_Event_Calendar').'" method="post">';
		
		echo '<table style="width:100%" class="bordered">';
		
		echo '<tr>';
			echo '<th>';
			echo 'Option';
			echo '</th>';
			echo '<th>';
			echo 'Value';
			echo '</th>';
		echo '</tr>';

		echo '<tr>';
			echo '<td>';
			echo 'Language';
			echo '</td>';
			echo '<td>';
			echo '<select name="language" class="gpselect">';
				if ($event_settings['language'] == 0)
				{
					echo '<option value="1">English</option>';
					echo '<option value="0" selected="selected">Deutsch</option>';
					echo '<option value="2">Francais</option>';
				}
				elseif ($event_settings['language'] == 2)
				{
					echo '<option value="1">English</option>';
					echo '<option value="0">Deutsch</option>';
					echo '<option value="2" selected="selected">Francais</option>';
				}
				else
				{
					echo '<option value="1" selected="selected">English</option>';
					echo '<option value="0">Deutsch</option>';
					echo '<option value="2">Francais</option>';
				}
			echo '</select>';
			echo '</td>';
		echo '</tr>';
		
		echo '<tr>';
			echo '<td>';
			echo 'Event List Page Title';
			echo '</td>';
			echo '<td>';
			echo '<input type="text" name="event_list_page_tile" size="40" value="'.$event_settings['event_list_page_tile'].'" class="gpinput" />';
			echo '</td>';
		echo '</tr>';
		
		echo '<tr>';
			echo '<td>';
			echo 'Show Event List Page Title';
			echo '</td>';
			echo '<td>';
			if($event_settings['show_event_list_page_tile'] == true)
			{
				echo '<input type="checkbox" name="show_event_list_page_tile" value="allow" checked="checked" />';
			}
			else
			{
				echo '<input type="checkbox" name="show_event_list_page_tile" value="allow" />';
			}
			echo '</td>';
		echo '</tr>';
		
		echo '<tr>';
			echo '<td>';
			echo 'Event Yearly View Page Title';
			echo '</td>';
			echo '<td>';
			echo '<input type="text" name="event_yearly_view_page_tile" size="40" value="'.$event_settings['event_yearly_view_page_tile'].'" class="gpinput" />';
			echo '</td>';
		echo '</tr>';
		
		echo '<tr>';
			echo '<td>';
			echo 'Show Event Yearly View Page Title';
			echo '</td>';
			echo '<td>';
			if($event_settings['show_event_yearly_view_page_tile'] == true)
			{
				echo '<input type="checkbox" name="show_event_yearly_view_page_tile" value="allow" checked="checked" />';
			}
			else
			{
				echo '<input type="checkbox" name="show_event_yearly_view_page_tile" value="allow" />';
			}
			echo '</td>';
		echo '</tr>';
		
		echo '<tr>';
			echo '<td>';
			echo 'Highlight today in Gadget';
			echo '</td>';
			echo '<td>';
			if($event_settings['highlight_today'] == true)
			{
				echo '<input type="checkbox" name="highlight_today" value="allow" checked="checked" />';
			}
			else
			{
				echo '<input type="checkbox" name="highlight_today" value="allow" />';
			}
			echo '</td>';
		echo '</tr>';
		
		echo '<tr>';
			echo '<td>';
			echo 'Show Header with Link to List View in Gadget';
			echo '</td>';
			echo '<td>';
			if($event_settings['show_gadget_header'] == true)
			{
				echo '<input type="checkbox" name="show_gadget_header" value="allow" checked="checked" />';
			}
			else
			{
				echo '<input type="checkbox" name="show_gadget_header" value="allow" />';
			}
			echo '</td>';
		echo '</tr>';
		
		echo '<tr>';
			echo '<td>';
			echo 'Show Footer with Link to Yearly View in Gadget';
			echo '</td>';
			echo '<td>';
			if($event_settings['show_gadget_footer'] == true)
			{
				echo '<input type="checkbox" name="show_gadget_footer" value="allow" checked="checked" />';
			}
			else
			{
				echo '<input type="checkbox" name="show_gadget_footer" value="allow" />';
			}
			echo '</td>';
		echo '</tr>';
		
		echo '<tr>';
			echo '<th>';
			echo 'Event Entrys';
			echo '</th>';
			echo '<th>';
			echo '';
			echo '</th>';
		echo '</tr>';
		
		echo '<tr>';
			echo '<td colspan="2">';
			echo 'Format: Beginning|End|Description|Link<br>';
			echo 'Format: DD-MM-YYYY|DD-MM-YYYY|Event Title|Event Link<br>';
			echo 'Example: 20-08-2010|22-08-2010|This is my event|http://www.google.com<br>';
			echo 'The pipe |  is the seperator, each line represents a single event.<br>';
			echo 'Both dates has to be set, for single day Events use the same day.<br>';
			echo 'The link is an optional field and has not to be set.<br>';
			echo 'A link starting with http is openend in a new window, a internal link is opened in the same window.';
			echo '</td>';
		echo '</tr>';
		
		echo '<tr>';
			echo '<td colspan="2">';
			echo '<textarea name="dates" type="text" cols="80" rows="15" class="text" wrap="virtual">';
			echo $datesdata;
			echo '</textarea>';
			echo '</td>';
		echo '</tr>';
		
		echo '</table><br>';
		
		echo '<input type="submit" name="sendbutton" value="'.$langmessage['save'].'" class="gpsubmit"/>';
		
		echo '</form>';
	}
}
?>
