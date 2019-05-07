<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<?php
error_reporting(0);
 include ("ConnIMESys.php");
 //include ("ConnTE.php");
 $connectAMS = connectimesys('mcdr-skywise');

 set_time_limit(0);
 
function getDaysInWeek($weekNumber, $year)
{
$date_object = date_create( "0 January $year" );
$date_object->modify( "+$weekNumber weeks" );
$dayTimes = $date_object->format( "Y-m-d" );
$date_object->modify( "-6 days" );
//$dayTimes = $date_object->format( "d-m-Y" )." - ".$dayTimes;
$datecriteria = "'".$date_object->format( "Y-m-d" )."' AND '".$dayTimes."'";
return $datecriteria;
}

function queryFH($time,$actype)
{
	global $connectAMS;
	if ($actype == "B737-800-E"){
		$newqry = "AND `tbl_masterac`.`ACReg` like 'PK-GE%'";
	} else if ($actype == "B737-800") {
		$newqry = "AND (`tbl_masterac`.`ACReg` like 'PK-GM%' OR `tbl_masterac`.`ACReg` like 'PK-GF%' OR `tbl_masterac`.`ACReg` like 'PK-GN%' OR `tbl_masterac`.`ACReg` like 'PK-GD%' OR `tbl_masterac`.`ACReg` like 'PK-GE%')";
	} else if ($actype == "ATR72-600-GA") {
		$newqry = "AND (`tbl_masterac`.`ACReg` like 'PK-GA%')";
	} else if ($actype == "ATR72-600-QG") {
		$newqry = "AND (`tbl_masterac`.`ACReg` like 'PK-GJ%')";			
	} else if ($actype == "B737-800-M"){
		$newqry = "AND (`tbl_masterac`.`ACReg` like 'PK-GM%' OR `tbl_masterac`.`ACReg` like 'PK-GF%' OR `tbl_masterac`.`ACReg` like 'PK-GN%' OR `tbl_masterac`.`ACReg` like 'PK-GD%')";
	} else if ($actype == "A330") {
		$newqry = "AND (`tbl_masterac`.`ACReg` like 'PK-GP%' OR `tbl_masterac`.`ACReg` like 'PK-GH%')";				
	} else if ($actype == "ALL FLEET"){
		$newqry = "";
	} else {
		$newqry = "AND `tbl_masterac`.`ACType` IN ('".$actype."')";
	}
	$qryFH = "SELECT DATE_FORMAT(aircraftflightlog.blockOnDate,'%x-%v') AS `week`, SEC_TO_TIME(SUM(TIME_TO_SEC(aircraftflightlog.airtime))) AS `jam`, Count(`aircraftflightlog`.`landing`) AS `cycle`, `tbl_masterac`.`ACType` FROM aircraftflightlog Inner Join tbl_masterac ON aircraftflightlog.acReg = tbl_masterac.ACReg  WHERE DATE_FORMAT(aircraftflightlog.blockOnDate,'%x-%v') <= '".$time."' AND `aircraftflightlog`.`voidStatus` = 'false' AND (`aircraftflightlog`.`revenue` = 'Revenue' OR `aircraftflightlog`.`revenue` = 'Revenue.Revenue') ".$newqry." GROUP BY DATE_FORMAT(`aircraftflightlog`.`blockOnDate`,'%x-%v') ORDER BY  DATE_FORMAT(`aircraftflightlog`.`blockOnDate`,'%x-%v') DESC LIMIT 12";
	$resultFH = mysqli_query($connectAMS,$qryFH);
	//die($qryFH);
	while ($hasilFH = mysqli_fetch_array($resultFH))
	{
		$hour[$hasilFH["week"]] = $hasilFH["jam"]/1;
	}
	//die(print_r($hour));
	return $hour;
}
//$connectAMSbaru = connectimesys('imesys');
function queryFC($time,$actype='')
{
	global $connectAMS;
	if ($actype == "B737-800-E"){
		$newqry = "AND `tbl_masterac`.`ACReg` like 'PK-GE%'";
	} else if ($actype == "B737-800"){
		$newqry = "AND (`tbl_masterac`.`ACReg` like 'PK-GM%' OR `tbl_masterac`.`ACReg` like 'PK-GF%' OR `tbl_masterac`.`ACReg` like 'PK-GN%' OR `tbl_masterac`.`ACReg` like 'PK-GD%' OR `tbl_masterac`.`ACReg` like 'PK-GE%')";
	} else if ($actype == "ATR72-600-GA") {
		$newqry = "AND (`tbl_masterac`.`ACReg` like 'PK-GA%')";
	} else if ($actype == "ATR72-600-QG") {
		$newqry = "AND (`tbl_masterac`.`ACReg` like 'PK-GJ%')";			
	} else if ($actype == "B737-800-M"){
		$newqry = "AND (`tbl_masterac`.`ACReg` like 'PK-GM%' OR `tbl_masterac`.`ACReg` like 'PK-GF%' OR `tbl_masterac`.`ACReg` like 'PK-GN%' OR `tbl_masterac`.`ACReg` like 'PK-GD%')";
	} else if ($actype == "A330") {
		$newqry = "AND (`tbl_masterac`.`ACReg` like 'PK-GP%' OR `tbl_masterac`.`ACReg` like 'PK-GH%')";					
	} else if ($actype == "ALL FLEET"){
		$newqry = "";
	} else {
		$newqry = "AND `tbl_masterac`.`ACType` IN ('".$actype."')";
	}
	$qryFC = "SELECT DATE_FORMAT(aircraftflightlog.blockOnDate,'%x-%v') AS `week`, SEC_TO_TIME(SUM(TIME_TO_SEC(aircraftflightlog.airtime))) AS `jam`, Count(`aircraftflightlog`.`landing`) AS `cycle`, `tbl_masterac`.`ACType` FROM aircraftflightlog Inner Join tbl_masterac ON aircraftflightlog.acReg = tbl_masterac.ACReg  WHERE DATE_FORMAT(aircraftflightlog.blockOnDate,'%x-%v') <= '".$time."' AND `aircraftflightlog`.`voidStatus` = 'false' AND (`aircraftflightlog`.`revenue` = 'Revenue' OR `aircraftflightlog`.`revenue` = 'Revenue.Revenue') ".$newqry." GROUP BY DATE_FORMAT(`aircraftflightlog`.`blockOnDate`,'%x-%v') ORDER BY  DATE_FORMAT(`aircraftflightlog`.`blockOnDate`,'%x-%v') DESC LIMIT 12";
	$resultFC = mysqli_query($connectAMS,$qryFC);
	//die($qryFC);
	while ($hasilFC = mysqli_fetch_array($resultFC))
	{
		$cycle[$hasilFC["week"]] = $hasilFC["cycle"];
	}
	return $cycle;
}



