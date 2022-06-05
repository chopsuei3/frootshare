<?php
include "dbinfo.php"; 

$sql = "SELECT * FROM locations WHERE 1";
$result = mysqli_query($link, $sql) or die("Error in Selecting " . mysqli_error($link));

//create an array
$emparray = array();
while($row =mysqli_fetch_assoc($result))
{
$emparray[] = $row;
}
$mapped_markers = json_encode($emparray);

//close the db connection
mysqli_close($link);

?>
