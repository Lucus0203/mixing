<?php

/**

 * Project: the usualy usering data

 * Sub Project: 共通クラス

 * Author: libin

 * Date: 2008年11月03日

 * File: Data.php

 * Version: 1.0

 */



/**

 * 共通クラス

 */

class Service_Date
{

	/**
	 * Enter description here...
	 *
	 */
	function __construct(){

	}


	/**
     * プルダウン年のデータを取得する
     * @param int $year　表示される年
     * @return array $sYear 年のリストを返す
     */
	function getSelectYear($year=1980)
	{
		$mons=array();
		for($i=1955;$i<=date('Y');$i++){
			$mons[]=$i;
		}
		$sYear = array($mons,$year);

		return $sYear;
	}
	
	/**
	     * プルダウン日のデータを取得する
	     * @param int $day　表示される日
	     * @return array $sDay 日のリストを返す
	     */
	function getDay($year,$month)
	{
		$month=$month*1;
		if($month==2){
			if(($year % 4) ==0 && ($year%100!=0 || $year%400==0)){
				$sDay = array("01","02","03","04","05","06","07","08","09","10","11","12","13","14","15","16","17","18","19","20","21","22","23","24","25","26","27","28","29");
			}else{
				$sDay = array("01","02","03","04","05","06","07","08","09","10","11","12","13","14","15","16","17","18","19","20","21","22","23","24","25","26","27","28");
			}
		}else if($month ==4 || $month ==6 || $month==9 || $month ==11){
			$sDay = array("01","02","03","04","05","06","07","08","09","10","11","12","13","14","15","16","17","18","19","20","21","22","23","24","25","26","27","28","29","30");
		}else{
			$sDay = array("01","02","03","04","05","06","07","08","09","10","11","12","13","14","15","16","17","18","19","20","21","22","23","24","25","26","27","28","29","30","31");
		}
		return $sDay;
	}
	/**
     * Enter description here...
     *
     * @param unknown_type $time
     * @return unknown
     */
	function getTime($time="")
	{
		$stime = array(array("00:00","00:30","01:00","01:30","02:00","02:30","03:00","03:30","04:00","04:30","05:00","05:30","06:00","06:30",
		"07:00","07:30","08:00","08:30","09:00","09:30","10:00","10:30","11:00","11:30","12:00","12:30","13:00","13:30",
		"14:00","14:30","15:00","15:30","16:00","16:30","17:00","17:30","18:00","18:30","19:00","19:30","20:00",
		"20:30","21:00","21:30","22:00","22:30","23:00","23:30"),
		$time);

		return $stime;
	}

	/**
     * プルダウン月のデータを取得する
     * @param int $month　表示される月
     * @return array $sMonth 月のリストを返す
     */
	function getSelectMonth($month="01")
	{
		$sMonth = array(array("01","02","03","04","05","06","07","08","09","10","11","12"),
		$month);

		return $sMonth;
	}

	/**
     * プルダウン日のデータを取得する
     * @param int $day　表示される日
     * @return array $sDay 日のリストを返す
     */
	function getSelectDay($day="01")
	{
		$sDay = array(array("01","02","03","04","05","06","07","08","09","10","11","12","13","14","15","16","17","18","19","20","21","22","23","24","25","26","27","28","29","30","31"),
		$day);

		return $sDay;
	}
	
