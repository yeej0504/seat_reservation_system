<?php
// 連接資料庫
require_once(__DIR__.'/dbconfig.php');
session_start();

$date = $_GET['date'];
$seat = $_GET['seat']; // 如果你要細分到座位層級的話
$usid = $_SESSION['memberID'];

// 取得禁用時段
$stmt1 = $conn->prepare("SELECT label FROM unavailable, timeslot WHERE unavailable.tsid = timeslot.tsid AND date = ?");
$stmt1->bind_param("s", $date);
$stmt1->execute();
$result1 = $stmt1->get_result();

$disabled = [];
while ($row = $result1->fetch_assoc()) {
    $disabled[] = $row['label'];
}

// 取得已被預約的時段（如果每個座位只能一人預約）
$stmt2 = $conn->prepare("SELECT label FROM reservation, timeslot, seat WHERE reservation.tsid = timeslot.tsid AND reservation.seid = seat.seid AND date = ? AND location = ?");
$stmt2->bind_param("ss", $date, $seat);
$stmt2->execute();
$result2 = $stmt2->get_result();

while ($row = $result2->fetch_assoc()) {
    $disabled[] = $row['label'];
}

// 如果該用戶在該時段已預約，則所有座位的該時段都不可用
$stmt3 = $conn->prepare("SELECT label FROM reservation, timeslot WHERE reservation.tsid = timeslot.tsid AND reservation.usid = ? AND `date` = ?");
$stmt3->bind_param("ss", $usid, $date);
$stmt3->execute();
$result3 = $stmt3->get_result();

while ($row = $result3->fetch_assoc()) {
    $disabled[] = $row["label"];
}

echo json_encode(array_unique($disabled)); // 移除重複
?>

