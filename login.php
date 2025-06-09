<?php
require_once(__DIR__ . '/dbconfig.php');


session_start();
if(isset($_SESSION["loggedin"])){
    // 登入驗證通過，前往主頁
    $_SESSION["loggedin"] = false;
    header("Location: ./login.php");
    exit;
}

if($_SERVER["REQUEST_METHOD"] == "POST"){
    // 取得 POST 過來的資料 / Get the data from POST
    $account = $_POST["account"];
    $password = $_POST["password"];

    // 以帳號進資料庫查詢 / Select the data from database using username
    $sql = "SELECT `usid`, `name`, `account`, `password` , `email`, `is_admin` FROM `user` WHERE `account`=?";
    // 使用預處理語句 / Use prepared statement
    
    $stmt = mysqli_prepare($conn, $sql);
    // 使用 mysqli_prepare() 函數準備 SQL 語句 / Prepare SQL statement using mysqli_prepare()

    mysqli_stmt_bind_param($stmt, "s", $account);
    // 使用 mysqli_stmt_bind_param() 函數綁定參數 (prepare) / Bind parameters using mysqli_stmt_bind_param()

    mysqli_stmt_execute($stmt);
    // 使用 mysqli_stmt_execute() 函數執行預處理語句 / Execute the prepared statement using mysqli_stmt_execute()

    mysqli_stmt_bind_result($stmt, $result_usid, $result_name, $result_account, $result_password, $result_email, $result_is_admin);
    // 使用 mysqli_stmt_bind_result() 函數綁定結果變數 / Bind result variables using mysqli_stmt_bind_result()

    if(mysqli_stmt_fetch($stmt)){
    // 使用 mysqli_stmt_fetch() 函數獲取結果 / Fetch the result using mysqli_stmt_fetch()

    
    
        // 驗證密碼 / Verify password
        if($password == $result_password){
            // 密碼通過驗證 / Password verification passed
            session_start();
            // 把資料存入Session / Put the data into session
            $_SESSION["loggedin"] = true;
            $_SESSION['memberID'] = $result_usid;
            $_SESSION["memberName"] = $result_name;
            $_SESSION["memberAccount"] = $result_account;
            $_SESSION["memberPassword"] = $result_password;
            $_SESSION["memberEmail"] = $result_email;
            $_SESSION["member_is_admin"] = $result_is_admin;

            if($result_is_admin == 0){
                // 轉跳到user頁面 / Redirect to member page
                header("location: ./userpage.php"); 
                exit;
            }
            else{
                // 轉跳到admin頁面 / Redirect to admin page
                header("location: ./adminpage.php");
                exit;
            }
        }
        else{
            // 密碼驗證失敗 / Password verification failed
            echo '<script>alert("密碼錯誤.\nIncorrect Password.");</script>';

        }
    }
    else{
        // 帳號不存在 / Account does not exist
        echo '<script>alert("帳號不存在.\nIncorrect Account .");</script>';
    }

    mysqli_stmt_close($stmt);
    // 使用 mysqli_stmt_close() 函數關閉預處理語句 / Close the prepared statement using mysqli_stmt_close()
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
    <title>自習室座位預約系統-登入</title>
</head>

<body class="text-center d-flex justify-content-center align-items-center vh-100" style="min-height: 100vh;">
    <div class="card shadow-sm" style="width: 24rem;">
        <div class="card-body form-control">
            <h2 class="h2 mb-2 fw-bold">自習室座位預約系統</h2>
            <!-- <h3 class="h3 mb-4 fw-bold">登入範例 / Login Example</h3> -->
            <hr>    <!-- 橫線 -->
            <form action="login.php" method="post"> <!-- POST -->
                <?php
                    if (isset($_SESSION["message"])) {
                        echo '<script> alert("' . $_SESSION["message"] . '") </script>';
                        unset($_SESSION["message"]);
                    }
                ?>
                <div class="mb-3">
                    <label for="uid" class="form-label">帳號 / Account</label>
                    <input type="text" class="form-control" id="account" placeholder="Account" name="account" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">密碼 / Password</label>
                    <input type="password" class="form-control" id="password" placeholder="Password" name="password" required>
                </div>
                <div class="d-grid">
                    <button class="w-100 btn btn-lg btn-primary fw-bold mb-3" type="submit">登入 / Login</button>
                    <a class="w-100 btn btn-lg btn-success fw-bold" href="register.php"> 註冊 / Register</a>
                </div>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
</body>

</html>