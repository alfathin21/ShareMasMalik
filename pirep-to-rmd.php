<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Pilot And Delay Report</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style type="text/css">
<!--
.style1 {font-size: 20px}
.style2 {font-size: 24px}
.style3 {font-size: 12px}
-->
</style>
<?php
error_reporting(0);
  include("function.php");
  $periode = isset($_REQUEST["periode"]) ? $_REQUEST["periode"] : "";
  //$year = isset($_REQUEST["year"]) ? $_REQUEST["year"] : "";
  $actype = isset($_REQUEST["actype"]) ? $_REQUEST["actype"] : "";

  $TE = connectimesysTE();
  $arractype = selectactype();
  $arrperiode = selectperiode();

?>

</head>

<body>
<script language="javascript">
function display_report() {

        if (document.frm1.actype.value == "") {
                alert('Please Insert ACType');
        } else if (document.frm1.periode.value == "") {
                alert('Please Select Periode');
        } else {
                location.href = "<?= $_SERVER['PHP_SELF']?>?actype=" +
                        escape(document.frm1.actype.value) + "&periode=" +
                        escape(document.frm1.periode.value);
        }
  }

function export_excel()
  {
        if (document.frm1.periode.value == 0) {
                alert('Please Select Periode');
        } else if (document.frm1.actype.value == 0) {
                alert('Please Select AC Type');
        } else {
				
        	   var str = "Pilot_excel.php?actype=" + escape(document.frm1.actype.value) + "&periode=" + escape(document.frm1.periode.value);
                window.open(str);
        }
  }

function export_pdf()
  {
        if (document.frm1.periode.value == 0) {
                alert('Please Select Periode');
        } else if (document.frm1.actype.value == 0) {
                alert('Please Select AC Type');
        } else {
				
        	   var str = "pirepPDF.php?actype=" + escape(document.frm1.actype.value) + "&periode=" + escape(document.frm1.periode.value);
                window.open(str);
        }
  }
   function display_detail(kriteria,ATA, periode, title)
  {
        var filter = document.frm1.filter.value;
		window.open("pilot_report_detail.php?filter=" + escape(filter) +
                        "&periode=" + escape(periode) + "&ATA=" + escape(ATA) + "&kriteria=" + escape(kriteria),'','status,resizable,scrollbars');
  }
</script>
<table width="100%"  border="0">
  <tr>
    <td>&nbsp;</td>
    <td><div align="center" class="style1">Reliability Report </div></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td><div align="center">Pilot Report </div></td>
    <td>&nbsp;</td>
  </tr>
</table>
<form name="frm1" method="GET">
<p>Periode (yyyy-mm) &nbsp&nbsp&nbsp
  <?php
cmbbox("periode",$arrperiode,$periode,'',1);
?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
Aircraft Type &nbsp&nbsp&nbsp
 <?php  
cmbbox("actype",$arractype,$actype,'',1);
?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
 <input type="submit" name="Submit" value="Display Report" onClick="display_report()">&nbsp;&nbsp;
 <input type="submit" name="Submit2" value="Export Excel" onClick="export_excel()">
 <input type="submit" name="Submit2" value="Export PDF" onClick="export_pdf()">
 <input type="submit" name="insert" value="input" onClick="display_report()">
