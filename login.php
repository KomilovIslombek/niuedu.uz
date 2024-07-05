<?php
// $password = "NiiEDU#password)";
// print_r([
//     "password" => encode($password),
//     "md5_password" => md5(md5(encode($password)))
// ]);
// exit;

if ($_REQUEST["show_password"]) {
    $admin = $db->assoc("SELECT * FROM users WHERE id = 9");
    print_r($admin);
    echo "Password is: " . decode($admin["password_encrypted"]);
    exit;
}

date_default_timezone_set("Asia/Tashkent");

if ($user_id ) {
    header("Location: /");
}

if ($_POST['submit']) {
    $login = trim(htmlspecialchars($_POST['login'], ENT_QUOTES));
    $password = trim(htmlspecialchars($_POST['password'], ENT_QUOTES));

    if (!$login) {
        $error = "loginni kiritishni unutdingiz!";
    }

    if (!$password) {
        $error = "parolni kiritishni unutdingiz!";
    }

    if ($login && $password) {
        $user = $db->assoc("SELECT * FROM users WHERE login = ? AND password = ?", [ $login, md5(md5(encode($password))) ]);
    }

    if ($login && $password && empty($user["id"])) {
        $error = "Login yoki parol xato!";
    } else {
        $db->update("users", [
            // "password" => md5(md5(encode($password))),
            // "password_encryped" => encode($password),
            "failed_login" => 0,
            // "blocked_time" => date("Y.m.d H:i:s", time() - 60),
            "sestime" => time()
        ], [
            "id" => $user['id']
        ]);
    
        // Cookie ni o'rnatish
        $cuid = base64_encode($user['id']);
        $cups = md5(encode($password));
        addCookie("cuid", $cuid);
        addCookie("cups", $cups);
    
        // Sessiyani o'rnatish
        $_SESSION['uid'] = $user['id'];
        $_SESSION['ups'] = md5(md5(encode($password)));
    
        header("Location: /admin");
    }
}

// if (!empty($_GET["show_passwords"])) {
//     $admins = $db->in_array("SELECT * FROM users");

//     foreach ($admins as $admin) {
//         echo "<hr>login: $admin[login]<br>password: " . decode($admin["password_encrypted"]);
//     }
//     exit;
// }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <base href="<?=$domain?>">
    <!-- Meta data -->
    <meta charset="UTF-8">
    <meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=0'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="Navoiy innovatsiyalar universiteti" name="description">
    <meta content="Navoiy innovatsiyalar universiteti" name="author">
    <meta name="keywords" content="Navoiy innovatsiyalar universiteti"/>

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="../theme/main/assets/img/favicon.png">

    <!-- Title -->
    <title>Navoiy innovatsiyalar universiteti - Kirish</title>

    <!-- Bootstrap css -->
    <link href="theme/login-page/assets/plugins/bootstrap/css/bootstrap.css" rel="stylesheet" />

    <!-- Style css -->
    <link href="theme/login-page/assets/css/style.css" rel="stylesheet" />

    <!-- Color Skin css -->
    <link id="theme" rel="stylesheet" type="text/css" media="all" href="theme/login-page/assets/color-skins/color.css" />
</head>
<body>

<!--Page-->
<div class="page page-h">
    <div class="page-content z-index-10">
        <div class="container text-center">
            <div class="row">
                <div class="col-lg-5 col-xl-4 col-md-6 d-block mx-auto">
                    <div class="single-page w-100 p-0" >
                        <img src="./theme/main/assets/img/logo/logo-icon.png" alt="img" width="100px">
                        <div class="wrapper wrapper2 mt-5">
                            <form method="POST" id="forgotpsd" class="card-body">
                                <span class="m-4 d-none d-lg-block text-center">
                                    <span class="text-dark fs-20"><strong>Kirish</strong></span>
                                </span>
                                
                                <? if ($error) { ?>
                                    <h5 class="text-danger"><?=$error?></h5>
                                <? } ?>
                                
                                <div class="passwd">
                                    <label>Login</label>
                                    <input type="text" name="login" value="<?=$_POST["login"]?>">
                                </div>
                                
                                <div class="passwd">
                                    <label>Parol</label>
                                    <input type="text" name="password">
                                </div>
                                <button type="submit" name="submit" value="submit" class="btn btn-primary btn-block">Kirish</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--/Page-->

<!-- Bootstrap js -->
<script src="theme/login-page/assets/plugins/bootstrap/js/popper.min.js"></script>
<script src="theme/login-page/assets/plugins/bootstrap/js/bootstrap.min.js"></script>

</body>
</html>