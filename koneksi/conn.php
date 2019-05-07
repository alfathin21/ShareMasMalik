<? 
include "adodb/adodb.inc.php";

$db = &ADONewConnection('mysql');
$db->Connect('192.168.40.118','develop','aeroasia','imesys');
//$db->Connect('localhost','root','','imesys');
//
$dbter= &ADONewConnection('mysql');

$dbter->Connect('GMFAA-TE','reliability','ter1','mcdr');



?>