</p>
</form>
<?php 
	
	//die(print_r(ATA("2008-01","2008-03","B737-500")));
	$tahun = substr($periode,0,4);
	$bulanalert = substr($periode,-2,2);
	if ($bulanalert <= 06){
		//$tahun = $tahun - 1;
		$timeawal = ($tahun-1)."-01";
		$timeakhir = ($tahun-1)."-12";
	} else {
		$timeawal = ($tahun - 1)."-07";
		$timeakhir = $tahun."-06";
	}
	
			if ($periode == "" OR $actype == ""){
			die("Please Select Aircraft Type And Periode");
			}
	$TE = connectimesysTE();
	//die($periode);
	//echo $actype."\n";
	//echo $periode."\n";
	$FH = queryFH($periode,$actype);//
	$bulan = array_keys($FH);
	
	$FH12bulan = queryFH12(($tahun-1)."-12",$actype,12);
	$FH12 = array_sum($FH12bulan);
	$keys12bulan = array_keys($FH12bulan);
	$jmlhblnyglalu = count($keys12bulan);
	$keys12bulan[11] = $keys12bulan[$jmlhblnyglalu-1];
	//die(print_r($keys12bulan[11]));

	$FC = queryFC12rev($periode,$actype);
	$FC12 = array_sum(queryFC12rev(($tahun-1)."-12",$actype,12));//

	$ATAdelay = ATAdelay($bulan[2],$bulan[0],$actype);
	$ATAdelay12bulan = ATAdelay12($keys12bulan[11],$keys12bulan[0],$actype);//die(print_r($ATAdelay12bulan));
	$alertleveldelay = alertdelay($timeawal,$timeakhir,$actype);
	
	foreach ($ATAdelay as $k => $v){
		$newATAdelay[$k]['OT'] = 0;
		foreach ($v as $key => $value){
			if ($key < '21'){
				$newATAdelay[$k]['OT'] += $value;
			} else {
				$newATAdelay[$k][$key] = $value;
			}
		}
	}
	$newATAdelay12bulan['OT'] = 0;
	foreach ($ATAdelay12bulan as $k => $v){
		if ($k < '21'){
			$newATAdelay12bulan['OT'] += $v;
		} else {
			$newATAdelay12bulan[$k] = $v;
		}
	}
	//die(print_r($newATAdelay12bulan));

	$ATA12bulan = ATA12($keys12bulan[11],$keys12bulan[0],$actype);//die(print_r($ATA12bulan));
	
	$ATApirep = ATA($bulan[2],$bulan[0],$actype);//
		
	
	$alertlevel = alert($timeawal,$timeakhir,$actype);//die(print_r($alertlevel));

	$connectAMS = connectimesys('mcdr-skywise');
	$ATA = ATAdef();
	$ATAdesc = ATAdesc();
	//die(print_r($newATAdelay12bulan));	
	//$ATAforDelay = ATAdefdelay();
	//$ATAdescdelay = ATAdescdelay();die(print_r($ATAforDelay));
	///bagiannya pembuatan delay
	
	
	

	
	//die(print_r($alertleveldelay));	
			//die(print_r(ATAdelay($periode2,$actype)));
?> 
<div align="center" class="style2">
  <p>Pilot Report</p>