function querydelay($time,$actype)
{
	global $connectAMS;
	if ($actype == "B737-800-E"){
		$newqry = "AND `mcdrnew`.`Reg` like 'PK-GE%'";
	} else if ($actype == "B737-800"){
		$newqry = "AND (`mcdrnew`.`Reg` like 'PK-GM%' OR `mcdrnew`.`Reg` like 'PK-GF%' OR `mcdrnew`.`Reg` like 'PK-GN%' OR `mcdrnew`.`Reg` like 'PK-GD%' OR `mcdrnew`.`Reg` like 'PK-GE%')";
	} else if ($actype == "ATR72-600-GA"){
		$newqry = "AND (`mcdrnew`.`Reg` like 'PK-GA%')";
	} else if ($actype == "ATR72-600-QG"){
		$newqry = "AND (`mcdrnew`.`Reg` like 'PK-GJ%')";		
	} else if ($actype == "B737-800-M"){
		$newqry = "AND (`mcdrnew`.`Reg` like 'PK-GM%' OR `mcdrnew`.`Reg` like 'PK-GF%' OR `mcdrnew`.`Reg` like 'PK-GN%' OR `mcdrnew`.`Reg` like 'PK-GD%')";
	} else if ($actype == "A330") {
		$newqry = "AND (`mcdrnew`.`Reg` like 'PK-GP%' OR `mcdrnew`.`Reg` like 'PK-GH%')";				
	} else if ($actype == "ALL FLEET"){
		$newqry = "";
	} else {
		$newqry = "AND `mcdrnew`.`ACtype` = '".$actype."'";
	}
	$qrydelay = "SELECT DATE_FORMAT(`mcdrnew`.`DateEvent`,'%x-%v') AS `week`, `mcdrnew`.`ACtype`, Count(`mcdrnew`.`DCP`) AS `Jumlah` FROM `mcdrnew` WHERE (DATE_FORMAT(`mcdrnew`.`DateEvent`,'%x-%v') <= '".$time."') AND `mcdrnew`.`DCP`= 'D' ".$newqry." Group by DATE_FORMAT(`mcdrnew`.`DateEvent`,'%x-%v') ORDER BY DATE_FORMAT(`mcdrnew`.`DateEvent`,'%x-%v') DESC";
	//die($qrydelay);
	$resultdelay = mysqli_query( $connectAMS,$qrydelay);
	while ($hasildelay = mysqli_fetch_array($resultdelay))
	{
		$delay[$hasildelay["week"]] = $hasildelay["Jumlah"];
	}
	//$delay = isset($delay) ? $delay : "0";
	return $delay;
}

