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
    <title>自習室座位預約系統-records</title>
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
    <div class="card d-flex justify-content-center align-items-center mx-auto mt-5 bg-light px-5" style="width: 90%">
        <div class="card-body w-100">
            <h3 class="fw-bold text-center">所有紀錄</h3>
            <form action="#" method="post">
                <div class="d-flex justify-content-center align-items-center mt-4 gap-2">
                    <label for="search-date" class="form-label mb-0 fw-bold"> 日期查詢: </label>
                    <input type="date" class="form-control text-center" id="search-date" onclick="this.showPicker?.()" name="search_date" style="cursor: pointer; width: 200px;" value="<?php echo isset($_POST['search_date']) ? $_POST['search_date'] : ''?>" required>
                    <button type="submit" class="btn btn-primary"> 查詢 </button>
                </div>
            </form>
            <?php
                if (isset($_SESSION["message"])) {
                    echo '<script> alert("' . $_SESSION["message"] . '") </script>';
                    unset($_SESSION["message"]);
                }
            ?>
            <form action="#" method="post">
                <div class="d-flex justify-content-center align-items-center mt-4">
                    <button type="submit" class="btn btn-primary" name="all_record"> 顯示所有紀錄 </button>
                </div>
            </form>
            <table class="table table-bordered text-center align-middle my-4">
                <thead class="table-light fs-5">
                    <tr>
                        <th>預約人</th>
                        <th>座位位置</th>
                        <th>座位插座</th>
                        <th>預約日期</th>
                        <th>預約時段</th>
                        <th>預約時間</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['search_date']))
                        {
                            $date = $_POST['search_date'];
                            $query = "SELECT seat.is_socket, seat.location, timeslot.label, reservation.date, reservation.reserve_time, `name`
                                        FROM reservation, seat, timeslot, user
                                        WHERE reservation.seid = seat.seid 
                                        AND reservation.tsid = timeslot.tsid
                                        AND reservation.date = '$date'
                                        AND reservation.usid = user.usid
                                        ORDER BY reserve_time DESC";
                            $result = mysqli_query($conn, $query);
                            $rownum = mysqli_num_rows($result);
                            // echo "$_SESSION[memberID]";
                            if($rownum > 0){
                                while($row = mysqli_fetch_assoc($result)){
                                    $socket = $row['is_socket'] ? 'YES' : 'NO';
                                    echo "
                                    <tr>
                                        <td> {$row['name']} </td>
                                        <td> {$row['location']} </td>
                                        <td> {$socket} </td>
                                        <td> {$row['date']} </td>
                                        <td> {$row['label']} </td>
                                        <td> {$row['reserve_time']} </td>
                                    </tr>";
                                }
                            }
                            else {
                                echo "
                                <tr>
                                    <td colspan='6' class='text-muted text-center'> 無預約紀錄 </td>
                                </tr>";
                            }
                        }
                    ?>
                    <?php
                        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['all_record']))
                        {
                            // $now = date('Y-m-d');
                            $query = "SELECT seat.is_socket, seat.location, timeslot.label, reservation.reid, reservation.date, reservation.reserve_time, `name`
                                        FROM reservation, seat, timeslot, user
                                        WHERE reservation.seid = seat.seid 
                                        AND reservation.tsid = timeslot.tsid
                                        AND reservation.usid = user.usid
                                        ORDER BY reserve_time DESC";
                            $result = mysqli_query($conn, $query);
                            $rownum = mysqli_num_rows($result);
                            // echo "$_SESSION[memberID]";
                            if($rownum > 0){
                                while($row = mysqli_fetch_assoc($result)){
                                    $socket = $row['is_socket'] ? 'YES' : 'NO';
                                    echo "
                                    <tr>
                                        <td> {$row['name']} </td>
                                        <td> {$row['location']} </td>
                                        <td> {$socket} </td>
                                        <td> {$row['date']} </td>
                                        <td> {$row['label']} </td>
                                        <td> {$row['reserve_time']} </td>
                                    </tr>";
                                }
                            }
                            else {
                                echo "
                                <tr>
                                    <td colspan='6' class='text-muted text-center'> 無預約紀錄 </td>
                                </tr>";
                            }
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>