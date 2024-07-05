<?php

// tekshiruv
if (!$user_id || $user_id == 0) {
    header('Location:/login');
    exit;
} else if ($systemUser->admin != 1){
    header('Content-Type: text/html');
    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
    </head>
    <body>
        siz admin lavozimida emassiz!<br>admin lavozimidagi akkauntga kirish uchun <a href="/exit">akkauntdan chiqish</a> tugmasini bosing.
    </body>
    </html>';
    exit;
}



include('filter.php');

if ($_REQUEST['type'] == "add_user"){
    $login = !empty($_REQUEST['login']) ? $_REQUEST['login'] : null;
    if (!$login) {echo"error [login]";return;}

    $first_name = !empty($_REQUEST['first_name']) ? $_REQUEST['first_name'] : null;
    if (!$first_name) {echo"error [first_name]";return;}

    $last_name = !empty($_REQUEST['last_name']) ? $_REQUEST['last_name'] : null;
    if (!$last_name) {echo"error [last_name]";return;}

    $password = !empty($_REQUEST['password']) ? $_REQUEST['password'] : null;

    $admin = !empty($_REQUEST['admin']) ? $_REQUEST['admin'] : 0;

    $birth_date = !empty($_REQUEST['birth_date']) ? $_REQUEST['birth_date'] : null;
    $address = !empty($_REQUEST['address']) ? $_REQUEST['address'] : null;
    $country_id = !empty($_REQUEST['country_id']) ? $_REQUEST['country_id'] : null;
    $region_id = !empty($_REQUEST['region_id']) ? $_REQUEST['region_id'] : null;
    $bio = !empty($_REQUEST['bio']) ? $_REQUEST['bio'] : null;
    $address = !empty($_REQUEST['address']) ? $_REQUEST['address'] : null;
    $permissions = !empty($_REQUEST['permissions']) ? $_REQUEST['permissions'] : null;

    $db->insert("users", [
        "first_name" => $first_name,
        "last_name" => $last_name,
        "password" => md5(md5(encode($password))),
        "password_encrypted" => encode($password),
        "login" => $login,
        "birth_date" => $birth_date,
        "country_id" => $country_id,
        "region_id" => $region_id,
        "bio" => $bio,
        "address" => $address,
        "datereg" => time(),
        "permissions" => ($permissions ? json_encode($permissions) : NULL),
        "admin" => 1,
        "role" => $_REQUEST["role"],
    ]);
  
    header('Location: admins_list.php?page=1');
}

include('head.php');
?>

<div class="app-content content container-fluid">
    <div class="content-wrapper">
        <div class="content-body">
            <div class="row">

                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title" id="basic-layout-colored-form-control">Yangi admin qo'shish</h4>
                            <a class="heading-elements-toggle"><i class="icon-ellipsis font-medium-3"></i></a>
                            <div class="heading-elements">
                                <ul class="list-inline mb-0">
                                    <li><a data-action="collapse"><i class="icon-minus4"></i></a></li>
                                    <li><a data-action="reload"><i class="icon-reload"></i></a></li>
                                    <li><a data-action="expand"><i class="icon-expand2"></i></a></li>
                                    <li><a data-action="close"><i class="icon-cross2"></i></a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="card-body collapse in">
                            <div class="card-block">
                                <form action="" method="POST" class="form">
                                    <input type="hidden" name="type" value="add_user">

                                    <div class="form-group">
                                        <label for="first_name">ism</label>
                                        <input type="text" name="first_name" class="form-control border-primary" placeholder="ism" id="first_name">
                                    </div>

                                    <div class="form-group">
                                        <label for="last_name">familiya</label>
                                        <input type="text" name="last_name" class="form-control border-primary" placeholder="familiya" id="last_name">
                                    </div>

                                    <div class="form-group">
                                        <label for="login">login</label>
                                        <input type="text" name="login" class="form-control border-primary" placeholder="telefon" id="login">
                                    </div>

                                    <div class="form-group">
                                        <label for="password">parol</label>
                                        <input type="text" name="password" class="form-control border-primary" placeholder="parol" id="password">
                                    </div>

                                    <div class="form-group">
                                        <label for="birth_date">Tug'ilgan sana</label>
                                        <input type="date" name="birth_date" class="form-control border-primary" placeholder="Tug'ilgan sana" id="birth_date">
                                    </div>

                                    <div class="form-group">
                                        <label for="country_id">Davlat</label>

                                        <select name="country_id" class="form-control" id="country_id">
                                            <option value="1" selected="">O'zbekiston</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="region_id">Viloyat</label>

                                        <select name="region_id" class="form-control" id="region_id">
                                            <option value="1">Andijon viloyati</option>
                                            <option value="2">Namangan viloyati</option>
                                            <option value="3">Farg'ona viloyati</option>
                                            <option value="4">Toshkent viloyati</option>
                                            <option value="5">Buxoro viloyati</option>
                                            <option value="6">Jizzax viloyati</option>
                                            <option value="7">Xorazm viloyati</option>
                                            <option value="8">Qashqadaryo viloyati</option>
                                            <option value="10">Qoraqalpogʻiston</option>
                                            <option value="11">Samarqand viloyati</option>
                                            <option value="12">Sirdaryo viloyati</option>
                                            <option value="13">Surxondaryo viloyati</option>
                                            <option value="14">Toshkent shahri</option>
                                        </select>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="bio">Admin haqida</label>
                                                <input type="text" name="bio" class="form-control border-primary" placeholder="Admin haqida" id="bio">
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="address">Manzil</label>
                                                <input type="text" name="address" class="form-control border-primary" placeholder="Manzil" id="address">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="role">Lavozimi</label>
                                        <input type="text" name="role" class="form-control border-primary" placeholder="Lavozimi" id="role">
                                    </div>

                                    <table class="table table-hover table-bordered table-responsive-sm">
                                        <tbody>
                                            <tr class="bg-dark text-white">
                                                <th><label class="form-check-label" for="checkAll">Hamasini belgilash</label></th>
                                                <td><input type="checkbox" class="form-check-input" id="checkAll"></td>
                                            </tr>
                                            <? foreach($menu_pages as $key => $val) { ?>
                                            <tr>
                                                <th style="padding-left: 50px;"><?=$val["name"]?></th>
                                                <td>
                                                    <div class="col">
                                                        <div class="form-check custom-checkbox mb-3 check-xs">
                                                            <input name="permissions[]" value="<?=$val["page"]?>" type="checkbox" class="checkitem form-check-input" id="page_<?=$val["page"]?>" >
                                                            <label class="form-check-label" for="page_<?=$val["page"]?>"></label>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <? } ?>
                                        </tbody>
                                    </table>

                                    <div class="form-actions right">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="icon-check2"></i> Saqlash
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<? include "scripts.php"; ?>


<script>
    $("#checkAll").change(function(){
        $(".checkitem").prop('checked', $(this).prop('checked'));
    });
    $(".checkitem").change(function() {
        if($(this).prop('checked') == false) {
            $('#checkAll').prop('checked', false);
        }
        if($(".checkitem:checked").length == $(".checkitem").length) {
            $('#checkAll').prop('checked', true);
            console.log("hello");
        }
    })
</script>

<? include('end.php'); ?>
