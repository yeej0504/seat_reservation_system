<?php
    require_once(__DIR__.'/dbconfig.php');
    session_start();
    if(!isset($_SESSION['loggedin'])){
        header('location: ./logout.php');
        exit;
    }
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>自習室座位預約系統-首頁</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .nav-link.active {
            background-color:rgb(7, 77, 22) !important;
        }
        .nav-link:hover {
            background-color:rgb(7, 77, 22) !important; /* hover 顏色可自訂 */
        }
    </style>
</head>
<body>
    <!-- 導覽列 -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <div class="container-fluid">
            <!-- 系統名稱 -->
            <a class="navbar-brand fw-bold fs-3" href="adminpage.php">自習室座位預約系統</a>

            <!-- 漢堡選單按鈕（手機版會用到） -->
            <!-- <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                <span class="navbar-toggler-icon"></span>
            </button> -->

            <!-- 導覽列中功能按鈕區 -->
            <div class="flex-grow-1">
                <ul class="nav nav-pills nav-fill">
                    <li class="nav-item mx-1">
                        <a class="nav-link text-white fs-5 <?php echo basename($_SERVER['PHP_SELF']) == 'unavailable_setting.php' ? 'active bg-info' : ''; ?>" href="unavailable_setting.php">設定不開放時段</a>
                    </li>
                    <li class="nav-item mx-1">
                        <a class="nav-link text-white fs-5 <?php echo basename($_SERVER['PHP_SELF']) == 'record_all.php' ? 'active bg-info' : ''; ?>" href="record_all.php">查詢預約紀錄</a>
                    </li>
                    <li class="nav-item mx-1">
                        <a class="nav-link text-white fs-5 <?php echo basename($_SERVER['PHP_SELF']) == 'seat_management.php' ? 'active bg-info' : ''; ?>" href="seat_management.php">座位管理</a>
                    </li>
                </ul>
            </div>

            <!-- 登出按鈕 -->
            <div class="d-flex align-items-center">
                <a href="account.php" class="btn text-white fs-3 me-4">
                    <i class="bi bi-person-circle text-white fs-4"></i>
                    <span class="fs-4"> <?php echo $_SESSION['memberName'] ?> </span>
                    </a> 
                <a href="logout.php" class="btn btn-danger">登出</a>
            </div>
        </div>
    </nav>
    <!-- 主內容 -->
    <div>
        <!-- <div>
            <h3 class="text-center mt-4 fw-bold"> 最新十筆預約紀錄 </h3>
            <table class="table table-bordered text-center align-middle mx-auto" style="width: 80%;">
                <thead class="table-light fs-5">
                    <tr>
                        <th>預約人</th>
                        <th>預約座位</th>
                        <th>預約日期</th>
                        <th>預約時段</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $now = date('Y-m-d');
                        $query = "SELECT user.name, seat.location, timeslot.label, reservation.date
                                    FROM reservation, user, seat, timeslot
                                    WHERE reservation.usid = user.usid
                                    AND reservation.seid = seat.seid
                                    AND reservation.tsid = timeslot.tsid
                                    ORDER BY reserve_time DESC
                                    LIMIT 10";
                        $result = mysqli_query($conn, $query);
                        $rownum = mysqli_num_rows($result);
                        // echo "$_SESSION[memberID]";
                        if($rownum > 0){
                            while($row = mysqli_fetch_assoc($result)){
                                echo "
                                <tr>
                                    <td> {$row['name']} </td>
                                    <td> {$row['location']} </td>
                                    <td> {$row['date']} </td>
                                    <td> {$row['label']} </td>
                                </tr>";
                            }
                        }
                        else {
                            echo "
                            <tr>
                                <td colspan='4' class='text-muted text-center'> 無預約紀錄 </td>
                            </tr>";
                        }
                    ?>
                </tbody>
            </table>
        </div>
        <br>
        <hr>
        <br> -->
        <div>
            <h3 class="text-center mt-4 fw-bold"> 不開放借用時段 </h3>
            <table class="table table-bordered text-center align-middle mx-auto" style="table-layout: fixed; width: 80%;">
                <thead class="table-light fs-5">
                    <tr>
                        <th>座位位置</th>
                        <th>日期</th>
                        <th>時段</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $now = date('Y-m-d');
                        $query = "SELECT unavailable.seid, location, label, unavailable.date
                                    FROM unavailable, seat, timeslot
                                    WHERE unavailable.seid = seat.seid
                                    AND unavailable.tsid = timeslot.tsid
                                    AND DATE(unavailable.date) >= DATE('$now')
                                    ORDER BY unavailable.date, unavailable.tsid";
                        $result = mysqli_query($conn, $query);
                        $rownum = mysqli_num_rows($result);
                        // echo "$_SESSION[memberID]";
                        if($rownum > 0){
                            while($row = mysqli_fetch_assoc($result)){
                                echo "
                                <tr>
                                    <td> {$row['location']} </td>
                                    <td> {$row['date']} </td>
                                    <td> {$row['label']} </td>
                                </tr>";
                            }
                        }
                        else {
                            echo "
                            <tr>
                                <td colspan='3' class='text-muted text-center'> 無不開放時段 </td>
                            </tr>";
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>