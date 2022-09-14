<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "calmecac";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM records_agosto";
$result = $conn->query($sql);

$myArray = array();
if ($result->num_rows > 0) {
// output data of each row
  while($row = $result->fetch_assoc()) {
    $myArray[] = $row;
  }
} 
else 
{
  echo "0 results";
}
$conn->close();


?>