function querycancel($time,$actype)
{
	global $connectAMS;
	if ($actype == "B737-800-E"){
		$newqry = "AND `mcdrnew`.`Reg` like 'PK-GE%'";
	} else if ($actype == "B737-800"){
		$newqry = "AND (`mcdrnew`.`Reg` like 'PK-GM%' OR `mcdrnew`.`Reg` like 'PK-GF%' OR `mcdrnew`.`Reg` like 'PK-GN%' OR `mcdrnew`.`Reg` like 'PK-GD%' OR `mcdrnew`.`Reg` like 'PK-GE%')";
	} else if ($actype == "ATR72-600-GA"){
		$newqry = "AND (`mcdrnew`.`Reg` like 'PK-GA%')";
	} else if ($actype == "ATR72-600-QG"){
		$newqry = "AND (`mcdrnew`.`Reg` like 'PK-GJ%')";		
	} else if ($actype == "B737-800-M"){
		$newqry = "AND (`mcdrnew`.`Reg` like 'PK-GM%' OR `mcdrnew`.`Reg` like 'PK-GF%' OR `mcdrnew`.`Reg` like 'PK-GN%' OR `mcdrnew`.`Reg` like 'PK-GD%')";
	} else if ($actype == "A330") {
		$newqry = "AND (`mcdrnew`.`Reg` like 'PK-GP%' OR `mcdrnew`.`Reg` like 'PK-GH%')";			
	} else if ($actype == "ALL FLEET"){
		$newqry = "";
	} else {
		$newqry = "AND `mcdrnew`.`ACtype` = '".$actype."'";
	}
	$qrycancel = "SELECT DATE_FORMAT(`mcdrnew`.`DateEvent`,'%x-%v') AS `week`, `mcdrnew`.`ACtype`, Count(`mcdrnew`.`DCP`) AS `Jumlah` FROM `mcdrnew` WHERE (DATE_FORMAT(`mcdrnew`.`DateEvent`,'%x-%v') <= '".$time."') AND `mcdrnew`.`DCP`= 'C' ".$newqry." Group by DATE_FORMAT(`mcdrnew`.`DateEvent`,'%x-%v') ORDER BY DATE_FORMAT(`mcdrnew`.`DateEvent`,'%x-%v') DESC";
	$resultcancel = mysqli_query($connectAMS,$qrycancel);//die($qrycancel);
	while ($hasilcancel = mysqli_fetch_array($resultcancel))
	{
		$cancel[$hasilcancel["week"]] = $hasilcancel["Jumlah"];
	}
	$cancel = isset($cancel) ? $cancel : "";
	return $cancel;
}

