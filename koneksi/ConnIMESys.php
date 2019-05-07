<?php
	//include("adodb/adodb.inc.php");
	//$imesys = NewADOConnection('mysql');
	//$imesys->Connect("192.168.40.118", "develop", "aeroasia", "imesys");
	
	function connectimesys($database)
	{
		$server = '192.168.40.118';
		$username = 'develop';
		$password = 'aeroasia';
	
		//$database = 'imesys';

		$sqlconnect = mysqli_connect($server, $username, $password,$database);
		return $sqlconnect;	
	}
	
		function connectimesysTE()
	{
		$server = '172.16.40.202';
		$username = 'ter1';
		$password = 'reliability';
	
		$database = 'mcdr-skywise';

		return mysqli_connect($server,$username,$password,$database);
	}
?>
