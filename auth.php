<?php
$is_config = true;
if (empty($load_defined)) include 'load.php';

date_default_timezone_set("Asia/Tashkent");

// tekshiruv
if (!$user_id || $user_id == 0) {
} else {
    header("Location: /$url2[0]/profile/my");
}

$error = false;
if ($_POST["submit"]) {
    $login = trim($_POST["login"]);
    $pass = trim($_POST["pass"]);

    $login = str_replace(" ", "", $login);
    $pass = str_replace(" ", "", $pass);

    if (!$login) {
        $login_error = "IDni kiritishni unutdingiz!";
    }

    if (!$pass) {
        $pass_error = "Passport seriyasi hamda raqamini kiritishni unutdingiz!";
    }

    if ($login && $pass) {
        $user = $db->assoc("SELECT * FROM users WHERE login = ? AND password = ?", [
            $login,
            md5(md5(encode($pass)))
        ]);

        if (empty($user["id"])) {
            $student = $db->assoc("SELECT * FROM requests WHERE code = ?", [ $pass ]);
            if (empty($student["code"])) {
                $student = $db1->assoc("SELECT * FROM requests WHERE code = ?", [ $pass ]);
            }

            if (empty($student["code"])) {
                $error = "bunday ID raqamli talaba topilmadi";
            } else if (mb_strtoupper(str_replace(" ", "", $login)) != mb_strtoupper(str_replace(" ", "", $student["passport_serial_number"]))) {
                $error = "passport seriya xato";
            } else if (!empty($student["code"])) {
                $insert_user_id = $db->insert("users", [
                    "first_name" => $student["first_name"],
                    "last_name" => $student["last_name"],
                    "login" => mb_strtoupper(str_replace(" ", "", $login)),
                    "password" => md5(md5(encode($pass))),
                    "password_encrypted" => encode($pass),
                    "phone" => $student["phone_1"],
                    "code" => null,
                    "password_sended_time" => null,
                    "datereg" => time(),
                    "lastdate" => time(),
                    "ip" => $env->getIp(),
                    "ip_via_proxy" => $env->getIpViaProxy(),
                    "browser" => $env->getUserAgent(),
                    "sestime" => time(),
                    "request_id" => $student["code"]
                ]);

                if ($insert_user_id > 0) {
                    $user = $db->assoc("SELECT * FROM users WHERE id = ?", [ $insert_user_id ]);
                } else {
                    $user = [];
                }
            }
        }
    }

    if ($login && $pass && empty($user["id"])) {
        if (!$error) $error = "id yoki passport seriya xato!";
    } else {
        $db->update("users", [
            "failed_login" => 0,
            "sestime" => time()
        ], [
            "id" => $user['id']
        ]);

        $session = md5($env->getIp() . $env->getIpViaProxy() . $env->getUserAgent());
        $sessionArr = $db->assoc("SELECT * FROM cms_sessions WHERE session_id = ?", [ $session ]);

        if (!empty($sessionArr["session_id"])) {
            $sql_arr = [];
            $movings = ++$user["movings"];

            if ($user["sestime"] < (time() - 300)) {
                $movings = 1;
                $sql_arr["sestime"] = time();
            }

            if ($user["place"] != $headmod) {
                $sql_arr["place"] = "/id";
            }

            $sql_arr["movings"] = $movings;
            $sql_arr["lastdate"] = time();

            $db->update("cms_sessions", $sql_arr, [
                "session_id" => $session
            ]);
        } else {
            $db->insert("cms_sessions", [
                "session_id" => $session,
                "ip" => $env->getIp(),
                "ip_via_proxy" => $env->getIpViaProxy(),
                "browser" => $env->getUserAgent(),
                "lastdate" => time(),
                "sestime" => time(),
                "place" => "/id"
            ]);
        }

        // exit($session);
    
        // Cookie ni o'rnatish
        $cuid = base64_encode($user['id']);
        $cups = md5(encode($pass));
        addCookie("cuid", $cuid);
        addCookie("cups", $cups);
    
        header("Location: /$url2[0]/profile/my");
    }
}