function querystation($time,$actype)
{
	global $connectAMS;
	if ($actype == "B737-800-E"){
		$newqry = "AND `mcdrnew`.`Reg` like 'PK-GE%'";
	} else if ($actype == "B737-800"){
		$newqry = "AND (`mcdrnew`.`Reg` like 'PK-GM%' OR `mcdrnew`.`Reg` like 'PK-GF%' OR `mcdrnew`.`Reg` like 'PK-GN%' OR `mcdrnew`.`Reg` like 'PK-GD%' OR `mcdrnew`.`Reg` like 'PK-GE%')";
	} else if ($actype == "ATR72-600-GA"){
		$newqry = "AND (`mcdrnew`.`Reg` like 'PK-GA%')";
	} else if ($actype == "ATR72-600-QG"){
		$newqry = "AND (`mcdrnew`.`Reg` like 'PK-GJ%')";		
	} else if ($actype == "B737-800-M"){
		$newqry = "AND (`mcdrnew`.`Reg` like 'PK-GM%' OR `mcdrnew`.`Reg` like 'PK-GF%' OR `mcdrnew`.`Reg` like 'PK-GN%' OR `mcdrnew`.`Reg` like 'PK-GD%')";
	} else if ($actype == "A330") {
		$newqry = "AND (`mcdrnew`.`Reg` like 'PK-GP%' OR `mcdrnew`.`Reg` like 'PK-GH%')";			
	} else if ($actype == "ALL FLEET"){
		$newqry = "";
	} else {
		$newqry = "AND `mcdrnew`.`ACtype` = '".$actype."'";
	}
	$qrystation = "SELECT DATE_FORMAT(`mcdrnew`.`DateEvent`,'%x-%v') AS `week`, `mcdrnew`.`ACtype`, Count(`mcdrnew`.`DCP`) AS `Jumlah` FROM `mcdrnew` WHERE (DATE_FORMAT(`mcdrnew`.`DateEvent`,'%x-%v') <= '".$time."') AND (`mcdrnew`.`DCP`= 'C' OR `mcdrnew`.`DCP`= 'D') AND `mcdrnew`.`DepSta` = 'CGK' ".$newqry." Group by DATE_FORMAT(`mcdrnew`.`DateEvent`,'%x-%v') ORDER BY DATE_FORMAT(`mcdrnew`.`DateEvent`,'%x-%v') DESC";
	$resultstation = mysqli_query($connectAMS,$qrystation);
	while ($hasilstation = mysqli_fetch_array($resultstation))
	{
		$station[$hasilstation["week"]] = $hasilstation["Jumlah"];
	}
	return $station;
}

function actype() {
	global $connectAMS;
	$qryactype = "SELECT DISTINCT `tbl_masterac`.`ACType` AS `ACType` FROM `tbl_masterac`";
	$resultactype = mysqli_query($connectAMS,$qryactype);
	while ($hasilactype = mysqli_fetch_array($resultactype))
	{
		$actype[] = $hasilactype[0];
	}
	return $actype;
}


function queryFHdate($startdate,$enddate,$actype)
{
	global $connectAMS;
	if ($actype == "B737-800-E"){
		$newqry = "AND `tbl_masterac`.`ACReg` like 'PK-GE%'";
	} else if ($actype == "B737-800"){
		$newqry = "AND (`tbl_masterac`.`ACReg` like 'PK-GM%' OR `tbl_masterac`.`ACReg` like 'PK-GF%' OR `tbl_masterac`.`ACReg` like 'PK-GN%' OR `tbl_masterac`.`ACReg` like 'PK-GD%' OR `tbl_masterac`.`ACReg` like 'PK-GE%')";
	} else if ($actype == "ATR72-600-GA") {
		$newqry = "AND (`tbl_masterac`.`ACReg` like 'PK-GA%')";
	} else if ($actype == "ATR72-600-QG") {
		$newqry = "AND (`tbl_masterac`.`ACReg` like 'PK-GJ%')";		
	} else if ($actype == "B737-800-M"){
		$newqry = "AND (`tbl_masterac`.`ACReg` like 'PK-GM%' OR `tbl_masterac`.`ACReg` like 'PK-GF%' OR `tbl_masterac`.`ACReg` like 'PK-GN%' OR `tbl_masterac`.`ACReg` like 'PK-GD%')";
	} else if ($actype == "A330") {
		$newqry = "AND (`tbl_masterac`.`ACReg` like 'PK-GP%' OR `tbl_masterac`.`ACReg` like 'PK-GH%')";				
	} else {
		$newqry = "AND `tbl_masterac`.`ACType` IN ('".$actype."')";
	}
	$qryFH = "SELECT SEC_TO_TIME(SUM(TIME_TO_SEC(aircraftflightlog.airtime))) AS `jam`, Count(`aircraftflightlog`.`landing`) AS `cycle`, `tbl_masterac`.`ACType` FROM aircraftflightlog Inner Join tbl_masterac ON aircraftflightlog.acReg = tbl_masterac.ACReg  WHERE (aircraftflightlog.blockOnDate BETWEEN '".$startdate."' AND '".$enddate."') AND `aircraftflightlog`.`voidStatus` = 'false' AND (`aircraftflightlog`.`revenue` = 'Revenue' OR `aircraftflightlog`.`revenue` = 'Revenue.Revenue') ".$newqry." GROUP BY ACType";
	$resultFH = mysqli_query($connectAMS,$qryFH);
	//die($qryFH);
	while ($hasilFH = mysqli_fetch_array($resultFH))
	{
		$hour[] = $hasilFH["jam"]/1;
	}
	//die(print_r($hour));
	return $hour;
}

