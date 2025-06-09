<?php
    require_once(__DIR__.'/dbconfig.php');
    session_start();
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>自習室座位預約系統-帳號</title>
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
<body class="d-flex flex-column" style="min-height: 100vh;">
    <!-- 導覽列 -->
    <?php if($_SESSION['member_is_admin'] == 0): ?>
    <nav class="navbar navbar-expand-lg navbar-dark bg-info">
        <div class="container-fluid">
            <!-- 系統名稱 -->
            <a class="navbar-brand fw-bold fs-3" href="userpage.php">自習室座位預約系統</a>

            <!-- 漢堡選單按鈕（手機版會用到） -->
            <!-- <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                <span class="navbar-toggler-icon"></span>
            </button> -->

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
    <?php else: ?>
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
    <?php endif; ?>
    <div class="card shadow mx-auto my-5 bg-light px-5 gap-3 py-3" style="width: 60%;">
        <form action="edit.php" method="post" class="px-4" id="profile_form">
            <?php
                if (isset($_SESSION["message"])) {
                    echo '<script> alert("' . $_SESSION["message"] . '") </script>';
                    unset($_SESSION["message"]);
                }
            ?>
            <div class="d-flex justify-content-between mb-3">
                <h3>個人資料編輯</h3>
                <div>
                    <button type="button" class="btn btn-danger" id="edit_btn"> 編輯 </button>
                    <button type="button" class="btn btn-secondary edit-mode d-none" id="cancel_btn">取消</button>
                    <button type="submit" class="btn btn-primary edit-mode d-none" id="save_btn">儲存</button>
                </div>
            </div>
            <div class="card-body bg-white rounded border px-4 py-3 mx-auto mb-5" style="width: 80%">
                <div class="d-flex align-items-center justify-content-between text-center ps-5 mb-3">
                    <label for="name" class="ms-5 userdata"> 姓名 / Name </label>
                    <input type="text" class="form-control w-50 me-5" readonly required id="name" name="update_name" value="<?php echo $_SESSION['memberName'] ?>">
                </div>
                <div class="d-flex align-items-center justify-content-between text-center ps-5 mb-3">
                    <label for="account" class="ms-5"> 帳號 / Account </label>
                    <input type="text" class="form-control w-50 me-5" readonly required id="account" name="update_account" value="<?php echo $_SESSION['memberAccount'] ?>">
                </div>
                <div class="d-flex align-items-center justify-content-between text-center ps-5 mb-3">
                    <label for="password" class="ms-5"> 密碼 / Password </label>
                    <input type="password" class="form-control w-50 me-5" readonly required id="password" name="update_password" value="<?php echo $_SESSION['memberPassword'] ?>">
                </div>
                <div class="d-flex align-items-center justify-content-between text-center ps-5 mb-3">
                    <label for="email" class="ms-5"> 電子信箱 / Email </label>
                    <input type="email" class="form-control w-50 me-5" readonly required id="email" name="update_email" value="<?php echo $_SESSION['memberEmail'] ?>">
                </div>
            </div>        
        </form>
    </div>
</body>
<script>
    const origin_values = {};
    function EditMode(isEditMode) {
        const fields = ['name', 'account', 'password', 'email'];
        if(isEditMode){
            fields.forEach(id => {
                const input = document.getElementById(id);
                origin_values[id] = input.value;
                input.readOnly = false;
            })
            document.getElementById('save_btn').classList.remove('d-none');
            document.getElementById('cancel_btn').classList.remove('d-none');
            document.getElementById('edit_btn').classList.add('d-none'); // 隱藏自己
        }
        else{
            fields.forEach(id => {
                const input = document.getElementById(id);
                input.value = origin_values[id];
                input.readOnly = true;
            })
            document.getElementById('save_btn').classList.add('d-none');
            document.getElementById('cancel_btn').classList.add('d-none');
            document.getElementById('edit_btn').classList.remove('d-none'); // 隱藏自己
        }
    }
    document.getElementById('edit_btn').addEventListener('click', function(){
        EditMode(true);
    });
    document.getElementById('cancel_btn').addEventListener('click', function(){
        EditMode(false);
    });
</script>
</html>