	/**
	 * the first week and last week
	 *
	 * @param unknown_type $date
	 * @return unknown
	 */
	function getFirstAndEndOfWeek($date)
	{
		$w_last=date("Y-m-d",strtotime("Sunday",strtotime($date)));
		return array(date("Y-m-d",strtotime("-6 days",strtotime($w_last))),$w_last);
	}
	//the first week and last week
	function getWeekend($date)
	{
		$sunday=date("Y-m-d",strtotime("Sunday",strtotime($date)));
		return array(date("Y-m-d",strtotime("-1 days",strtotime($sunday))),$sunday);
	}
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $date
	 * @return unknown
	 */
	function isWeekend($date){
		$weekend=$this->getWeekend($date);
		if($date==$weekend[0] || $date==$weekend[1]){
			return true;
		}else {
			return false;
		}
	}
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $date
	 * @return unknown
	 */
	function getFriday($date){
		$weekend=$this->getWeekend($date);
		$Saturday=$weekend[0];
		$friday = $this->deleteOneDay($Saturday);
		return $friday;
	}
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $date
	 * @return unknown
	 */
	function getNextMonday($date){
		$weekend=$this->getWeekend($date);
		$sunday=$weekend[1];
		$monday = $this->addOneDay($sunday);
		return $monday;
	}
	/**
	 * add one day
	 *
	 * @param unknown_type $date
	 * @return unknown
	 */
	function addOneDay($date){
		$now_date=date("Y-m-d",strtotime($date));
		$next_date=date("Y-m-d",strtotime("+1day",strtotime($now_date)));
		return $next_date;
	}
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $startdate
	 * @param unknown_type $enddate
	 */
	function getBetweenDays($startdate,$enddate){
		$i=0;

		if(date("Y-m-d",strtotime($enddate)) <= date("Y-m-d",strtotime($startdate))){
			return 0;
		}else {

			while (date("Y-m-d",strtotime($startdate)) <= date("Y-m-d",strtotime($enddate))){
				$i++;
				$startdate=$this->addOneDay($startdate);
			}
			return $i-1;
		}
	}
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $startdate
	 * @param unknown_type $enddate
	 * @return unknown
	 */
	function getRestDays($startdate,$enddate){
		$task = & new Task();
		$num =0;
		while (date("Y-m-d",strtotime($startdate)) <= date("Y-m-d",strtotime($enddate))){
			$fla1= $this->isWeekend($startdate);
			$fla2 = $task->isHoliday($startdate);
			if($fla1){
				$num++;
			}else if($fla2){
				$num++;
			}
			$startdate=$this->addOneDay($startdate);
		}
		return $num;
	}
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $date
	 * @return unknown
	 */
	function addDays($date,$num){
		$now_date=date("Y-m-d",strtotime($date));
		$next_date=date("Y-m-d",strtotime($num."days",strtotime($now_date)));
		return $next_date;
	}
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $date
	 * @return unknown
	 */
	function deleteOneDay($date){
		$now_date=date("Y-m-d",strtotime($date));
		$next_date=date("Y-m-d",strtotime("-1day",strtotime($now_date)));
		return $next_date;
	}
	/**
     * add one week
     *
     * @param unknown_type $date
     * @return unknown
     */
	function addOneWeek($date){
		$now_date=date("Y-m-d",strtotime($date));
		$next_week=date("Y-m-d",strtotime("+1week",strtotime($now_date)));
		return $next_week;
	}
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $date
	 * @return unknown
	 */
	function deleteOneWeek($date){
		$now_date=date("Y-m-d",strtotime($date));
		$p_week=date("Y-m-d",strtotime("-1week",strtotime($now_date)));
		return $p_week;
	}
	/**
	 * add week
	 *
	 * @param unknown_type $num
	 * @param unknown_type $date
	 * @return unknown
	 */
	function addWeek($num,$date){
		$now_date=date("Y-m-d",strtotime($date));
		$next_week=date("Y-m-d",strtotime("+".$num."week",strtotime($now_date)));
		return $next_week;
	}
	/**
     * add one month
     *
     * @param unknown_type $date
     * @return unknown
     */
	function addOneMonth($date){
		$now_date=date("Y-m-d",strtotime($date));
		$next_week=date("Y-m-d",strtotime("+1month",strtotime($now_date)));
		return $next_week;
	}
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $num
	 * @param unknown_type $date
	 * @return unknown
	 */
	function addMonths($num,$date){
		$now_date=date("Y-m-d",strtotime($date));
		$next_week=date("Y-m-d",strtotime($num."month",strtotime($now_date)));
		return $next_week;
	}
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $num
	 * @param unknown_type $date
	 * @return unknown
	 */
	function addYears($num,$date){
		$now_date=date("Y-m-d",strtotime($date));
		$next_date=date("Y-m-d",strtotime($num."year",strtotime($now_date)));
		return $next_date;
	}
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $num
	 * @param unknown_type $date
	 * @return unknown
	 */
	function deleteMonths($num,$date){
		$now_date=date("Y-m-d",strtotime($date));
		$next_week=date("Y-m-d",strtotime("-".$num."month",strtotime($now_date)));
		return $next_week;
	}
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $date
	 * @return unknown
	 */
	function deleteOneMonth($date){
		$now_date=date("Y-m-d",strtotime($date));
		$next_week=date("Y-m-d",strtotime("-1month",strtotime($now_date)));
		return $next_week;
	}
	/**
	 * 计算日期差
	 *
	 * @param unknown_type $end_date
	 * @param unknown_type $start_date
	 * @return unknown
	 */
	function mulite_date_days($end_date,$start_date){
		$d1=strtotime($end_date);
		$d2=strtotime($start_date);
		$Days=round(($d1-$d2)/3600/24);
		return $Days;
	}
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $day
	 * @param unknown_type $date
	 * @return unknown
	 */
	function getDayFromMonth($day,$date){
		$m_first=date("Y-m-01",strtotime($date));
		$day=$day-1;
		return date("Y-m-d",strtotime("+".$day."days",strtotime($m_first)));
	}
	/**
	 * the first and last day
	 *
	 * @param unknown_type $date
	 * @return array
	 *
	 */
	function getFirstAndEndOfMonth($date)
	{
		$m_first=date("Y-m-01",strtotime($date));
		return array($m_first,date("Y-m-d",strtotime("+1 month -1day",strtotime($m_first))));
	}
	/**
     * get the week date
     *
     * @param unknown_type $week
     * @param unknown_type $date
     * @return unknown
     */
	function getWeek($week,$date){
		$sunday=date("Y-m-d",strtotime("Sunday",strtotime($date)));
		switch ($week) {
			case "日曜日":
				return date("Y-m-d",strtotime("Sunday",strtotime($date)));
				break;
			case "月曜日":
				return date("Y-m-d",strtotime("-6 days",strtotime($sunday)));
				break;
			case "火曜日":
				return date("Y-m-d",strtotime("-5 days",strtotime($sunday)));
				break;
			case "水曜日":
				return date("Y-m-d",strtotime("-4 days",strtotime($sunday)));
				break;
			case "木曜日":
				return date("Y-m-d",strtotime("-3 days",strtotime($sunday)));
				break;
			case "金曜日":
				return date("Y-m-d",strtotime("-2 days",strtotime($sunday)));
				break;
			case "土曜日":
				return date("Y-m-d",strtotime("-1 days",strtotime($sunday)));
				break;
			case "天":
				return date("Y-m-d",strtotime("Sunday",strtotime($date)));
				break;
			case "一":
				return date("Y-m-d",strtotime("-6 days",strtotime($sunday)));
				break;
			case "二":
				return date("Y-m-d",strtotime("-5 days",strtotime($sunday)));
				break;
			case "三":
				return date("Y-m-d",strtotime("-4 days",strtotime($sunday)));
				break;
			case "四":
				return date("Y-m-d",strtotime("-3 days",strtotime($sunday)));
				break;
			case "五":
				return date("Y-m-d",strtotime("-2 days",strtotime($sunday)));
				break;
			case "六":
				return date("Y-m-d",strtotime("-1 days",strtotime($sunday)));
				break;
			case "日":
				return date("Y-m-d",strtotime("Sunday",strtotime($date)));
				break;
			case "月":
				return date("Y-m-d",strtotime("-6 days",strtotime($sunday)));
				break;
			case "火":
				return date("Y-m-d",strtotime("-5 days",strtotime($sunday)));
				break;
			case "水":
				return date("Y-m-d",strtotime("-4 days",strtotime($sunday)));
				break;
			case "木":
				return date("Y-m-d",strtotime("-3 days",strtotime($sunday)));
				break;
			case "金":
				return date("Y-m-d",strtotime("-2 days",strtotime($sunday)));
				break;
			case "土":
				return date("Y-m-d",strtotime("-1 days",strtotime($sunday)));
				break;
			default:
				return date("Y-m-d",strtotime("-6 days",strtotime($sunday)));
				break;
		}

	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $date
	 * @return unknown
	 */
	function getWeekByDate($date){

		$s_7=date("Y-m-d",strtotime("Sunday",strtotime($date)));
		$s_6=$this->deleteOneDay($s_7);
		$s_5=$this->deleteOneDay($s_6);
		$s_4=$this->deleteOneDay($s_5);
		$s_3=$this->deleteOneDay($s_4);
		$s_2=$this->deleteOneDay($s_3);
		$s_1=$this->deleteOneDay($s_2);

		$date = str_replace('/','-',$date);
		switch ($date) {
			case $s_7:

				return _g("seven");
				break;
			case $s_6:

				return _g("six");
				break;
			case $s_5:

				return _g("five");
				break;
			case $s_4:
				return _g("four");

				break;
			case $s_3:
				return _g("third");

				break;
			case $s_2:
				return _g("second");

				break;
			case $s_1:
				return _g("one");

				break;
			default:
				break;
		}
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $date
	 * @return unknown
	 */
	function getWeekByDateNoLan($date){

		$s_7=date("Y-m-d",strtotime("Sunday",strtotime($date)));
		$s_6=$this->deleteOneDay($s_7);
		$s_5=$this->deleteOneDay($s_6);
		$s_4=$this->deleteOneDay($s_5);
		$s_3=$this->deleteOneDay($s_4);
		$s_2=$this->deleteOneDay($s_3);
		$s_1=$this->deleteOneDay($s_2);

		$date = str_replace('/','-',$date);
		switch ($date) {
			case $s_7:

				return "seven";
				break;
			case $s_6:

				return "six";
				break;
			case $s_5:

				return "five";
				break;
			case $s_4:
				return "four";

				break;
			case $s_3:
				return "third";

				break;
			case $s_2:
				return "second";

				break;
			case $s_1:
				return "one";

				break;
			default:
				break;
		}
	}
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $date
	 * @return unknown
	 */
	function getOrderWeekOfMonth($date){
		$i=1;
		$first_date = $this->getFirstAndEndOfMonth($date);
		$first_week=$this->getFirstAndEndOfWeek($first_date[0]);
		$sunday=$first_week[1];
		while (date("Y-m-d",strtotime($sunday)) < date("Y-m-d",strtotime($date))) {
			$i++;
			$sunday=$this->addOneWeek($sunday);
		}
		return $i;
	}


}



?>