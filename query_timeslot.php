<?php
require_once(__DIR__."/dbconfig.php");

$seid = $_GET['seid'];
$date = $_GET['date'];

// 查詢該座位、該日期的已被預約時段
$sql = "SELECT t.label
        FROM reservation r
        JOIN timeslot t ON r.tsid = t.tsid
        WHERE r.seid = ? AND r.date = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $seid, $date);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "<ul class='list-group'>";
    while ($row = $result->fetch_assoc()) {
        echo "<li class='list-group-item'>{$row['label']}</li>";
    }
    echo "</ul>";
} else {
    echo "<p class='text-muted'>目前無借出紀錄。</p>";
}
?>
