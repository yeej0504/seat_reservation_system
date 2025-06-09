<?php
    require_once(__DIR__.'/dbconfig.php');
    session_start();

    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        $name = $_POST['update_name'];
        $account = $_POST['update_account'];
        $password = $_POST['update_password'];
        $email = $_POST['update_email'];

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['message'] = "請輸入有效的 Email 格式。";
            header('Location: account.php');
            exit();
        }

        $query = "SELECT * FROM user WHERE `account` = '$account'";
        $result = mysqli_query($conn, $query);
        // 檢查資料庫連接是否成功
        if (mysqli_num_rows($result) > 0 && mysqli_fetch_assoc($result)['usid'] != $_SESSION['memberID']) {
            $_SESSION['message'] = "帳號已經被使用，請更換其他帳號。";
            header('Location: account.php');
            exit();
        }
        else {
            $query = "UPDATE user SET `account` = '$account', `password` = '$password', `name` = '$name', `email` = '$email' WHERE `usid` = '{$_SESSION['memberID']}'";
            $result = mysqli_query($conn, $query);
            if($result){
                $_SESSION['message'] = "資料更新成功";
                $_SESSION['memberName'] = $name;
                $_SESSION['memberAccount'] = $account;
                $_SESSION['memberPassword'] = $password;
                $_SESSION['memberEmail'] = $email;
            }
            else {
                $_SESSION['message'] = "資料更新失敗";
            }
        }
        // 重新導向避免重新整理重送
        header('Location: account.php');
        exit();
    }
?>