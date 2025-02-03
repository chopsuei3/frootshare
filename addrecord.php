<?php
include "dbinfo.php"; 

// Gets data from URL parameters
$name = $_GET['name'];
$address = $_GET['address'];
$lat = $_GET['lat'];
$lng = $_GET['lng'];
$type = strtolower($_GET['type']);

//echo $name, $address, $lat, $lng, $type;

$query = "INSERT INTO locations " . " (id, name, address, lat, lng, type ) " . "VALUES (NULL, '%s', '%s', '%s', '%s', '%s');";
$sql = sprintf($query, mysqli_real_escape_string($link, $name), mysqli_real_escape_string($link, $address),mysqli_real_escape_string($link, $lat), mysqli_real_escape_string($link, $lng), mysqli_real_escape_string($link, $type));

//echo $sql;
//$sql = "SELECT * FROM locations WHERE 1";
$result = mysqli_query($link, $sql) or die("Error in Selecting " . mysqli_error($link));

if (!$result) {
  die('Invalid query: ' . mysqli_error());
}
//else {
//	print_r($result);
//}
//close the db connection
mysqli_close($link);
?>
