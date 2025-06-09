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
    <title>自習室座位預約系統-座位</title>
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
    <div class="card d-flex justify-content-center align-items-center mx-auto my-5 bg-light px-5" style="width: 80%">
        <div class="card-body w-100">
            <h3 class="fw-bold text-center">座位資訊</h3>
            <form action="" method="post">
                <?php
                    if (isset($_SESSION["message"])) {
                        echo '<script> alert("' . $_SESSION["message"] . '") </script>';
                        unset($_SESSION["message"]);
                    }
                ?>
                <table class="table table-bordered text-center align-middle mx-auto my-4" style="width: 85%">
                    <thead class="table-light fs-5">
                        <tr>
                            <th>座位位置</th>
                            <th>插座有無</th>
                            <th>該日已被借出時段</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $query = "SELECT `seid`, `location`, `is_socket`
                                        FROM seat";
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
                                        <td> 
                                            <div class='d-flex align-items-center justify-content-center gap-2'> 
                                                <input id='selected-date' type='date' style='cursor: pointer;' onclick='this.showPicker?.()' class='form-control text-center w-50' name='selected_date' required='true'>
                                                <button type='button' class='form-control btn btn-outline-primary w-auto' onclick='queryTimeslot(\"{$row['seid']}\", this)'>
                                                    <i class='bi bi-search'> </i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>";
                                }
                            }
                            else {
                                echo "
                                <tr>
                                    <td colspan='3' class='text-muted text-center'> 無座位資訊 </td>
                                </tr>";
                            }
                        ?>
                        <!-- 查詢結果Modal -->
                        <div class="modal fade" id="resultModal" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">已被借出時段</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body" id="resultModalBody">
                                        請稍候...
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">關閉</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </tbody>
                </table>
            </form> 
        </div>
    </div>
</body>
<script>
function queryTimeslot(seid, btn) {
    const row = btn.closest('tr');
    const dateInput = row.querySelector('input[type="date"]');
    const selectedDate = dateInput.value;

    if (!selectedDate) {
        alert("請先選擇日期！");
        return;
    }

    // AJAX 請求到後端 PHP 檔
    fetch(`query_timeslot.php?seid=${seid}&date=${selectedDate}`)
        .then(response => response.text())
        .then(data => {
            document.getElementById("resultModalBody").innerHTML = data;
            let modal = new bootstrap.Modal(document.getElementById("resultModal"));
            modal.show();
        })
        .catch(err => {
            document.getElementById("resultModalBody").innerHTML = "查詢失敗，請稍後再試。";
            let modal = new bootstrap.Modal(document.getElementById("resultModal"));
            modal.show();
        });
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</html>