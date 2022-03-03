<?php
/**
 * Helper class for Data Ticker module
 */


class modDataTickerHelper
{
    function getDataTicker( $params ) {
		$dataticker = new dataTicker;
		return $dataticker->makeDataTicker();
		//return 'Data Ticker';
    }
}

/*
	 $report_data array
    [report_name] => Storage Audit
    [initial_data_set_gb] => 1024
    [initial_unique_data_perc] => 95
    [annual_growth_rate_perc] => 25
    [inactive_data_perc] => 90
    [retention_period_yrs] => 5
    [onhand_backups_annual] => 5
    [onhand_backups_month] => 12
    [onhand_backups_week] => 4
    [media_size_gb] => 160
    [media_rewritable] => NO
    [mysql_date] => 2012-06-16 11:01:22
    [username] => dutch
*/

class dataTicker
{
   public $mysql_table_prefix = 'chawf_';
	public $mysql_table_storageauditor = 'chawf_storageauditor_2_5_0';
	public $default_username = 'dutch';//when someone isn't logged in it grabs this report data for ticker

	function makeDataTicker() {

		$can_set_source = true;
		$user =& JFactory::getUser();
		$message = false;
		if($user->guest) {
			$username = $this->default_username;
			$can_set_source = false;
		}
		else {
			$username = $user->username;
			$datasource = JRequest::getVar('datasource', NULL, 'post');
			if(!is_null($datasource) && ($datasource > 0)) {
				$result = $this->setNewDataSourceDefault($username,$datasource);
				if($result) $message = "Success: data source changed";
				else $message = "Error: data source not changed";
			}
		}
		$row = $this->getDataSourceReportFromDb($username);
		if($row === false) {
			//then this user has no reports so use default
			$username = $this->default_username;
			$row = $this->getDataSourceReportFromDb($username);
			$can_set_source = false;
		}
		if($row === false) {
			return "Warning: No Report Data Available to Initialize Data Ticker";
			$can_set_source = false;
		}
		$report = $row;
		//make a flat array from serialized array
		$report_data = unserialize($report['report_values']);
		$report_data['mysql_date'] = $report['created'];
		$report_data['username'] = $report['username'];
		$report_data['seconds_elapsed'] = $this->secondsElapsed($report_data['mysql_date'], "mysql");

		$report_data['ratio_traditional'] = 1 + $report_data['onhand_backups_annual'] + $report_data['onhand_backups_month'] + $report_data['onhand_backups_week'];
		$report_data['ratio_us'] = 3;

		$report_data['primary_initial_data_set_bytes'] = $report_data['initial_data_set_gb'] * 1073741824;
		$report_data['primary_current_data_set_bytes'] = $this->bytesBySeconds($report_data['primary_initial_data_set_bytes'],($report_data['annual_growth_rate_perc'] / 100), $report_data['seconds_elapsed']);
		$report_data['primary_data_growth_bytes'] = $report_data['primary_current_data_set_bytes'] - $report_data['primary_initial_data_set_bytes'];
		$report_data['primary_bytes_per_second'] = $this->bytesPerSecond($report_data['primary_current_data_set_bytes'],($report_data['annual_growth_rate_perc'] / 100));

		$report_data['traditional_initial_data_set_bytes'] = $report_data['ratio_traditional'] * $report_data['primary_initial_data_set_bytes'];
		$report_data['traditional_current_data_set_bytes'] = $report_data['ratio_traditional'] * $report_data['primary_current_data_set_bytes'];
		$report_data['traditional_data_growth_bytes'] = $report_data['ratio_traditional'] * $report_data['primary_data_growth_bytes'];
		$report_data['traditional_bytes_per_second'] = $report_data['ratio_traditional'] * $report_data['primary_bytes_per_second'];

		$report_data['us_initial_data_set_bytes'] = $report_data['ratio_us'] * $report_data['primary_initial_data_set_bytes'];
		$report_data['us_current_data_set_bytes'] = $report_data['ratio_us'] * $report_data['primary_current_data_set_bytes'];
		$report_data['us_data_growth_bytes'] = $report_data['ratio_us'] * $report_data['primary_data_growth_bytes'];
		$report_data['us_bytes_per_second'] = $report_data['ratio_us'] * $report_data['primary_bytes_per_second'];

		$content = "";
		//$content = "<pre>" . print_r($report_data, true) . "</pre>";


		$content .= '
			<table style="width:320px; color:#ffffff; margin:0px; font-size:14px; font-weight:bold; line-height:1.2em; font-family:Arial,Verdana,Helvetica; background-image:url(\'/templates/gk_corporate2/images/style3/header_blue_bg.jpg\')">
				<tr>
					<td style="background:#000090; padding:8px; vertical-align:middle;">
							<span style="font-size:18px">Data Ticker</span><br />
							Xioss IQ vs. Traditional</p>
					</td>
					<td style="background:#000090; padding:0; text-align:right; vertical-align:bottom;">
						<img src="/images/graphics/speedometer84_60_transparent.png" alt="" style="margin:6px 0 0 6px" />
					</td>
				</tr>';
		if($message !== false) {
			$content .= '
				<tr>
					<td colspan="2" style="text-align:center; background:#ffffff; padding:10px; color:#ff3700;">
						' . $message . '
					</td>
				</tr>';
		}
/*
				<tr>
					<td colspan="2" style="padding:10px;">
						Storage Audit Source Data: ' . $report_data['report_name'] . '
					</td>
				</tr>
						Date Initiated: ' . date("M j, Y g:i A",$this->mysqlToUnixTime($report_data['mysql_date']))  . '<br />
						Initial Primary Data: ' . number_format($report_data['initial_data_set_gb'] / 1024,2) . ' Terabytes


				<tr>
					<td colspan="2" style="padding:10px">
						<p style="color:#ffffff">
							<sup>*</sup>The IQ, or <em>Information Quotient</em>, demonstrates how Xioss IQ Methods can cut 
							the size of the managed data set by a MAGNITUDE, saving considerable resources including 
							time, money and effort.</p>
					</td>
				</tr>
*/
		$content .= '
				<tr>
					<td colspan="2" style="padding:10px; font-size:16px; text-align:center;">Traditional Storage<br />
						<input type="text" id="a_bytes" size="20" style="background:#000000; color:#00ff00; border:1px solid #ffffff; text-align:right; display:inline;" />
					</td>
				</tr>
				<tr>
					<td colspan="2" style="padding:10px; font-size:16px; text-align:center;">Xioss IQ Storage<br />
						<input type="text" id="b_bytes" size="20" style="background:#000000; color:#00ff00; border:1px solid #ffffff; text-align:right; display:inline;" />
					</td>
				</tr>
				<tr>
					<td colspan="2" style="padding:10px; font-size:16px; text-align:center;">
						Xioss IQ/Traditional<br />
						<input type="text" id="quotient" size="8" style="background:#000000; color:#00ff00; border:1px solid #ffffff; text-align:right; display:inline;" />
					</td>
				</tr>';
		if($can_set_source) {
			$content .= '
				<tr>
					<td colspan="2" style="padding:10px; text-align:center;">
						' . $this->makeDataSourceSelector($username) . '
					</td>
				</tr>';
		}
		else {
			$content .= '
				<tr>
					<td colspan="2" style="padding:10px 20px 10px 20px; color:#ccccdd; text-align:center;">
						<em>FEATURE: When you are registered and logged in you may select the Data Ticker\'s source storage audit here.</em>
					</td>
				</tr>';
		}
		$content .= '
				<tr>
					<td colspan="2" style="padding:8px; color:#ccccdd; font-size:11px; text-align:center;">
						<em>Powered By</em> ORIGEN<sup>&#169;</sup> Data Engine
					</td>
				</tr> 
			</table>';

		$doc1 =& JFactory::getDocument();
		$doc1->addscript(JURI::root(true).'/modules/mod_dataticker/js/dataticker.js');

				$javascript = '
   window.onload = initialise;
   function initialise() {
		updateTicker('. $report_data['traditional_current_data_set_bytes'] .',' . $report_data['us_current_data_set_bytes'] . ',' . $report_data['traditional_bytes_per_second'] . ',' . $report_data['us_bytes_per_second'] . ',' . time() . ');
		setInterval(\'updateTicker('. $report_data['traditional_current_data_set_bytes'] .',' . $report_data['us_current_data_set_bytes'] . ',' . $report_data['traditional_bytes_per_second'] . ',' . $report_data['us_bytes_per_second'] . ',' . time() . ')\', 250);
   }
';
		$doc2 =& JFactory::getDocument();
		$doc2->addScriptDeclaration( $javascript );
		return $content;
    }

	public function makeDataSourceSelector($username) {
		$reports = $this->getDataSourceReportsFromDb($username);
		if(count($reports) < 2) return false;//they will not need a selector
		$form = '
			<form name="datasourceselector" action="' . $_SERVER["REQUEST_URI"] . '" method="post">
				<select name="datasource">';
		for($i=0;$i<count($reports);$i++) {
			$form .= '
					<option value="' . $reports[$i]['id'] . '">' . $reports[$i]['report_name'] . ' ' . $reports[$i]['created'] . '</option>';
		}
		$form .= '
				</select><br />
					<input type="submit" value="Set Data Source">
			</form>';
		return $form;
	}

	public function getDataSourceReportFromDb($username) {
		//query should retrieve 'Y' ticker_source if it exists by using order by asc
		$query = "select * from " . $this->mysql_table_storageauditor . "
			where username='" . $username . "' order by ticker_source asc limit 1";
		$db =& JFactory::getDBO();
		$db->setQuery($query);
		$row = $db->loadAssoc();
		if(!is_array($row)) return false;
		return $row;
	}

	public function getDataSourceReportsFromDb($username) {
		//query should retrieve 'Y' ticker_source if it exists by using order by asc
		$query = "select id,report_name,created,ticker_source from " . $this->mysql_table_storageauditor . "
			where username='" . $username . "' order by ticker_source asc";
		$db =& JFactory::getDBO();
		$db->setQuery($query);
		$rows = $db->loadAssocList();
		if(count($rows) === 0) return false;
		return $rows;
	}

	public function setNewDataSourceDefault($username,$id) {
		$query1 = "update " . $this->mysql_table_storageauditor . "
					set ticker_source='N' where username='" . $username . "'";
		$query2 = "update " . $this->mysql_table_storageauditor . "
					set ticker_source='Y' where username='" . $username . "' and id='" . $id . "' limit 1";
		$db =& JFactory::getDBO();
		$db->setQuery($query1);
		$db->query();
		$db->setQuery($query2);
		$result = $db->query();
		if($result === false) return false;
		return true;
	}

	public function dataByTime($initial_data_set, $annual_growth_rate, $years=0, $months=0, $weeks=0, $days=0, $round_precision=0) {
		$R = 1 + ($annual_growth_rate/100);
		$GB_o = $initial_data_set;
		$yrs = $years + ($months/12) + ($weeks/52) + ($days/365);
		$GB = $GB_o * pow($R, $yrs);
		return round($GB,$round_precision);
	}

	public function bytesBySeconds($initial_data_set, $annual_growth_rate, $seconds_elapsed, $round_precision=0) {
		//method returns whatever is put in (i.e. GB returns GB, KB returns KB)
		$r = $this->ratePerSecond($annual_growth_rate);
		$t = $seconds_elapsed;
		$x_bytes = $initial_data_set * exp($r * $t);
		return round($x_bytes,$round_precision);
	}

	public function bytesPerSecond($data_set, $annual_growth_rate, $round_precision=0) {
		$bps = $this->bytesBySeconds($data_set, $annual_growth_rate, 1) - $data_set;
		return round($bps,$round_precision);
	}

	public function ratePerSecond($annual_growth_rate) {
		$rps = log(1 + $annual_growth_rate) / (365 * 24 * 60 * 60);
		return $rps;
	}

	public function secondsElapsed($value, $value_type="mysql") {
		//if $value is unix time, then $value_type should = "unix"
		//if $value is mysql 0000-00-00 00:00:00, then $value_type should be "mysql"		
		$unix_now = time();
		if($value_type == "mysql") $value = $this->mysqlToUnixTime($value);
		$seconds_elapsed = $unix_now - intval($value);
		return $seconds_elapsed;
	}

	public function mysqlToUnixTime($mysqldate, $default_time_of_day=null) {
		$mysqldate = trim($mysqldate);
		if((strlen($mysqldate) == 25) && (strpos($mysqldate,"T") > 0)) {
			/* 0000-00-00T00:00:00-00:00 
				NOTE: For this format, the timezone value is ignored. To "normalize" the time, you must deal with that separately. */
			$mysqldate = str_replace("T"," ",substr($mysqldate,0,19));
		}
		elseif(strlen($mysqldate) == 10) {
			if(is_null($default_time_of_day)) $default_time_of_day = $this->default_time_of_day;
			//this is a date YYYY-MM-DD
			$mysqldate = $mysqldate . " " . $default_time_of_day;//make it middle of day, noon.
		}
		
		 $mysqldate = str_replace(array("-",":"," "),"E",$mysqldate);
		 $t = explode("E",$mysqldate);
		 $unixtime = mktime($t[3],$t[4],$t[5],$t[1],$t[2],$t[0]);
		 return $unixtime;
	}
}
?>
