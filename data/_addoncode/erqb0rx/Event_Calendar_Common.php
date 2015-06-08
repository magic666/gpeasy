<?php

defined('is_running') or die('Not an entry point...');

function strcontains($haystack, $needle)
{  
  if (strpos($haystack, $needle)!== false)  
    return true;  
  else  
    return false;  
}

function date_compare($a, $b)
{
  if ($a['0'] == $b['0']) {
    return 0;
  }

  return ($a['0'] < $b['0']) ? -1 : 1;
}

function Calendar_Get_Array()
{
	global $addonPathData;
	
	$datefile = '/date_data';
	
	$datearray = array();

	if(file_exists($addonPathData.$datefile))
	{
		$datesdata = file_get_contents($addonPathData.$datefile);
		
		$zeilen = explode("\n", $datesdata);
		
		foreach($zeilen as $zeile)
		{
			$dateentry = explode('|', $zeile, 4);
			if(sizeof($dateentry) >= 3 && preg_match('/^\d{2}-\d{2}-\d{4}/', $dateentry[0]) == 1 && preg_match('/^\d{2}-\d{2}-\d{4}/', $dateentry[1]) == 1)
			{
				try
				{	if(sizeof($dateentry) == 3)
					{
						$datearray[] = array(new DateTime($dateentry[0]), new DateTime($dateentry[1]), $dateentry[2], '');
					}
					else
					{
						$datearray[] = array(new DateTime($dateentry[0]), new DateTime($dateentry[1]), $dateentry[2], $dateentry[3]);
					}
				}
				catch (Exception $e)
				{
				}
			}
		}
	}
	
	// Sort array
	if(sizeof($datearray) > 1)
	{
		usort($datearray, 'date_compare');
	}
	return $datearray;
}
?>