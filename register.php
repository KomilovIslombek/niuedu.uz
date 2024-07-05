<?php
exit(http_response_code(404));
exit;
date_default_timezone_set("Asia/Tashkent");

// if ($domain != "https://test.allcomment.uz") exit(http_response_code(404));

if ($user_id ) {
    header("Location: /");
}

if ($_POST['submit']) {
    $first_name = trim(htmlspecialchars($_POST['first_name'], ENT_QUOTES));
    $last_name = trim(htmlspecialchars($_POST['last_name'], ENT_QUOTES));

    if (!$first_name) {
        $error = "first_name not found";
    }

    if (!$last_name) {
        $error = "last_name not found";
    }

    $password = encode("grandadmin2022#");

    $inserted_user_id = $db->insert("users", [
        "first_name" => $first_name,
        "last_name" => $last_name,
        "password" => md5(md5(encode($password))),
        "password_encrypted" => encode($password),
        "datereg" => time(),
    ]);

    if ($inserted_user_id > 0) {
        $user = $db->assoc("SELECT * FROM users WHERE id = ?", [ $inserted_user_id ]);
    
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
    
        header("Location: /$url[0]");
    } else {
        exit("ERROR INSERT USER");
    }

}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <base href="<?=$domain?>">
    <!-- Meta data -->
    <meta charset="UTF-8">
    <meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=0'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content=" Almaz" name="description">
    <meta content="Almaz" name="author">
    <meta name="keywords" content="Almaz"/>

    <!-- Favicon -->
    <link rel="icon" href="theme/main/assets/images/brand/favicon.ico" type="image/x-icon"/>
    <link rel="shortcut icon" type="image/x-icon" href="theme/main/assets/images/brand/favicon.ico" />

    <!-- Title -->
    <title>Almaz - Register</title>

    <!-- Bootstrap css -->
    <link href="theme/main/assets/plugins/bootstrap/css/bootstrap.css" rel="stylesheet" />

    <!-- Style css -->
    <link href="theme/main/assets/css/style.css" rel="stylesheet" />

    <!-- Color Skin css -->
    <link id="theme" rel="stylesheet" type="text/css" media="all" href="theme/main/assets/color-skins/color.css" />
</head>
<body>

<!--Page-->
<div class="page page-h">
    <div class="page-content z-index-10">
        <div class="container text-center">
            <div class="row">
                <div class="col-lg-5 col-xl-4 col-md-6 d-block mx-auto">
                    <div class="single-page w-100 p-0" >
                        <img src="./theme/rubust/app-assets/images/logo/robust-logo-blue-big.png" alt="img" width="200px">
                        <div class="wrapper wrapper2 mt-5">
                            <form method="POST" id="forgotpsd" class="card-body">
                                <span class="m-4 d-none d-lg-block text-center">
                                    <span class="text-dark fs-20"><strong>Register</strong></span>
                                </span>
                                <div class="passwd">
                                    <label>Ism</label>
                                    <input type="text" name="first_name" value="<?=$_POST["first_name"]?>">
                                </div>
                                <div class="passwd">
                                    <label>Familiya</label>
                                    <input type="text" name="last_name">
                                </div>
                                <button type="submit" name="submit" value="submit" class="btn btn-primary btn-block">Submit</button>
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
<script src="theme/main/assets/plugins/bootstrap/js/popper.min.js"></script>
<script src="theme/main/assets/plugins/bootstrap/js/bootstrap.min.js"></script>

</body>
</html>