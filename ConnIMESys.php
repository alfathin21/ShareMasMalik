<?php
	//include("adodb/adodb.inc.php");
	//$imesys = NewADOConnection('mysql');
	//$imesys->Connect("192.168.40.118", "develop", "aeroasia", "imesys");
	
	function connectimesys($database)
	{
		$server = '172.16.40.202';
		$username = 'ter1';
		$password = 'reliability';
	
		//$database = 'imesys';

		return mysqli_connect($server, $username, $password,$database);
		
	}
	
	function connectimesysTE()
	{
		$server = '172.16.40.202';
		$username = 'ter1';
		$password = 'reliability';
	
		$database = 'mcdr-skywise';

		return mysqli_connect($server,$username,$password,$database);
	}
	function connectimesysRPTDB()
	{
		$server = 'RPT-DB-01';
		$username = 'usr-te-imesys';
		$password = 'gmfmr0';
	
		$database = 'db_Imesys';

		$sqlconnect = sqlsrv_connect($server, $username, $password, $database);
		
		return $sqlconnect;
	}
?>
