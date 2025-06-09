<?php
    require_once(__DIR__.'/dbconfig.php');
    session_start();
    if (!isset($_SESSION['memberID'])) {
        header('Location: login.php');
        exit();
    }
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>自習室座位預約系統-預約</title>
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
    <div class="card d-flex justify-content-center align-items-center mx-auto mt-5 bg-light" style="width: 70%">
        <div class="card-body">
            <h3 class="fw-bold text-center">預約座位</h3>
            <div class="d-flex justify-content-center align-items-center mx-auto">
                <form action="reserve.php" method="post">
                    <?php
                        if (isset($_SESSION["message"])) {
                            echo '<script> alert("' . $_SESSION["message"] . '") </script>';
                            unset($_SESSION["message"]);
                        }
                    ?>
                    <ol class="fw-bold">
                        <li>
                            <!-- 選擇日期 -->
                            <div class="d-flex align-items-center">
                                <span>選擇日期:&nbsp;&nbsp;&nbsp;&nbsp;</span>
                                <input id="selected-date" type="date" class="form-control w-50 text-center" style="cursor: pointer;" onclick="this.showPicker?.()" name="selected_date" min="<?php echo date('Y-m-d');?>" required>
                            </div>
                        </li>
                        <br>
                        <li>
                            <!-- 選擇座位 -->
                             <div class="d-flex align-items-center gap-2">
                                <span>選擇座位:&nbsp;&nbsp;</span>
                                <input type="text" id="selected-seat" class="fw-bold text-dark form-control w-50 text-center" readonly required placeholder="未選取" name="selected_seat">
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#seatModal"> 選取 </button>
                             </div>
                            <!-- 座位Modal -->
                            <div class="modal fade" id="seatModal" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <!-- modal 標題 -->
                                        <div class="modal-header">
                                            <h5 class="modal-title">選擇座位</h5>
                                            <span class="badge bg-warning text-dark mx-1">有插座</span>
                                            <span class="badge bg-secondary text-dark mx-1">無插座</span>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <!-- modal 內容 -->
                                        <div class="modal-body">
                                            <!-- 樓層頁籤 -->
                                            <ul class="d-flex nav nav-tabs mb-5 bg-info rounded nav-fill" id="floorTab" role="tablist">
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#floor1" type="button">1F</button>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#floor2" type="button">2F</button>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#floor3" type="button">3F</button>
                                                </li>
                                            </ul>
                                            <!-- 樓層座位內容 -->
                                            <div class="tab-content">
                                                <div class="tab-pane fade show active" id="floor1">
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered text-center align-middle" id="floor1-table">
                                                            <?php
                                                                $query = "SELECT `seid`, `location`, `is_socket`
                                                                            FROM seat
                                                                            WHERE `location` LIKE '%1F%'
                                                                            ORDER BY `seid`";
                                                                $result = mysqli_query($conn, $query);
                                                                $rownum = mysqli_num_rows($result);
                                                                if($rownum > 0) {
                                                                    for($i = 0; $i < $rownum / 3; $i++) {
                                                                        echo "<tr>";
                                                                        for($j = 0; $j < 3; $j++) {
                                                                            if(!$row = mysqli_fetch_array($result)){
                                                                                break;
                                                                            }
                                                                            $socket_color = $row['is_socket'] ? 'warning' : 'secondary';
                                                                            echo "<td><button type='button' class='btn btn-outline-{$socket_color} btn-sm seat-btn' data-seat='{$row['location']}'>{$row['location']}</button></td>";
                                                                        }
                                                                        echo"</tr>";
                                                                    }
                                                                }
                                                            ?>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="tab-pane fade show" id="floor2">
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered text-center align-middle" id="floor2-table">
                                                            <?php
                                                                $query = "SELECT `seid`, `location`, `is_socket`
                                                                            FROM seat
                                                                            WHERE `location` LIKE '%2F%'
                                                                            ORDER BY `seid`";
                                                                $result = mysqli_query($conn, $query);
                                                                $rownum = mysqli_num_rows($result);
                                                                if($rownum > 0) {
                                                                    for($i = 0; $i < $rownum / 3; $i++) {
                                                                        echo "<tr>";
                                                                        for($j = 0; $j < 3; $j++) {
                                                                            if(!$row = mysqli_fetch_array($result)){
                                                                                break;
                                                                            }
                                                                            $socket_color = $row['is_socket'] ? 'warning' : 'secondary';
                                                                            echo "<td><button type='button' class='btn btn-outline-{$socket_color} btn-sm seat-btn' data-seat='{$row['location']}'>{$row['location']}</button></td>";
                                                                        }
                                                                        echo"</tr>";
                                                                    }
                                                                }
                                                            ?>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="tab-pane fade show" id="floor3">
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered text-center align-middle" id="floor3-table">
                                                            <?php
                                                                $query = "SELECT `seid`, `location`, `is_socket`
                                                                            FROM seat
                                                                            WHERE `location` LIKE '%3F%'
                                                                            ORDER BY `seid`";
                                                                $result = mysqli_query($conn, $query);
                                                                $rownum = mysqli_num_rows($result);
                                                                if($rownum > 0) {
                                                                    for($i = 0; $i < $rownum / 3; $i++) {
                                                                        echo "<tr>";
                                                                        for($j = 0; $j < 3; $j++) {
                                                                            if(!$row = mysqli_fetch_array($result)){
                                                                                break;
                                                                            }
                                                                            $socket_color = $row['is_socket'] ? 'warning' : 'secondary';
                                                                            echo "<td><button type='button' class='btn btn-outline-{$socket_color} btn-sm seat-btn' data-seat='{$row['location']}'>{$row['location']}</button></td>";
                                                                        }
                                                                        echo"</tr>";
                                                                    }
                                                                }
                                                            ?>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- 確認按鈕 -->
                                        <div class="modal-footer">
                                            <button type="button" id="confirmSeat" class="btn btn-success" data-bs-dismiss="modal">確認</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <br>
                        <li>
                            <!-- 選擇時段 -->
                            <div class="d-flex align-items-center gap-2">
                                <span>選擇時段:&nbsp;&nbsp;</span>
                                <input type="text" id="selected-ts" class="fw-bold text-dark form-control w-50 text-center" readonly required placeholder="未選取" name="selected_ts">
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tsModal"> 選取 </button>
                            </div>
                            <!-- 時段Modal -->
                            <div class="modal fade" id="tsModal" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <!-- modal 標題 -->
                                        <div class="modal-header">
                                            <h5 class="modal-title">選擇時段</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <!-- modal 內容 -->
                                        <div class="modal-body">
                                            <div class="table-responsive">
                                                <table class="table table-bordered text-center align-middle" id="ts-table">
                                                     <?php
                                                        $query = "SELECT `label`
                                                                    FROM timeslot
                                                                    ORDER BY `tsid`";
                                                        $result = mysqli_query($conn, $query);
                                                        $rownum = mysqli_num_rows($result);
                                                        if($rownum > 0) {
                                                            for($i = 0; $i < $rownum / 3; $i++) {
                                                                echo "<tr>";
                                                                for($j = 0; $j < 3; $j++) {
                                                                    $row = mysqli_fetch_array($result);
                                                                    echo "<td><button type='button' class='btn btn-outline-success btn-sm ts-btn' data-ts='{$row['label']}'>{$row['label']}</button></td>";
                                                                }
                                                                echo"</tr>";
                                                            }
                                                        }
                                                    ?>
                                                    </table>
                                            </div>
                                        </div>
                                        <!-- 確認按鈕 -->
                                        <div class="modal-footer">
                                            <button type="button" id="confirmTS" class="btn btn-success" data-bs-dismiss="modal">確認</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ol>
                    <div class="d-flex justify-content-center">
                        <button type='submit' class="btn btn-success"> 送出 </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