function queryFCdate($startdate,$enddate,$actype)
{
	global $connectAMS;
	if ($actype == "B737-800-E"){
		$newqry = "AND `tbl_masterac`.`ACReg` like 'PK-GE%'";
	} else if ($actype == "B737-800"){
		$newqry = "AND (`tbl_masterac`.`ACReg` like 'PK-GM%' OR `tbl_masterac`.`ACReg` like 'PK-GF%' OR `tbl_masterac`.`ACReg` like 'PK-GN%' OR `tbl_masterac`.`ACReg` like 'PK-GD%' OR `tbl_masterac`.`ACReg` like 'PK-GE%')";
	} else if ($actype == "ATR72-600-GA") {
		$newqry = "AND (`tbl_masterac`.`ACReg` like 'PK-GA%')";
	} else if ($actype == "ATR72-600-QG") {
		$newqry = "AND (`tbl_masterac`.`ACReg` like 'PK-GJ%')";		
	} else if ($actype == "B737-800-M"){
		$newqry = "AND (`tbl_masterac`.`ACReg` like 'PK-GM%' OR `tbl_masterac`.`ACReg` like 'PK-GF%' OR `tbl_masterac`.`ACReg` like 'PK-GN%' OR `tbl_masterac`.`ACReg` like 'PK-GD%')";
	} else if ($actype == "A330") {
		$newqry = "AND (`tbl_masterac`.`ACReg` like 'PK-GP%' OR `tbl_masterac`.`ACReg` like 'PK-GH%')";					
	} else {
		$newqry = "AND `tbl_masterac`.`ACType` IN ('".$actype."')";
	}
	$qryFH = "SELECT SEC_TO_TIME(SUM(TIME_TO_SEC(aircraftflightlog.airtime))) AS `jam`, Count(`aircraftflightlog`.`landing`) AS `cycle`, `tbl_masterac`.`ACType` FROM aircraftflightlog Inner Join tbl_masterac ON aircraftflightlog.acReg = tbl_masterac.ACReg  WHERE (aircraftflightlog.blockOnDate BETWEEN '".$startdate."' AND '".$enddate."') AND `aircraftflightlog`.`voidStatus` = 'false' AND (`aircraftflightlog`.`revenue` = 'Revenue' OR `aircraftflightlog`.`revenue` = 'Revenue.Revenue') ".$newqry." GROUP BY ACType";
	$resultFH = mysqli_query($connectAMS,$qryFH);
	//die($qryFH);
	while ($hasilFH = mysqli_fetch_array($resultFH))
	{
		$hour[] = $hasilFH["cycle"];
	}
	//die(print_r($hour));
	return $hour;
}

