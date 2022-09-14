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
  
  $query = "
  SELECT * FROM tbl_attendance 
  WHERE student_id = '".$_GET['student_id']."' 
  AND attendance_date >= '".$_GET["from_date"]."' 
  AND attendance_date <= '".$_GET["to_date"]."'"




  function check ($start_date,$end_date) {

    // (C2) DATABASE ENTRY
    try {
      //select date_format(attendance_date, '%M %Y') as "Month",COUNT(CASE WHEN attendance_status = "Present" then 1 END) as "Present", COUNT(CASE WHEN attendance_status = "Absent" then 1 END) as "Absent"  from tbl_attendance where attendance_date<"2022-04-01" group by year(attendance_date),month(attendance_date);
      //$this->stmt = $this->pdo->prepare("SELECT * FROM tbl_attendance  where attendance_date >= ? AND  attendance_date <= ?");
      $this->stmt = $this->pdo->prepare("SELECT DATE_FORMAT(attendance_date, '%M %Y') AS 'Month', COUNT(CASE WHEN attendance_status = 'Present' then 1 END) as 'Present', COUNT(CASE WHEN attendance_status = 'Absent' THEN 1 END) AS 'Absent'  FROM tbl_attendance WHERE attendance_date>= ? attendance_date<= ? GROUP BY year(attendance_date),month(attendance_date)");
      $this->stmt->execute([$start_date,$end_date]);

      return $this->stmt->fetchall(PDO::FETCH_COLUMN, 0); 
    } catch (Exception $ex) {
      $this->error = $ex->getMessage();
      return false;
    }
  }
}

define("DB_HOST", "localhost");
define("DB_NAME", "pequecitas");
define("DB_CHARSET", "utf8");
define("DB_USER", "root");
define("DB_PASSWORD", "");


$_AVT = new Data_Range();



if ((isset($_GET["end_date"]) && (isset($_GET["start_date"]))){
  $start_date=$_GET["start_date"];
  $end_date=$_GET["end_date"];
  $records=$_AVT->check($start_date,$end_date);

$total_row = $records->rowCount();

foreach($result as $row)
{
  $status = '';
  if($row["attendance_status"] == "Present")
  {
    $total_present++;
    $status = '<span class="badge badge-success">Presente</span>';
  }

  if($row["attendance_status"] == "Absent")
  {
    $total_absent++;
    $status = '<span class="badge badge-danger">Ausente</span>';
  }

  $output .= '
    <tr>
      <td>'.$row["attendance_date"].'</td>
      <td>'.$status.'</td>
    </tr>
  ';

  $present_percentage = ($total_present/$total_row) * 100;
  $absent_percentage = ($total_absent/$total_row) * 100;

}




  header('Content-Type: application/json; charset=utf-8');
  echo json_encode($available_times);
}else {
  echo json_encode("LauraSAD");
}



?>