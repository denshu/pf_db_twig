<?php 

$con = mysqli_connect("localhost","root","DSFARGEG","pf_database");

if (mysqli_connect_errno())
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }

return $con;

?>