<?php

function connectmcdr() {
	$dbname = "mcdr";
}
function connect() {
	$dbname = "Imesys";
}
$conn_aircraftflightlog = connectmcdr();
while ($hasillagi = mysql_fetch_array($hasilid)){
mysql_free_result($hasilid);

if (mssql_num_rows($result) == 0){
}
mssql_close($conn_aircraftflightlog);
while ($hasilFH = mssql_fetch_array($result)){
}
print_r($qry);

mssql_free_result($result);
?>