<?php
require_once(__DIR__. "/dbconfig.php");

session_start();

if($_SERVER["REQUEST_METHOD"] == "POST"){
    // $usid = $_SESSION["memberID"];
    $date = $_POST['selected_date'];
    $seat = $_POST['selected_seat'];
    $ts = $_POST['selected_ts'];

    $qry = "SELECT `seid` FROM seat WHERE `location`= '$seat'";
    $result = mysqli_query($conn, $qry);
    $row = mysqli_fetch_assoc($result);
    $seid = $row["seid"];

    $qry = "SELECT `tsid` FROM timeslot WHERE `label`= '$ts'";
    $result = mysqli_query($conn, $qry);
    $row = mysqli_fetch_assoc($result);
    $tsid = $row["tsid"];

    $qry = "INSERT INTO unavailable(`seid`, `date`, `tsid`)
            VALUES ('$seid', '$date', '$tsid')";
    $result = mysqli_query($conn, $qry);

    if($result){
        $_SESSION["message"] = "新增成功!";
        header("Location: unavailable_setting.php");
        exit();
    }
    else{
        $_SESSION["message"] = "新增失敗";
        header("Location: unavailable_setting.php");
        exit();
    }
}

mysqli_close($conn);

?>