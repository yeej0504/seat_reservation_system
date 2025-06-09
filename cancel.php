<?php
session_start();
require_once(__DIR__ . '/dbconfig.php');
require_once(__DIR__.'/func_sendemail_sample.php');
if (isset($_POST['cancel'])) {
    $email_sender_email = "M133040097@student.nsysu.edu.tw";
    $email_sender_name = "管理員";
    $email_recipient_email = $_SESSION['memberEmail'];
    $email_recipient_name = $_SESSION['memberName'];
    $email_subject = "自習室座位預約系統-預約取消通知";
    $current_time = date('Y-m-d H:i:s');
    $email_body = "{$email_recipient_name} 同學您好:\r\n\r\n您於{$current_time}取消了以下預約:\r\n\r\n";
    foreach ($_POST['cancel'] as $reid) {
        $reid = intval($reid);
        $query = "SELECT `location`, `date`, `label`
                FROM reservation, seat, timeslot
                WHERE `reid` = '$reid'
                AND reservation.seid = seat.seid
                AND reservation.tsid = timeslot.tsid";
        $result = mysqli_query($conn, $query);
        $row = mysqli_fetch_assoc($result);
        $seat = $row['location'];
        $date = $row['date'];
        $ts = $row['label'];

        $query = "DELETE FROM reservation WHERE reid = {$reid}";
        $result = mysqli_query($conn, $query);
        if($result){
            $email_body .= "座位：{$seat}\r\n日期：{$date}\r\n時段：{$ts}\r\n\r\n";
        }
    }
    $email_body .= "國立中山大學自習室\t敬啟";
    $msg = sendemail_sample($email_sender_email, $email_sender_name, $email_recipient_email, $email_recipient_name, $email_subject, $email_body);
    mysqli_close($conn);
    $_SESSION['message'] = "取消成功!{$msg}";
    header('Location: userpage.php');
    exit();
} else {
    $_SESSION['message'] = "你沒有選擇任何項目!";
    header('Location: userpage.php');
    exit();
}
?>