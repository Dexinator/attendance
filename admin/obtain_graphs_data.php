<?php

class Data_Range{
  // (A) CONSTRUCTOR - CONNECT TO DATABASE
  private $pdo; // PDO object
  private $stmt; // SQL statement
  public $error; // Error message
  
  function __construct() {
    try {
      $this->pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET,
        DB_USER, DB_PASSWORD, [
          PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
          PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_NAMED
        ]
      );
    } catch (Exception $ex) { exit($ex->getMessage()); }
  }


  function __destruct() {
    $this->pdo = null;
    $this->stmt = null;
  }
  




  function check ($start_date,$end_date,$periodicidad) {


    $corr_start=correct_times($start_date);
    $corr_end=correct_times($end_date);

    // (C2) DATABASE ENTRY
    try {
      //select date_format(attendance_date, '%M %Y') as "Month",COUNT(CASE WHEN attendance_status = "Present" then 1 END) as "Present", COUNT(CASE WHEN attendance_status = "Absent" then 1 END) as "Absent"  from tbl_attendance where attendance_date<"2022-04-01" group by year(attendance_date),month(attendance_date);
      //$this->stmt = $this->pdo->prepare("SELECT * FROM tbl_attendance  where attendance_date >= ? AND  attendance_date <= ?");
      if ($periodicidad=="Mensual"){
        $this->stmt = $this->pdo->prepare("SELECT DATE_FORMAT(attendance_date, '%M %Y') AS 'Month', COUNT(CASE WHEN attendance_status = 'Present' then 1 END) as 'Present', COUNT(CASE WHEN attendance_status = 'Absent' THEN 1 END) AS 'Absent'  FROM tbl_attendance WHERE attendance_date>=FROM_UNIXTIME(?)  AND attendance_date<= FROM_UNIXTIME(?) GROUP BY year(attendance_date),month(attendance_date)");
      }elseif ($periodicidad=="Diario") {
        $this->stmt = $this->pdo->prepare("SELECT DATE_FORMAT(attendance_date, '%M %Y %D') AS 'DAY', COUNT(CASE WHEN attendance_status = 'Present' then 1 END) as 'Present', COUNT(CASE WHEN attendance_status = 'Absent' THEN 1 END) AS 'Absent'  FROM tbl_attendance WHERE attendance_date>=FROM_UNIXTIME(?)  AND attendance_date<= FROM_UNIXTIME(?) GROUP BY month(attendance_date),day(attendance_date)");
      }
      $this->stmt->execute([$corr_start,$corr_end]);

      return $this->stmt->fetchall(); 
    } catch (Exception $ex) {
      $this->error = $ex->getMessage();
      return false;
    }
  }
}

function correct_times($item) {
  return $item/ 1000;
}

define("DB_HOST", "localhost");
define("DB_NAME", "attendance");
define("DB_CHARSET", "utf8");
define("DB_USER", "root");
define("DB_PASSWORD", "");



$_AVT = new Data_Range();



if ((isset($_GET["end_date"])) && (isset($_GET["start_date"]))){
  $start_date=$_GET["start_date"];
  $end_date=$_GET["end_date"];
  $periodicidad=$_GET["periodicidad"];

  $records=$_AVT->check($start_date,$end_date,$periodicidad);

  $Presentes=0;
  $Ausentes=0;
  $total=0;
/*
  foreach($records as $row)
  {
    $records[0]["suma"]=$row["Present"]+$row["Absent"];
    $Ausentes=$row["Absent"];
    $total=$Presentes+$Ausentes;
  }
*/
for ($i = 0; $i < count($records); $i++) {
    $records[$i]["asistencia"]=$records[$i]["Present"]/($records[$i]["Present"]+$records[$i]["Absent"]);
    $records[$i]["faltas"]=$records[$i]["Absent"]/($records[$i]["Present"]+$records[$i]["Absent"]);

}

  header('Content-Type: application/json; charset=utf-8');
  echo json_encode($records);
}else {
  echo json_encode("LauraSAD");
}



?>