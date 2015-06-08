<?php
    class calendarlib
    {
        private $language = 0;
        private $link     = 1;
        private $mydate   = NULL;
        private $tag,$monat,$jahr;
        private $getVariable = 'mydate';
        

        private $arr_monate = array(
            array ('Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember'),
            array ('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'),
            array ('Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre')
        );
        
        private $arr_Tage   = array(
            array ('Mo','Di','Mi','Do','Fr','Sa','So'),
            array ('Mon','Tue','Wed','Thu','Fri','Sat','Sun'),
            array ('Lu','Ma','Me','Je','Ve','Sa','Di')
        );

        private $events = array();

                
        private function isEvent($date)
        {
            return array_key_exists($date, $this->events);
        }
        
        private function getspezialLink($date,$isNavlnk=false)
        {
           
            if ($this->isEvent($date) && !$isNavlnk)
            {
                    return ($this->events[$date]['link'] != '') ? $this->events[$date]['link'].'?'.$this->getVariable.'='.$date : '?'.$this->getVariable.'='.$date;
            }
            
            return '?'.$this->getVariable.'='.$date;
           
        }
        
        private function getspezialTitle($date, $isNavlnk=false)
        {
           
            if ($this->isEvent($date) && !$isNavlnk)
            {
            	return $this->events[$date]['title'];
            }
            
            return '';
           
        }
        
        private function check_date($date,$format,$sep){
            $pos1    = strpos($format, 'd');
            $pos2    = strpos($format, 'm');
            $pos3    = strpos($format, 'Y'); 
            $check    = explode($sep,$date);
            return checkdate($check[$pos2],$check[$pos1],$check[$pos3]);
        }
        
        private function datetest(){
            $arr_mydate = explode('-', $this->mydate);
            if($arr_mydate[0]>=1 && $arr_mydate[0]<=31 &&
               $arr_mydate[1]>0 && $arr_mydate[1]<13 && 
               $arr_mydate[2] > 1970 && $arr_mydate[2]< 2038){
                $this->tag   = $arr_mydate[0];
                $this->monat = $arr_mydate[1];
                $this->jahr  = $arr_mydate[2];    
            }
            
            //Defaultwerte, wenn kein Monat/Jahr Übergeben wurde
            $this->tag   = (isset($this->tag)!==true) ? date("d",time()) : $this->monat;    
            $this->monat = (isset($this->monat)!==true) ? date("n",time()) : $this->monat;
            $this->jahr  = (isset($this->jahr)!==true)  ? date("Y",time()) : $this->jahr;
        }        
        
        public function __construct(){
        }        
        
        public function addEvent($tdate, $title, $speziallnk=NULL){
            if ($this->check_date($tdate,"dmY","-"))
            {
              $tmp = explode('-', $tdate);

              $tdate  = (($tmp[0][0]=='0') ? $tmp[0][1] : $tmp[0]).
                        '-'.(($tmp[1][0]=='0') ? $tmp[1][1] : $tmp[1]).
                        '-'.$tmp[2];
              
              if($this->isEvent($tdate))
              {
	              $this->events[$tdate]['title'] .= '&#10;'.$title;
              }
              else
              {
	              $this->events[$tdate]['title'] = $title;
              }
              $this->events[$tdate]['link'] = $speziallnk;
              return true;
            }
            else
            {
              return false;
            }
        }
        
        public function setDate($caldate){
            $this->mydate = $caldate;
        }
        
        public function setDateWithGET(){
            if(isset($_GET[$this->getVariable]))
                $this->mydate = $_GET[$this->getVariable];
        }        
        
        public function setLinkTyp($lnk=1){
            $this->link = $lnk;
        }        
        
        public function setGETVariableName($newName){
          $this->getVariable = $newName;
        }
        
        
        public function setLanguage($lang)
        {
            if($lang>=0 && $lang<count($this->arr_monate))
            {
                $this->language = $lang;
        	}
        	else 
        	{
	        	$this->language = 1;
        	}
        }
        
        public function showCAL($imagePath){
	        ob_start();
            $this->datetest();
            
            $actual_date = getdate();
            
            global $addonPathData;
            
            $configfile = '/config_data';
			$event_settings = array();
			if(file_exists($addonPathData.$configfile))
			{
				require($addonPathData.$configfile);
			}
        
            $iWochenTag  = date("w", mktime(0, 0, 0, $this->monat, 1, $this->jahr));
            $iAnzahltage = date("t", mktime(0, 0, 0, $this->monat, 1, $this->jahr));
            $iZeilen = ($iWochenTag==1 && $iAnzahltage==28) ? 4 : (($iAnzahltage == 31 && ($iWochenTag == 6 || $iWochenTag == 0))|| ($iWochenTag  == 0 && $iAnzahltage == 30)) ? 6 : 5; 

            //Nächster Monat
            if($this->monat==12){
                $nmonat=1;
                $njahr=$this->jahr+1;
            } else {
                $nmonat=$this->monat+1;
                $njahr=$this->jahr;  
            }

            //Vorheriger Monat    
            if($this->monat==1){
                $vmonat=12;
                $vjahr=$this->jahr-1;
            } else {
                $vmonat=$this->monat-1;
                $vjahr=$this->jahr;  
            }

            $iAnzahltageVormonat = date("t", mktime(0, 0, 0, $vmonat, 1, $vjahr));
            
            echo '<div align="center"><table id="cal"><tr>',
                  '<th>',
                  (($this->link==1 || $this->link==2 || $this->link==4) ? '<a href="'.$this->getspezialLink('1-'.$vmonat.'-'.$vjahr,true).'" name="Event_Calendar_Gadget_Nav"><img src="'.$imagePath.'/img/arrowp.gif" width="9" height="11" alt="&gt;"></a>' : '&nbsp;'),
				  '</th>',
  				  '<th colspan="5">',htmlentities($this->arr_monate[$this->language][$this->monat-1]),' ',$this->jahr,'</th>',
  				  '<th >',
  				  (($this->link==1 || $this->link==2 || $this->link==4) ? '<a href="'.$this->getspezialLink('1-'.$nmonat.'-'.$njahr,true).'" name="Event_Calendar_Gadget_Nav"><img src="'.$imagePath.'/img/arrown.gif" width="9" height="11"></a>': '&nbsp;'),
				  '</th>',
                  
                  
                  '</tr>
                    <tr>
                      <th >', htmlentities($this->arr_Tage[$this->language][0]) ,'</th>
                      <th >', htmlentities($this->arr_Tage[$this->language][1]) ,'</th>
                      <th >', htmlentities($this->arr_Tage[$this->language][2]) ,'</th>
                      <th >', htmlentities($this->arr_Tage[$this->language][3]) ,'</th>
                      <th >', htmlentities($this->arr_Tage[$this->language][4]) ,'</th>
                      <th >', htmlentities($this->arr_Tage[$this->language][5]) ,'</th>
                      <th >', htmlentities($this->arr_Tage[$this->language][6]) ,'</th>
                    </tr>';
            
            
            
            $iTag = 0; //Tag im Monat
            $i=0;
			$ntmp=0;
            do{ // while($i < $iZeilen);
                echo '<tr>';
            
                $j=1;
                do { //while($j <= 7);
            
                    //Hilfsvariable Mo=1, Di=2 .... So=7
                    $m = ($iWochenTag==0) ? 7 :  $iWochenTag;
            
                    //Nicht jeder Monat beginnt am Monat
                    if($m == $j && $j <= 7 && $iTag == 0){
                        $iTag = 1;
                    }
                    
                    
                    $preTag = ($iAnzahltageVormonat+$j-$m+1);
                    
                    if ($iTag == 0){
                      $tmpEvent =$this->isEvent($preTag.'-'.$vmonat.'-'.$vjahr);
                      echo '<td ';
                      echo $tmpEvent ? 'id="aevent">' : 'id="amonat">';
                      echo ($this->link==1 || ($tmpEvent==true && ($this->link==3 || $this->link==4))) ? '<a href="'.$this->getspezialLink($preTag.'-'.$vmonat.'-'.$vjahr).'" title="'.$this->getspezialTitle($preTag.'-'.$vmonat.'-'.$vjahr).'">' : '';
                      echo $preTag;
                      echo ($this->link==1 || ($tmpEvent==true && ($this->link==3 || $this->link==4))) ? '</a>' : '';
                    }
                    
                    if ($iTag > $iAnzahltage){
                      ++$ntmp;
                      $tmpEvent = $this->isEvent($ntmp.'-'.$nmonat.'-'.$njahr);
                      echo '<td ';
                      echo $tmpEvent ? 'id="aevent">' : 'id="amonat">';
                      echo ($this->link==1 || ($tmpEvent==true && ($this->link==3 || $this->link==4))) ? '<a href="'.$this->getspezialLink($ntmp.'-'.$nmonat.'-'.$njahr).'" title="'.$this->getspezialTitle($ntmp.'-'.$nmonat.'-'.$njahr).'">' : '';
                      echo $ntmp;
                      echo ($this->link==1 || ($tmpEvent==true && ($this->link==3 || $this->link==4))) ? '</a>' : '';
                      echo '</td>';
                    }
                    
                    if ($iTag != 0 && $iTag <= $iAnzahltage){
                      $tmpEvent = $this->isEvent($iTag.'-'.$this->monat.'-'.$this->jahr);
                      echo '<td ';
					  // Hier das Highlight fuer den aktuellen Tag
					  if($event_settings['highlight_today'] == true && $iTag == $actual_date['mday'] && $this->monat == $actual_date['mon'] && $this->jahr == $actual_date['year'])
                      {
	                      echo 'style="border-width:1px; border-style:solid; border-color:Crimson;"';
                      }
                      echo $tmpEvent ? 'id="monatevent">' : 'id="monat">';
                      echo ($this->link==1 || ($tmpEvent==true && ($this->link==3 || $this->link==4))) ? '<a href="'.$this->getspezialLink($iTag.'-'.$this->monat.'-'.$this->jahr).'" title="'.$this->getspezialTitle($iTag.'-'.$this->monat.'-'.$this->jahr).'">' : '';
                      echo $iTag;
                      echo ($this->link==1 || ($tmpEvent==true && ($this->link==3 || $this->link==4))) ? '</a>' : '';
                      echo '</td>';
                      ++$iTag;
                    }

                    
                } while(++$j <= 7);
            
                echo '</tr>';
            
            } while(++$i < $iZeilen);
            echo '</table></div>';
            $myStr = ob_get_contents();
			ob_end_clean();
			return $myStr;
        }
    } 
?>