$page_name = "Kirish";
?>
<!DOCTYPE html>
<html lang="uz">
<head>
    <base href="<?=$domain?>">
    <!-- Meta data -->
    <meta charset="UTF-8">
    <meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=0'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="NAVOIY INNOVATSIYALAR UNIVERSITETI" name="description">
    <meta content="NAVOIY INNOVATSIYALAR UNIVERSITETI" name="author">
    <meta name="keywords" content="NAVOIY INNOVATSIYALAR UNIVERSITETI"/>

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="../theme/main/assets/img/favicon.png">

    <!-- Title -->
    <title>NAVOIY INNOVATSIYALAR UNIVERSITETI - <?=$page_name?></title>

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
                        <img src="images/logo/niu_logo.png" alt="img" width="200px">
                        <div class="wrapper wrapper2 mt-5">
                            <form method="POST" id="forgotpsd" class="card-body">
                                <span class="m-4 d-lg-block text-center">
                                    <span class="text-dark fs-20"><strong>Kirish</strong></span>
                                </span>
                                
                                <? if ($error) { ?>
                                    <h5 class="text-danger"><?=$error?></h5>
                                <? } ?>
                                
                                <div class="was-validated">
                                    <input 
                                        type="text"
                                        name="pass"
                                        value="<?=($_POST['pass'] ? $_POST['pass'] : "")?>"
                                        id="homeSignupFirstName"
                                        placeholder="Talaba ID"
                                        pattern=".{3,50}"
                                        minlength="3"
                                        maxlength="50"
                                        class="form-control <?=($login_error ? 'is-invalid' : '')?>"
                                        required="required"
                                    />

                                    <div class="invalid-feedback">
                                        <?=($login_error ? $login_error : "iltimos ID raqamingizni kiriting")?>
                                    </div>
                                </div>

                                <div class="was-validated">
                                    <input 
                                        type="text"
                                        name="login"
                                        value="<?=($_POST['login'] ? $_POST['login'] : "")?>"
                                        id="homeSignupLastName"
                                        placeholder="AA1234567"
                                        pattern=".{3,50}"
                                        minlength="3"
                                        maxlength="50"
                                        class="form-control <?=($pass_error ? 'is-invalid' : '')?>"
                                        required="required"
                                    />

                                    <div class="invalid-feedback">
                                        <?=($pass_error ? $pass_error : "iltimos Passport seriya hamda raqamingizni kiriting")?>
                                    </div>
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

<!-- Additional style -->
<style>
    .btn-primary {
        background-color: #198A29;
    }

    .change-number {
        height: 40px;
        width: 100%;
        margin-bottom: 6px;
        text-align: left;
    }

    .reset-form, #resend-password {
        margin-top: 11px;
        display: inline-block;
    }

    .change-number #resend-password {
        color: #198A29;
        float: right;
    }

    #countdown {
        display: inline-block;
        float: right;
        position: relative;
        /* margin: auto; */
        /* margin-top: 100px; */
        height: 40px;
        width: 40px;
        text-align: center;
        vertical-align: middle;
    }

    #countdown-number {
        color: black;
        display: inline-block;
        line-height: 40px;
        vertical-align: middle;
        font-size: 10px;
    }

    #countdown svg {
        position: absolute;
        top: 0;
        right: 0;
        width: 40px;
        height: 40px;
        transform: rotateY(-180deg) rotateZ(-90deg);
    }

    #countdown  circle {
        stroke-dasharray: 113px;
        stroke-dashoffset: 0px;
        stroke-linecap: round;
        stroke-width: 2px;
        stroke: black;
        fill: none;
        animation: countdown <?=($countdown_password_seconds_blocked > 0 ? $countdown_password_seconds_blocked : ($countdown_password_seconds > 0 ? $countdown_password_seconds : '10') )?>s linear infinite forwards;
    }

    @keyframes countdown {
        from {
            stroke-dashoffset: 0px;
        }
        to {
            stroke-dashoffset: 113px;
        }
    }

    .invalid-feedback {
        text-align-last: left;
    }
</style>
<!-- end Additional style -->

<!-- Bootstrap js -->
<script src="theme/grandmaktab/assets/plugins/bootstrap/js/popper.min.js"></script>
<script src="theme/grandmaktab/assets/plugins/bootstrap/js/bootstrap.min.js"></script>

<!-- Jquery -->
<script src="theme/main/assets/js/vendor/jquery-3.5.1.min.js"></script>

</body>
</html>