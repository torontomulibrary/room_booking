<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Calendar {
	function drawCalendar($month = null, $year = null, $selected_day = null) {
		if($month === null || $year === null){
			$month = date('n');
			$year = date('Y');
		}
		
		
		$date = mktime(12, 0, 0, $month, 1, $year);
		
		$daysInMonth = date("t", $date);
		
		// calculate the position of the first day in the calendar (sunday = 1st column, etc)
		$offset = date("w", $date);
		$rows = 1;
		 
		$output = "";
		$output .= '<table class="table_calendar">';
		$output .= '<tr><th class="month_year" colspan="7"><a style="float: left; font-size: 25px;" href="'. base_url() .'booking?month=' . date("Ym", strtotime("-1 month", $date)) .'">&lt;</a>&nbsp;&nbsp;'. date('F Y', $date) .'&nbsp;&nbsp;<a style="float:right; font-size: 25px;" href="'. base_url() .'booking?month=' . date("Ym", strtotime("+1 month", $date)) .'">&gt;</a></th></tr>';
		$output .= "<tr><th>Su</th><th>Mo</th><th>Tu</th><th>We</th><th>Th</th><th>Fr</th><th>Sa</th></tr>";
		$output .= "<tr>";
		 
		for($i = 1; $i <= $offset; $i++){
			$output .= "<td>&nbsp;</td>";
		}
		
		
		for($day = 1; $day <= $daysInMonth; $day++){
			if( ($day + $offset - 1) % 7 == 0 && $day != 1){
				$output .= "</tr><tr>";
				$rows++;
			}

			//Format the link to the date
			$temp_date = mktime(12, 0, 0, $month, $day, $year);
			$url_date = '<a href="'. base_url() .'booking?month='.date('Ym',$date).'&date='.date('Ymd', $temp_date).'">'.$day.'</a>';
			
			//If it is the selected date
			if($selected_day !== null && $selected_day == $day){
					$output .= '<td class="selected_date">' . $url_date . '</td>';	
					continue;
			}
			
			//Check month and day to see if it is today's date!
			else if(date('Yn') === date('Yn', $date) && date('j') == $day){
					$output .= '<td class="calendar_today">' . $url_date . '</td>';

			}	
				
			
			//Check if its in the past (past month, or same month but earlier day)
			else if( date('Ym', $date) < date('Ym') || (date('Ym') == date('Ym', $date) && $day < date('j'))){
				$output .= '<td class="past_date">' . $url_date . '</td>';
			}
			
			else{
				$output .= '<td class="">' . $url_date . '</td>';
			}
		}
		while( ($day + $offset) <= $rows * 7){
			$output .= "<td>&nbsp;</td>";
			$day++;
		}
		$output .= "</tr>";
		
		$output .= "</table>";
		
		return $output;
	}
	
	function isValidDateTimeString($str_dt, $str_dateformat) {
		$date = DateTime::createFromFormat($str_dateformat, $str_dt);
		return $date && DateTime::getLastErrors()["warning_count"] == 0 && DateTime::getLastErrors()["error_count"] == 0;
	}
}

/* End of file Template.php */
/* Location: ./system/application/libraries/Template.php */