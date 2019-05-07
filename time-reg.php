<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<?php
 include ("ConnIMESys.php");
 //include ("ConnTE.php");
 $connectAMS = connectimesys('mcdr-skywise');

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
	$qryFH = "SELECT DATE_FORMAT(aircraftflightlog.blockOnDate,'%x-%v') AS `week`, SEC_TO_TIME(SUM(TIME_TO_SEC(aircraftflightlog.airtime))) AS jam, Count(`aircraftflightlog`.`landing`) AS cycle, aircraftflightlog.acReg FROM aircraftflightlog WHERE DATE_FORMAT(aircraftflightlog.blockOnDate,'%x-%v') <=  '".$time."' AND aircraftflightlog.voidStatus =  'false' AND (aircraftflightlog.revenue =  'Revenue' OR aircraftflightlog.revenue =  'Revenue.Revenue') AND aircraftflightlog.acReg IN  ('".$actype."') GROUP BY DATE_FORMAT(`aircraftflightlog`.`blockOnDate`,'%x-%v') ORDER BY DATE_FORMAT(`aircraftflightlog`.`blockOnDate`,'%x-%v') DESC LIMIT 12";
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
	$qryFC = "SELECT DATE_FORMAT(aircraftflightlog.blockOnDate,'%x-%v') AS `week`, SEC_TO_TIME(SUM(TIME_TO_SEC(aircraftflightlog.airtime))) AS jam, Count(`aircraftflightlog`.`landing`) AS cycle, aircraftflightlog.acReg FROM aircraftflightlog WHERE DATE_FORMAT(aircraftflightlog.blockOnDate,'%x-%v') <=  '".$time."' AND aircraftflightlog.voidStatus =  'false' AND (aircraftflightlog.revenue =  aircraftflightlog.revenue OR aircraftflightlog.revenue =  'Revenue.Revenue') AND aircraftflightlog.acReg IN  ('".$actype."') GROUP BY DATE_FORMAT(`aircraftflightlog`.`blockOnDate`,'%x-%v') ORDER BY DATE_FORMAT(`aircraftflightlog`.`blockOnDate`,'%x-%v') DESC LIMIT 12	";
	$resultFC = mysqli_query( $connectAMS,$qryFC);
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
	$qrydelay = "SELECT DATE_FORMAT(`mcdrnew`.`DateEvent`,'%x-%v') AS `week`, `mcdrnew`.`Reg`, Count(`mcdrnew`.`DCP`) AS `Jumlah` FROM `mcdrnew` WHERE (DATE_FORMAT(`mcdrnew`.`DateEvent`,'%x-%v') <= '".$time."') AND `mcdrnew`.`DCP`= 'D' AND `mcdrnew`.`Reg` = '".$actype."' Group by DATE_FORMAT(`mcdrnew`.`DateEvent`,'%x-%v') ORDER BY DATE_FORMAT(`mcdrnew`.`DateEvent`,'%x-%v') DESC";
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
	$qrycancel = "SELECT DATE_FORMAT(`mcdrnew`.`DateEvent`,'%x-%v') AS `week`, `mcdrnew`.`Reg`, Count(`mcdrnew`.`DCP`) AS `Jumlah` FROM `mcdrnew` WHERE (DATE_FORMAT(`mcdrnew`.`DateEvent`,'%x-%v') <= '".$time."') AND `mcdrnew`.`DCP`= 'C' AND `mcdrnew`.`Reg` = '".$actype."' Group by DATE_FORMAT(`mcdrnew`.`DateEvent`,'%x-%v') ORDER BY DATE_FORMAT(`mcdrnew`.`DateEvent`,'%x-%v') DESC";
	$resultcancel = mysqli_query($qrycancel);//die($qrycancel);
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
	$qrystation = "SELECT DATE_FORMAT(`mcdrnew`.`DateEvent`,'%x-%v') AS `week`, `mcdrnew`.`REG`, Count(`mcdrnew`.`DCP`) AS `Jumlah` FROM `mcdrnew` WHERE (DATE_FORMAT(`mcdrnew`.`DateEvent`,'%x-%v') <= '".$time."') AND (`mcdrnew`.`DCP`= 'C' OR `mcdrnew`.`DCP`= 'D') AND `mcdrnew`.`DepSta` = 'SUB' AND `mcdrnew`.`Reg` = '".$actype."' Group by DATE_FORMAT(`mcdrnew`.`DateEvent`,'%x-%v') ORDER BY DATE_FORMAT(`mcdrnew`.`DateEvent`,'%x-%v') DESC";
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


?>
</body>
</html>