</div>
<table width="100%"  border="1">
  <tr>
    <th colspan="2" scope="col"><span class="style3">Total Flight Hours  </span></th>
    <th scope="col"><div align="center" class="style3"><?=  $FH[$bulan[2]]?></div></th>
    <th scope="col"><div align="center" class="style3"><?=  $FH[$bulan[1]]?></div></th>
    <th scope="col"><div align="center" class="style3"><?=  $FH[$bulan[0]]?></div></th>
    <th scope="col"><div align="center" class="style3"><?=  $FH[$bulan[2]] + $FH[$bulan[1]] + $FH[$bulan[0]]?></div></th>
    <th scope="col"><div align="center" class="style3"><?=  $FH12?></div></th>
    <th scope="col"><span class="style3"></span></th>
    <th scope="col"><span class="style3"></span></th>
    <th scope="col"><span class="style3"></span></th>
    <th scope="col"><span class="style3"></span></th>
    <th scope="col"><span class="style3"></span></th>
    <th scope="col"><span class="style3"></span></th>
    <th scope="col"><span class="style3"></span></th>
    <th scope="col"><span class="style3"></span></th>
  </tr>
  <tr>
    <th colspan="2" scope="row"><span class="style3">ATA Chapter</span></th>
    <th><div align="center" class="style3"><?=  bulan($bulan[2])?></div></th>
    <th><div align="center" class="style3"><?=  bulan($bulan[1])?></div></th>
    <th><div align="center" class="style3"><?=  bulan($bulan[0])?></div></th>
    <th><div align="center" class="style3">Last 3 Mo </div></th>
    <th><div align="center" class="style3">Last 12 Mo</div></th>
    <th><div align="center" class="style3"><?=  bulan($bulan[2])?></div></th>
    <th><div align="center" class="style3"><?=  bulan($bulan[1])?></div></th>
    <th><div align="center" class="style3"><?=  bulan($bulan[0])?></div></th>
    <th><div align="center" class="style3">3 Month </div></th>
    <th><div align="center" class="style3">12 Month </div></th>
    <th rowspan="2"><div align="center" class="style3">Alert Level </div></th>
    <th rowspan="2"><div align="center" class="style3">Alert Status </div></th>
    <th rowspan="2"><div align="center" class="style3">Trend</div></th>
  </tr>
  <tr>
    <th scope="row"><span class="style3"></span></th>
    <th><span class="style3"></span></th>
    <th><span class="style3"></span></th>
    <th><span class="style3"></span></th>
    <th><span class="style3"></span></th>
    <th><span class="style3"></span></th>
    <th><div align="center" class="style3"><?=  $tahun-1?></div></th>
    <th><div align="center" class="style3">Rate</div></th>
    <th><div align="center" class="style3">Rate</div></th>
    <th><div align="center" class="style3">Rate</div></th>
    <th><div align="center" class="style3">Rate</div></th>
    <th><div align="center" class="style3">Rate</div></th>
  </tr>
  <?php
  $kriteria = "";
  

  for ($i=0;$i<35;$i++)
  {
	  $ATApirep[$bulan[2]][$ATA[$i]] = isset($ATApirep[$bulan[2]][$ATA[$i]]) ? $ATApirep[$bulan[2]][$ATA[$i]] : 0;
	  $ATApirep[$bulan[1]][$ATA[$i]] = isset($ATApirep[$bulan[1]][$ATA[$i]]) ? $ATApirep[$bulan[1]][$ATA[$i]] : 0;
	  $ATApirep[$bulan[0]][$ATA[$i]] = isset($ATApirep[$bulan[0]][$ATA[$i]]) ? $ATApirep[$bulan[0]][$ATA[$i]] : 0;
	  $ATA12bulan[$ATA[$i]] = isset($ATA12bulan[$ATA[$i]]) ? $ATA12bulan[$ATA[$i]] : 0;
	  $alertlevel[$ATA[$i]] = isset($alertlevel[$ATA[$i]]) ? $alertlevel[$ATA[$i]] : 0;
	  $alertleveldelay[$ATA[$i]] = isset($alertleveldelay[$ATA[$i]]) ? $alertleveldelay[$ATA[$i]] : 0;
  ?>
  <tr>
    <th scope="row"><div align="center" class="style3"><?=  $ATA[$i]?></div></th>
    <th><div align="center" class="style3"><?=  $ATAdesc[$i]?> </div></th>
    <td><div align="center" class="style3"><?=  link_detail("pirep",$ATApirep[$bulan[2]][$ATA[$i]],$ATA[$i],$bulan[2],$actype)?></div></td>
    <td><div align="center" class="style3"><?=  link_detail("pirep",$ATApirep[$bulan[1]][$ATA[$i]],$ATA[$i],$bulan[1],$actype)?></div></td>
    <td><div align="center" class="style3"><?=  link_detail("pirep",$ATApirep[$bulan[0]][$ATA[$i]],$ATA[$i],$bulan[0],$actype)?></div></td>
    <td><div align="center" class="style3"><?=  $ATApirep[$bulan[0]][$ATA[$i]] + $ATApirep[$bulan[1]][$ATA[$i]] + $ATApirep[$bulan[2]][$ATA[$i]]?></div></td>
    <td><div align="center" class="style3"><?=  $ATA12bulan[$ATA[$i]]?></div></td>
    <td><div align="center" class="style3"><?=  round($ATApirep[$bulan[2]][$ATA[$i]]/$FH[$bulan[2]]*1000,2)?></div></td>
    <td><div align="center" class="style3"><?=  round($ATApirep[$bulan[1]][$ATA[$i]]/$FH[$bulan[1]]*1000,2)?></div></td>
    <td><div align="center" class="style3"><?=  round($ATApirep[$bulan[0]][$ATA[$i]]/$FH[$bulan[0]]*1000,2)?></div></td>
    <td><div align="center" class="style3"><?=  round(($ATApirep[$bulan[2]][$ATA[$i]] + $ATApirep[$bulan[1]][$ATA[$i]] + $ATApirep[$bulan[0]][$ATA[$i]])/($FH[$bulan[2]] + $FH[$bulan[1]] + $FH[$bulan[0]])*1000,2)?></div></td>
    <td><div align="center" class="style3"><?=  round($ATA12bulan[$ATA[$i]]/$FH12*1000,2)?></div></td>
    <td><div align="center" class="style3"><?=  round($alertlevel[$ATA[$i]],2)?></div></td>
    <td><div align="center" class="style3"><?=  alertstatus(round($ATApirep[$bulan[2]][$ATA[$i]]/$FH[$bulan[2]]*1000,2),round($ATApirep[$bulan[1]][$ATA[$i]]/$FH[$bulan[1]]*1000,2),round($ATApirep[$bulan[0]][$ATA[$i]]/$FH[$bulan[0]]*1000,2),$alertlevel[$ATA[$i]])?></div></td>
    <td><div align="center" class="style3"><?=  alerttrend($ATApirep[$bulan[2]][$ATA[$i]]/$FH[$bulan[2]]*1000,$ATApirep[$bulan[1]][$ATA[$i]]/$FH[$bulan[1]]*1000,$ATApirep[$bulan[0]][$ATA[$i]]/$FH[$bulan[0]]*1000)?></div></td>
  </tr>
  <?php
  }
	  ?>
