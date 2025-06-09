<?php
    require_once(__DIR__.'/dbconfig.php');
    session_start();
    if(!isset($_SESSION['loggedin'])){
        header('location: ./logout.php');
        exit;
    }
    if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_seat'])){
        $delete_seid = $_POST['delete_seat'];
        $query = "DELETE FROM seat WHERE `seid` = '$delete_seid'";
        $result = mysqli_query($conn, $query);
        if($result){
            $_SESSION['message'] = '刪除成功';
        }
        else{
            $_SESSION['message'] = '刪除失敗';
        }
    }
    if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['socket_status'])){
        $seid = $_POST['seid'];
        $status = $_POST['socket_status'];
        $query = "UPDATE seat SET `is_socket` = '$status' WHERE `seid` = '$seid'";
        $result = mysqli_query($conn, $query);
    }
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_seat'])) {
        $floor = $_POST['floor']; // 例如 '1F'
        $socket = $_POST['socket'];
        $location = getNextSeatLocation($conn, $floor);
    
        $query = "INSERT INTO seat (location, is_socket) VALUES ('$location', '$socket')";
        mysqli_query($conn, $query);
    
        $_SESSION["message"] = "成功新增座位：$location";
    }
    function getNextSeatLocation($conn, $floor) {
        // 先抓該樓層所有座位
        $query = "SELECT location FROM seat WHERE location LIKE '{$floor}-%'";
        $result = mysqli_query($conn, $query);
    
        $used_numbers = [];
    
        while ($row = mysqli_fetch_assoc($result)) {
            // 拆出數字，例如 1F-03 -> 03
            $parts = explode('-', $row['location']);
            if (count($parts) === 2) {
                $used_numbers[] = intval($parts[1]);
            }
        }
    
        // 決定下一個號碼
        $next_num = 1;
        while (in_array($next_num, $used_numbers)) {
            $next_num++;
        }
    
        // 格式化為兩位數
        $next_num_str = str_pad($next_num, 2, '0', STR_PAD_LEFT);
        return "{$floor}-{$next_num_str}";
    }
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>自習室座位預約系統-座位管理</title>
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
    <div class="card d-flex justify-content-center align-items-center mx-auto my-5 bg-light px-5" style="width: 80%">
        <div class="card-body w-100">
            <h3 class="fw-bold text-center">座位管理</h3>
                <?php
                    if (isset($_SESSION["message"])) {
                        echo '<script> alert("' . $_SESSION["message"] . '") </script>';
                        unset($_SESSION["message"]);
                    }
                ?>
                <div class="card mx-auto my-4" style="width: 85%">
                    <form action='#' method="post">
                        <div class="d-flex justify-content-center align-items-center my-4">
                            <label for="floor" class="form-label mb-0 fw-bold">選擇樓層: </label>
                            <select id="floor" name="floor" class="form-select mx-3 w-auto" required>
                                <option value="1F">1F</option>
                                <option value="2F">2F</option>
                                <option value="3F">3F</option>
                            </select>
                            <label for="socket" class="form-label mb-0 fw-bold">有無插座: </label>
                            <select id="socket" name="socket" class="form-select mx-3 w-auto" required>
                                <option value="1">YES</option>
                                <option value="0">NO</option>
                            </select>
                            <button type="submit" class="btn btn-primary" name="add_seat">新增座位</button>
                        </div>
                    </form>
                </div>
                <table class="table table-bordered text-center align-middle mx-auto my-4" style="width: 85%">
                    <thead class="table-light fs-5">
                        <tr>
                            <th>座位位置</th>
                            <th>插座有無</th>
                            <th>該日已被借出時段</th>
                            <th>刪除座位</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $query = "SELECT `seid`, `location`, `is_socket`
                                        FROM seat
                                        ORDER BY `location`";
                            $result = mysqli_query($conn, $query);
                            $rownum = mysqli_num_rows($result);
                            if($rownum > 0){
                                while($row = mysqli_fetch_assoc($result)){
                                    $socket = $row['is_socket'] ? 'YES' : 'NO';
                                    echo "
                                    <tr>
                                        <td> {$row['location']} </td>
                                        <td>
                                            <form action='#' method='post'>
                                                <input type='hidden' name='seid' value='{$row['seid']}'>
                                                <select class='form-select' name='socket_status' onchange='this.form.submit()'>
                                                    <option value='1' ". ($socket == 'YES' ? 'selected' : '') . "> YES </option>
                                                    <option value='0' " . ($socket == 'NO' ? 'selected' : '') . "> NO </option>
                                                </select>
                                            </form>
                                        </td>
                                        <td>
                                            <form action='#' method='post'>
                                                <div class='d-flex align-items-center justify-content-center gap-2'> 
                                                    <input id='selected-date' type='date' class='form-control text-center w-50' style='cursor: pointer;' onclick='this.showPicker?.()' name='selected_date' required='true'>
                                                    <button type='button' class='form-control btn btn-outline-secondary justify-content-center d-flex w-auto' onclick='queryTimeslot(\"{$row['seid']}\", this)'>
                                                        <i class='bi bi-search'> </i>
                                                    </button>
                                                </div>
                                            </form>
                                        </td>
                                        <td> 
                                            <form action='#' method='post'>
                                                <button type='submit' class='btn btn-sm btn-outline-danger' name='delete_seat' value={$row['seid']}> 刪除 </button>
                                            </form>
                                        </td>
                                    </tr>";
                                }
                            }
                            else {
                                echo "
                                <tr>
                                    <td colspan='4' class='text-muted text-center'> 無座位資訊 </td>
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