function querydelaydate($startdate,$enddate,$actype)
{
	global $connectAMS;
	if ($actype == "B737-800-E"){
		$newqry = "AND `mcdrnew`.`Reg` like 'PK-GE%'";
	} else if ($actype == "B737-800"){
		$newqry = "AND (`mcdrnew`.`Reg` like 'PK-GM%' OR `mcdrnew`.`Reg` like 'PK-GF%' OR `mcdrnew`.`Reg` like 'PK-GN%' OR `mcdrnew`.`Reg` like 'PK-GD%' OR `mcdrnew`.`Reg` like 'PK-GE%')";
	} else if ($actype == "ATR72-600-GA"){
		$newqry = "AND (`mcdrnew`.`Reg` like 'PK-GA%')";
	} else if ($actype == "ATR72-600-QG"){
		$newqry = "AND (`mcdrnew`.`Reg` like 'PK-GJ%')";		
	} else if ($actype == "B737-800-M"){
		$newqry = "AND (`mcdrnew`.`Reg` like 'PK-GM%' OR `mcdrnew`.`Reg` like 'PK-GF%' OR `mcdrnew`.`Reg` like 'PK-GN%' OR `mcdrnew`.`Reg` like 'PK-GD%')";
	} else if ($actype == "A330") {
		$newqry = "AND (`mcdrnew`.`Reg` like 'PK-GP%' OR `mcdrnew`.`Reg` like 'PK-GH%')";				
	} else {
		$newqry = "AND `mcdrnew`.`ACtype` = '".$actype."'";
	}
	$qrydelay = "SELECT `mcdrnew`.`ACtype`, Count(`mcdrnew`.`DCP`) AS `Jumlah` FROM `mcdrnew` WHERE (`mcdrnew`.`DateEvent` BETWEEN '".$startdate."' AND '".$enddate."') AND `mcdrnew`.`DCP`= 'D' ".$newqry." Group by ACtype";
	//die($qrydelay);
	$resultdelay = mysqli_query($connectAMS,$qrydelay);
	while ($hasildelay = mysqli_fetch_array($resultdelay))
	{
		$delay[] = $hasildelay["Jumlah"];
	}
	//$delay = isset($delay) ? $delay : "0";
	return $delay;
}

function querycanceldate($startdate,$enddate,$actype)
{
	global $connectAMS;
	if ($actype == "B737-800-E"){
		$newqry = "AND `mcdrnew`.`Reg` like 'PK-GE%'";
	} else if ($actype == "B737-800"){
		$newqry = "AND (`mcdrnew`.`Reg` like 'PK-GM%' OR `mcdrnew`.`Reg` like 'PK-GF%' OR `mcdrnew`.`Reg` like 'PK-GN%' OR `mcdrnew`.`Reg` like 'PK-GD%' OR `mcdrnew`.`Reg` like 'PK-GE%')";
	} else if ($actype == "ATR72-600-GA"){
		$newqry = "AND (`mcdrnew`.`Reg` like 'PK-GA%')";
	} else if ($actype == "ATR72-600-QG"){
		$newqry = "AND (`mcdrnew`.`Reg` like 'PK-GJ%')";		
	} else if ($actype == "B737-800-M"){
		$newqry = "AND (`mcdrnew`.`Reg` like 'PK-GM%' OR `mcdrnew`.`Reg` like 'PK-GF%' OR `mcdrnew`.`Reg` like 'PK-GN%' OR `mcdrnew`.`Reg` like 'PK-GD%')";
	} else if ($actype == "A330") {
		$newqry = "AND (`mcdrnew`.`Reg` like 'PK-GP%' OR `mcdrnew`.`Reg` like 'PK-GH%')";		
	} else {
		$newqry = "AND `mcdrnew`.`ACtype` = '".$actype."'";
	}
	$qrycancel = "SELECT  `mcdrnew`.`ACtype`, Count(`mcdrnew`.`DCP`) AS `Jumlah` FROM `mcdrnew` WHERE (`mcdrnew`.`DateEvent` BETWEEN '".$startdate."' AND '".$enddate."') AND `mcdrnew`.`DCP`= 'C' ".$newqry." Group by ACtype";
	$resultcancel = mysqli_query($connectAMS,$qrycancel);//die($qrycancel);
	while ($hasilcancel = mysqli_fetch_array($resultcancel))
	{
		$cancel[$hasilcancel["week"]] = $hasilcancel["Jumlah"];
	}
	$cancel = isset($cancel) ? $cancel : "";
	return $cancel;
}