</table>
<p align="center" class="style2">Delay Report</p>
<table width="100%" border="1">
  <tr>
    <th colspan="2" scope="col"><span class="style3">Total Flight Cycle </span></th>
    <th scope="col"><span class="style3">
      <?=  $FC[$bulan[2]]?>
    </span></th>
    <th scope="col"><span class="style3">
      <?=  $FC[$bulan[1]]?>
    </span></th>
    <th scope="col"><span class="style3">
      <?=  $FC[$bulan[0]]?>
    </span></th>
    <th scope="col"><span class="style3">
      <?=  $FC[$bulan[2]] + $FC[$bulan[1]] + $FC[$bulan[0]]?>
    </span> </th>
    <th scope="col"><span class="style3">
      <?=  $FC12?>
    </span></th>
    <th scope="col">&nbsp;</th>
    <th scope="col">&nbsp;</th>
    <th scope="col">&nbsp;</th>
    <th scope="col">&nbsp;</th>
    <th scope="col">&nbsp;</th>
    <th scope="col">&nbsp;</th>
    <th scope="col">&nbsp;</th>
    <th scope="col">&nbsp;</th>
  </tr>
  <tr>
    <th colspan="2" scope="row"><span class="style3">ATA Chapter </span></th>
    <th><div align="center" class="style3"><?=  bulan($bulan[2])?></div></th>
    <th><div align="center" class="style3"><?=  bulan($bulan[1])?></div></th>
    <th><div align="center" class="style3"><?=  bulan($bulan[0])?></div></th>
    <th><div align="center" class="style3">Last 3 Mo</div></th>
    <th><div align="center" class="style3">Last 12 Mo</div></th>
    <th><div align="center" class="style3"><?=  bulan($bulan[2])?></div></th>
    <th><div align="center" class="style3"><?=  bulan($bulan[1])?></div></th>
    <th><div align="center" class="style3"><?=  bulan($bulan[0])?></div></th>
    <th><div align="center" class="style3">3 Month</div></th>
    <th><div align="center" class="style3">12 Month</div></th>
    <th rowspan="2"><div align="center" class="style3">Alert Level</div> </th>
    <th rowspan="2"><div align="center" class="style3">Alert Status</div> </th>
    <th rowspan="2"><div align="center" class="style3">Trend</div></th>
  </tr>
  <tr>
    <th scope="row">&nbsp;</th>
    <th>&nbsp;</th>
    <th>&nbsp;</th>
    <th>&nbsp;</th>
    <th>&nbsp;</th>
    <th>&nbsp;</th>
    <th><div align="center" class="style3"><?=  $tahun-1?></div></th>
    <th><div align="center" class="style3">Rate</div></th>
    <th><div align="center" class="style3">Rate</div></th>
    <th><div align="center" class="style3">Rate</div></th>
    <th><div align="center" class="style3">Rate</div></th>
    <th><div align="center" class="style3">Rate</div></th>
  </tr>
  <?php
  //$rel = connectimesys('reliability');
  //$ATA = ATAdef();
  //$ATAdesc = ATAdesc();
	
  for ($i=0;$i<35;$i++)
  {
	  $newATAdelay[$bulan[2]][$ATA[$i]] = isset($newATAdelay[$bulan[2]][$ATA[$i]]) ? $newATAdelay[$bulan[2]][$ATA[$i]] : 0;
	  $newATAdelay[$bulan[1]][$ATA[$i]] = isset($newATAdelay[$bulan[1]][$ATA[$i]]) ? $newATAdelay[$bulan[1]][$ATA[$i]] : 0;
	  $newATAdelay[$bulan[0]][$ATA[$i]] = isset($newATAdelay[$bulan[0]][$ATA[$i]]) ? $newATAdelay[$bulan[0]][$ATA[$i]] : 0;
	  $newATAdelay12bulan[$ATA[$i]] = isset($newATAdelay12bulan[$ATA[$i]]) ? $newATAdelay12bulan[$ATA[$i]] : 0;
  ?>
  <tr>
    <th scope="row"><div align="center" class="style3"><?=  $ATA[$i]?></div></th>
    <th><div align="center" class="style3"><?=  $ATAdesc[$i]?></div></th>
    <td><div align="center" class="style3"><?=  link_detail("delay",$newATAdelay[$bulan[2]][$ATA[$i]],$ATA[$i],$bulan[2],$actype)?></div></td>
    <td><div align="center" class="style3"><?=  link_detail("delay",$newATAdelay[$bulan[1]][$ATA[$i]],$ATA[$i],$bulan[1],$actype)?></div></td>
    <td><div align="center" class="style3"><?=  link_detail("delay",$newATAdelay[$bulan[0]][$ATA[$i]],$ATA[$i],$bulan[0],$actype)?></div></td>
    <td><div align="center" class="style3"><?=  $newATAdelay[$bulan[2]][$ATA[$i]] + $newATAdelay[$bulan[1]][$ATA[$i]] + $newATAdelay[$bulan[0]][$ATA[$i]]?></div></td>
    <td><div align="center" class="style3"><?=  $newATAdelay12bulan[$ATA[$i]]?></div></td>
    <td><div align="center" class="style3"><?=  round($newATAdelay[$bulan[2]][$ATA[$i]]/$FC[$bulan[2]]*100,2)?></div></td>
    <td><div align="center" class="style3"><?=  round($newATAdelay[$bulan[1]][$ATA[$i]]/$FC[$bulan[1]]*100,2)?></div></td>
    <td><div align="center" class="style3"><?=  round($newATAdelay[$bulan[0]][$ATA[$i]]/$FC[$bulan[0]]*100,2)?></div></td>
    <td><div align="center" class="style3"><?=  round(($newATAdelay[$bulan[2]][$ATA[$i]] + $newATAdelay[$bulan[1]][$ATA[$i]] + $newATAdelay[$bulan[0]][$ATA[$i]])/($FC[$bulan[2]] + $FC[$bulan[1]] + $FC[$bulan[0]])*100,2)?></div></td>
    <td><div align="center" class="style3"><?=  round($newATAdelay12bulan[$ATA[$i]]/$FC12*100,2)?></div></td>
    <td><div align="center" class="style3"><?=  round($alertleveldelay[$ATA[$i]],2)?></div></td>
    <td><div align="center" class="style3"><?=  alertstatus(round($newATAdelay[$bulan[2]][$ATA[$i]]/$FC[$bulan[2]]*100,2),round($newATAdelay[$bulan[1]][$ATA[$i]]/$FC[$bulan[1]]*100,2),round($newATAdelay[$bulan[0]][$ATA[$i]]/$FC[$bulan[0]]*100,2),$alertleveldelay[$ATA[$i]])?></div></td>
    <td><div align="center" class="style3"><?=  alerttrend($newATAdelay[$bulan[2]][$ATA[$i]]/$FC[$bulan[2]]*1000,$newATAdelay[$bulan[1]][$ATA[$i]]/$FC[$bulan[1]]*1000,$newATAdelay[$bulan[0]][$ATA[$i]]/$FC[$bulan[0]]*1000)?></div></td>
  </tr>
  <?php
	}
 ?>
 <tr>
 <?php
	$newATAdelay[$bulan[2]]['OT'] = isset($newATAdelay[$bulan[2]]['OT']) ? $newATAdelay[$bulan[2]]['OT'] : 0;
	$newATAdelay[$bulan[1]]['OT'] = isset($newATAdelay[$bulan[1]]['OT']) ? $newATAdelay[$bulan[1]]['OT'] : 0;
	$newATAdelay[$bulan[0]]['OT'] = isset($newATAdelay[$bulan[0]]['OT']) ? $newATAdelay[$bulan[0]]['OT'] : 0;
	$newATAdelay12bulan['OT'] = isset($newATAdelay12bulan['OT']) ? $newATAdelay12bulan['OT'] : 0;
 ?>
    <th scope="row"><div align="center" class="style3">OT</div></th>
    <th><div align="center" class="style3">OTHER</div></th>
    <td><div align="center" class="style3"><?=  link_detail("delay",$newATAdelay[$bulan[2]]['OT'],'OT',$bulan[2],$actype)?></div></td>
    <td><div align="center" class="style3"><?=  link_detail("delay",$newATAdelay[$bulan[1]]['OT'],'OT',$bulan[2],$actype)?></div></td>
    <td><div align="center" class="style3"><?=  link_detail("delay",$newATAdelay[$bulan[0]]['OT'],'OT',$bulan[2],$actype)?></div></td>
    <td><div align="center" class="style3"><?=  $newATAdelay[$bulan[2]]['OT'] + $newATAdelay[$bulan[1]]['OT'] + $newATAdelay[$bulan[0]]['OT']?></div></td>
    <td><div align="center" class="style3"><?=  $newATAdelay12bulan['OT']?></div></td>
    <td><div align="center" class="style3"><?=  round($newATAdelay[$bulan[2]]['OT']/$FC[$bulan[2]]*100,2)?></div></td>
    <td><div align="center" class="style3"><?=  round($newATAdelay[$bulan[1]]['OT']/$FC[$bulan[1]]*100,2)?></div></td>
    <td><div align="center" class="style3"><?=  round($newATAdelay[$bulan[0]]['OT']/$FC[$bulan[0]]*100,2)?></div></td>
    <td><div align="center" class="style3"><?=  round(($newATAdelay[$bulan[2]]['OT'] + $newATAdelay[$bulan[1]]['OT'] + $newATAdelay[$bulan[0]]['OT'])/($FC[$bulan[2]] + $FC[$bulan[1]] + $FC[$bulan[0]])*100,2)?></div></td>
    <td><div align="center" class="style3"><?=  round($newATAdelay12bulan['OT']/$FC12*100,2)?></div></td>
    <td><div align="center" class="style3"><?=  round($alertleveldelay['5'],2)?></div></td>
    <td><div align="center" class="style3"><?=  alertstatus(round($newATAdelay[$bulan[2]]['OT']/$FC[$bulan[2]]*100,2),round($newATAdelay[$bulan[1]]['OT']/$FC[$bulan[1]]*100,2),round($newATAdelay[$bulan[0]]['OT']/$FC[$bulan[0]]*100,2),$alertleveldelay['5'])?></div></td>
    <td><div align="center" class="style3"><?=  alerttrend($newATAdelay[$bulan[2]]['OT']/$FC[$bulan[2]]*1000,$newATAdelay[$bulan[1]]['OT']/$FC[$bulan[1]]*1000,$newATAdelay[$bulan[0]]['OT']/$FC[$bulan[0]]*1000)?></div></td>
  </tr>
</table>
<p>&nbsp;  </p>
</body>
</html>