<script>
    function updateTimeslots() {
        const selectedDate = document.getElementById('selected-date').value;
        const selectedSeat = document.querySelector('.seat-btn.active')?.dataset.seat;
        if (!selectedDate || !selectedSeat) return;

        fetch(`get_disabled_timeslots.php?date=${selectedDate}&seat=${selectedSeat}`)
            .then(res => res.json())
            .then(disabledTimeslots => {
                document.querySelectorAll('.ts-btn').forEach(btn => {
                    btn.disabled = false;
                    btn.classList.remove('btn-secondary');
                    btn.classList.add('btn-outline-success');
                });

                disabledTimeslots.forEach(ts => {
                    const btn = document.querySelector(`.ts-btn[data-ts="${ts}"]`);
                    if (btn) {
                        btn.disabled = true;
                        btn.classList.remove('btn-outline-success');
                        btn.classList.add('btn-secondary');
                    }
                });
            });
    }

    // 綁定事件：當日期或座位改變時，更新可選時段
    document.getElementById('selected-date').addEventListener('change', updateTimeslots);
    document.querySelectorAll('.seat-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.seat-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            updateTimeslots();
        });
    });
</script>
<script>
    let selectedSeat = '';

    document.querySelectorAll('.seat-btn').forEach(btn => {
        btn.addEventListener('click', function () {
        // 清除其他選擇
        document.querySelectorAll('.seat-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        selectedSeat = this.getAttribute('data-seat');
        });
    });

    document.getElementById('confirmSeat').addEventListener('click', function () {
        if (selectedSeat) {
        document.getElementById('selected-seat').value = selectedSeat;
        // document.getElementById('selected-seat-input').value = selectedSeat;
        }
    });
</script>
<script>
    let selectedTS = '';

    document.querySelectorAll('.ts-btn').forEach(btn => {
        btn.addEventListener('click', function () {
        // 清除其他選擇
        document.querySelectorAll('.ts-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        selectedTS = this.getAttribute('data-ts');
        });
    });

    document.getElementById('confirmTS').addEventListener('click', function () {
        if (selectedTS) {
        document.getElementById('selected-ts').value = selectedTS;
        }
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</html>