function querystationdate($startdate,$enddate,$actype)
{
	global $connectAMS;
	if ($actype == "B737-800-E"){
		$newqry = "AND `mcdrnew`.`Reg` like 'PK-GE%'";
	} else if ($actype == "B737-800"){
		$newqry = "AND (`mcdrnew`.`Reg` like 'PK-GM%' OR `mcdrnew`.`Reg` like 'PK-GF%' OR `mcdrnew`.`Reg` like 'PK-GN%' OR `mcdrnew`.`Reg` like 'PK-GD%' OR `mcdrnew`.`Reg` like 'PK-GE%')";
	} else if ($actype == "ATR72-600-GA"){
		$newqry = "AND (`mcdrnew`.`Reg` like 'PK-GA%')";
	} else if ($actype == "ATR72-600-QG"){
		$newqry = "AND (`mcdrnew`.`Reg` like 'PK-GJ%')";		
	} else if ($actype == "B737-800-M"){
		$newqry = "AND (`mcdrnew`.`Reg` like 'PK-GM%' OR `mcdrnew`.`Reg` like 'PK-GF%' OR `mcdrnew`.`Reg` like 'PK-GN%' OR `mcdrnew`.`Reg` like 'PK-GD%')";
	} else if ($actype == "A330") {
		$newqry = "AND (`mcdrnew`.`Reg` like 'PK-GP%' OR `mcdrnew`.`Reg` like 'PK-GH%')";		
	} else {
		$newqry = "AND `mcdrnew`.`ACtype` = '".$actype."'";
	}
	$qrystation = "SELECT `mcdrnew`.`ACtype`, Count(`mcdrnew`.`DCP`) AS `Jumlah` FROM `mcdrnew` WHERE (`mcdrnew`.`DateEvent` BETWEEN '".$startdate."' AND '".$enddate."') AND (`mcdrnew`.`DCP`= 'C' OR `mcdrnew`.`DCP`= 'D') AND `mcdrnew`.`DepSta` = 'CGK' ".$newqry." Group by ACtype";//die($qrystation);
	$resultstation = mysqli_query($connectAMS,$qrystation);
	while ($hasilstation = mysqli_fetch_array($resultstation))
	{
		$station[$hasilstation["week"]] = $hasilstation["Jumlah"];
	}
	return $station;
}

function queryavail($time,$actype)
{
	global $connectAMS;
	if ($actype == "B737-800-E"){
		$newqry = "AND `tbl_masterac`.`ACReg` like 'PK-GE%'";	
	} else if ($actype == "B737-800-M"){
		$newqry = "AND (`tbl_masterac`.`ACReg` like 'PK-GM%' OR `tbl_masterac`.`ACReg` like 'PK-GF%' OR `tbl_masterac`.`ACReg` like 'PK-GN%')";
	} else if ($actype == "ATR72-600-GA") {
		$newqry = "AND (`tbl_masterac`.`ACReg` like 'PK-GA%')";
	} else if ($actype == "ATR72-600-QG") {
		$newqry = "AND (`tbl_masterac`.`ACReg` like 'PK-GJ%')";		
	} else if ($actype == "A330") {
		$newqry = "AND (`tbl_masterac`.`ACReg` like 'PK-GP%' OR `tbl_masterac`.`ACReg` like 'PK-GH%')";	
	} else if ($actype == "ALL FLEET"){
		$newqry = "";
	} else {
		$newqry = "AND `tbl_masterac`.`ACType` IN ('".$actype."')";
	}
	$qryFC = "SELECT Count(distinct CONCAT(aircraftflightlog.blockOnDate,aircraftflightlog.acReg)) AS `avail`, DATE_FORMAT(`aircraftflightlog`.`blockOnDate`,'%x-%v') AS `week`, tbl_masterac.ACType FROM aircraftflightlog Inner Join tbl_masterac ON aircraftflightlog.acReg = tbl_masterac.ACReg  WHERE DATE_FORMAT(aircraftflightlog.blockOnDate,'%x-%v') <= '$time' AND `aircraftflightlog`.`voidStatus` = 'false' AND (`aircraftflightlog`.`revenue` = 'Revenue' OR `aircraftflightlog`.`revenue` = 'Revenue.Revenue') ".$newqry." GROUP BY DATE_FORMAT(`aircraftflightlog`.`blockOnDate`,'%x-%v'), tbl_masterac.ACType ORDER BY  DATE_FORMAT(`aircraftflightlog`.`blockOnDate`,'%x-%v') DESC LIMIT 12";
	$resultFC = mysqli_query($connectAMS,$qryFC);
	//die($qryFC);
	while ($hasilFC = mysqli_fetch_array($resultFC))
	{
		$cycle[$hasilFC["week"]] = round($hasilFC["avail"]/7,2);
	}
	return $cycle;
}

?>
</body>
</html>
