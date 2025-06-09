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
    <title>自習室座位預約系統-紀錄</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .nav-link.active {
            background-color: #138496 !important;
        }
        .nav-link:hover {
            background-color: #138496 !important; /* hover 顏色可自訂 */
        }
    </style>
</head>
<body>

    <!-- 導覽列 -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-info">
        <div class="container-fluid">

            <!-- 系統名稱 -->
            <a class="navbar-brand fw-bold fs-3" href="userpage.php">自習室座位預約系統</a>

            <!-- 導覽列中功能按鈕區 -->
            <div class="flex-grow-1">
                <ul class="nav nav-pills nav-fill">
                    <li class="nav-item mx-1">
                        <a class="nav-link text-white fs-5 <?php echo basename($_SERVER['PHP_SELF']) == 'seat_reserve.php' ? 'active bg-info' : ''; ?>" href="seat_reserve.php">預約座位</a>
                    </li>
                    <li class="nav-item mx-1">
                        <a class="nav-link text-white fs-5 <?php echo basename($_SERVER['PHP_SELF']) == 'reserved_record.php' ? 'active bg-info' : ''; ?>" href="reserved_record.php">預約紀錄</a>
                    </li>
                    <li class="nav-item mx-1">
                        <a class="nav-link text-white fs-5 <?php echo basename($_SERVER['PHP_SELF']) == 'seat_info.php' ? 'active bg-info' : ''; ?>" href="seat_info.php">座位資訊</a>
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
            <form action="cancel.php" method="post">
                <?php
                    if (isset($_SESSION["message"])) {
                        echo '<script> alert("' . $_SESSION["message"] . '") </script>';
                        unset($_SESSION["message"]);
                    }
                ?>
                <table class="table table-bordered text-center align-middle my-4">
                    <thead class="table-light fs-5">
                        <tr>
                            <th>座位位置</th>
                            <th>座位插座</th>
                            <th>預約日期</th>
                            <th>預約時段</th>
                            <th>預約時間</th>
                            <th class="cancel-mode d-none"> 
                                <input class="text-start" type="checkbox" onclick="toggleSelectAll(this)"> 全選 
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $now = date('Y-m-d');
                            $query = "SELECT seat.is_socket, seat.location, timeslot.label, reservation.reid, reservation.date, reservation.reserve_time
                                        FROM reservation, seat, timeslot
                                        WHERE reservation.usid = {$_SESSION['memberID']} 
                                        AND reservation.seid = seat.seid 
                                        AND reservation.tsid = timeslot.tsid 
                                        ORDER BY reserve_time DESC";
                            $result = mysqli_query($conn, $query);
                            $rownum = mysqli_num_rows($result);
                            // echo "$_SESSION[memberID]";
                            if($rownum > 0){
                                while($row = mysqli_fetch_assoc($result)){
                                    $socket = $row['is_socket'] ? 'YES' : 'NO';
                                    echo "
                                    <tr>
                                        <td> {$row['location']} </td>
                                        <td> {$socket} </td>
                                        <td> {$row['date']} </td>
                                        <td> {$row['label']} </td>
                                        <td> {$row['reserve_time']} </td>
                                        <td class='cancel-mode d-none'> 
                                            <input type='checkbox' name='cancel[]' value='{$row['reid']}'> 
                                        </td>
                                    </tr>";
                                }
                            }
                            else {
                                echo "
                                <tr>
                                    <td colspan='6' class='text-muted text-center'> 無預約紀錄 </td>
                                </tr>";
                            }
                        ?>
                    </tbody>
                </table>
                <div class="text-center">
                    <button type="button" class="btn btn-danger" id="toggleCancel">取消預約</button>
                    <button type="submit" class="btn btn-danger d-none" id="confirmCancel">確認取消</button>
                    <button type="button" class="btn btn-secondary d-none" id="cancelAction">取消操作</button>
                </div>
            </form> 
        </div>
    </div>
</body>
<script>
    function toggleCancelMode(isCancelMode) {
        if (isCancelMode) {
            // 顯示 checkbox 欄位和按鈕
            document.querySelectorAll('.cancel-mode').forEach(e => e.classList.remove('d-none'));
            document.getElementById('confirmCancel').classList.remove('d-none');
            document.getElementById('cancelAction').classList.remove('d-none');
            document.getElementById('toggleCancel').classList.add('d-none'); // 隱藏自己
        } else {
            // 隱藏 checkbox 欄位和按鈕
            document.querySelectorAll('.cancel-mode').forEach(e => e.classList.add('d-none'));
            document.getElementById('confirmCancel').classList.add('d-none');
            document.getElementById('toggleCancel').classList.remove('d-none');
            document.getElementById('cancelAction').classList.add('d-none');

            // 清除 checkbox 選取
            document.querySelectorAll("input[type='checkbox']").forEach(checkbox => checkbox.checked = false);
        }
    }

    // 切換全選或全不選
    function toggleSelectAll(selectAllCheckbox) {
        const checkboxes = document.querySelectorAll("input[type='checkbox']");
        checkboxes.forEach(checkbox => checkbox.checked = selectAllCheckbox.checked);
    }

    // 事件綁定
    document.getElementById('toggleCancel').addEventListener('click', function () {
        toggleCancelMode(true);
    });

    document.getElementById('cancelAction').addEventListener('click', function () {
        toggleCancelMode(false);
    });
</script>
</html>