<?php
require_once(__DIR__. "/dbconfig.php");
require_once(__DIR__.'/func_sendemail_sample.php');
session_start();

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $usid = $_SESSION["memberID"];
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

    $qry = "INSERT INTO reservation(`usid`, `seid`, `date`, `tsid`)
            VALUES ('$usid', '$seid', '$date', '$tsid')";
    $result = mysqli_query($conn, $qry);

    if($result){
        $qry = "SELECT reserve_time
                FROM reservation
                WHERE `usid` = '$usid'
                AND `seid` = '$seid'
                AND `date` = '$date'
                AND `tsid` = '$tsid'";
        $result = mysqli_query($conn, $qry);
        $row = mysqli_fetch_assoc($result);
        $reverse_time = $row['reserve_time'];

        $email_sender_email = "M133040097@student.nsysu.edu.tw";
        $email_sender_name = "管理員";
        $email_recipient_email = $_SESSION['memberEmail'];
        $email_recipient_name = $_SESSION['memberName'];
        $email_subject = "自習室座位預約系統-預約成功通知";
        $email_body = "{$email_recipient_name} 同學您好:\r\n\r\n您於{$reverse_time}預約了以下座位：\r\n\r\n座位：{$seat}\r\n日期：{$date}\r\n時段：{$ts}\r\n\r\n請準時前往，逾時將違規記點。\r\n\r\n國立中山大學自習室\t敬啟";
        $msg = sendemail_sample($email_sender_email, $email_sender_name, $email_recipient_email, $email_recipient_name, $email_subject, $email_body);
        $_SESSION["message"] = "預約成功!{$msg}";
        header("Location: seat_reserve.php");
        exit();
    }
    else{
        $_SESSION["message"] = "預約失敗";
        header("Location: seat_reserve.php");
        exit();
    }
}

mysqli_close($conn);

?>