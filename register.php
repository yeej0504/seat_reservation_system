<?php
require_once(__DIR__ . '/dbconfig.php');
session_start();

if($_SERVER["REQUEST_METHOD"] == "POST"){
    // 取得 POST 過來的資料 / Get the data from POST
    $name = $_POST["name"];
    $account = $_POST["account"];
    $password = $_POST["password"];
    $email = $_POST["email"];

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
    // 電子郵件地址格式正確
    } else {
        // 電子郵件地址格式不正確
        $_SESSION["message"] = "無效的電子郵件地址。";
        header("Location: register.php");
        exit();
    }

    $qry = "SELECT * FROM user WHERE `account` = '$account'";
    $result = mysqli_query($conn, $qry);
    $rownum = mysqli_num_rows($result);
    if ($rownum > 0) {
        $_SESSION["message"] = "該帳號已註冊!";
        header("Location: register.php");
        exit();
    }
    else {
        $query2 = "INSERT INTO user(name, account, password, email, is_admin) VALUES ('$name', '$account', '$password', '$email', '0')";
        $result2 = mysqli_query($conn, $query2);
        if ($result2){
            $_SESSION["message"] = "註冊成功!";
            header("Location: login.php");
            exit();
        }
        else {
            $_SESSION["message"] = "註冊失敗";
            header("Location: register.php");
            exit();
        }
    }
}
// Close connection
mysqli_close($conn);
// 使用 mysqli_close() 函數關閉資料庫連線 / Close the database connection using mysqli_close()
?>

<!doctype html>
<html lang="zh-hant">

<head>
    <meta charset="utf-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <!-- <style>
        body {
            background-color: #f8f9fa;
        }
        html, body {
            /* height: 100%; */
            margin: 0;
            overflow: auto;
        }

        .container {
            /* height: 100%; */
            display: flex;
            justify-content: center;
            align-items: center;
            padding-bottom: 150px; 
        }
    </style> -->
    <title>自習室座位預約系統-登入</title>
</head>

<body class="text-center d-flex justify-content-center align-items-center"  style="min-height: 100vh;">
    <div class="card shadow-sm" style="width: 24rem;">
        <div class="card-body">
            <h2 class="h2 mb-2 fw-bold">自習室座位預約系統</h2>
            <!-- <h3 class="h3 mb-4 fw-bold">登入範例 / Login Example</h3> -->
            <hr>    <!-- 橫線 -->
            <form action="register.php" method="post"> <!-- POST -->
                <?php
                    if (isset($_SESSION["message"])) {
                        echo '<script> alert("' . $_SESSION["message"] . '") </script>';
                        unset($_SESSION["message"]);
                    }
                ?>
                <div class="mb-3">
                    <label for="name" class="form-label">姓名 / Name</label>
                    <input type="text" class="form-control" id="name" placeholder="Name" name="name" required>
                </div>
                <div class="mb-3">
                    <label for="account" class="form-label">帳號 / Account</label>
                    <input type="text" class="form-control" id="account" placeholder="Account" name="account" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">密碼 / Password</label>
                    <input type="password" class="form-control" id="password" placeholder="Password" name="password" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">電子信箱 / Email</label>
                    <input type="email" class="form-control" id="email" placeholder="Email" name="email" required>
                </div>
                <div class="d-grid">                    
                    <button class="w-100 btn btn-lg btn-success fw-bold mb-3" type="submit">註冊 / Register</button>
                    <a class="w-100 btn btn-lg btn-secondary fw-bold" href="login.php">返回 / Back</a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
</body>

</html>