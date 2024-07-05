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
include "./modules/menuPages.php";


$user = $db->assoc("SELECT * FROM users WHERE id = ?", [ $_REQUEST["user_id"]]);
if (!$user["id"]) exit(http_response_code(404));

if ($_REQUEST['type'] == "edit_user"){
    $first_name = !empty($_REQUEST['first_name']) ? $_REQUEST['first_name'] : null;
    if (!$first_name) {echo"error1";return;}

    $last_name = !empty($_REQUEST['last_name']) ? $_REQUEST['last_name'] : null;
    if (!$last_name) {echo"error1";return;}

    $login = !empty($_REQUEST['login']) ? $_REQUEST['login'] : null;
    if (!$login) {echo"error1";return;}
    
    $permissions = !empty($_REQUEST['permissions']) ? $_REQUEST['permissions'] : null;
    if (!$permissions) {echo"Permissions not found";return;}

    $password = !empty($_REQUEST['password']) ? $_REQUEST['password'] : null;

    if ($password) {
      $password_encrypted = encode($password);
      $password = md5(md5(encode($password)));
    } else {
      $password_encrypted = $user["password_encrypted"];
      $password = $user['password'];
    }

    $birth_date = !empty($_REQUEST['birth_date']) ? $_REQUEST['birth_date'] : null;
    $country_id = !empty($_REQUEST['country_id']) ? $_REQUEST['country_id'] : null;
    $region_id = !empty($_REQUEST['region_id']) ? $_REQUEST['region_id'] : null;
    $bio = !empty($_REQUEST['bio']) ? $_REQUEST['bio'] : null;
    $address = !empty($_REQUEST['address']) ? $_REQUEST['address'] : null;

    $db->update("users", [
      "first_name" => $first_name,
      "last_name" => $last_name,
      "login" => $login,
      "password" => $password,
      "password_encrypted" => $password_encrypted,
      "birth_date" => $birth_date,
      "country_id" => $country_id,
      "region_id" => $region_id,
      "bio" => $bio,
      "address" => $address,
      "admin" => 1,
      "role" => $_REQUEST["role"],
      "permissions" => ($permissions ? json_encode($permissions) : NULL)
    ], [
      "id" => $_REQUEST['user_id']
    ]);

    header('Location: admins_list.php?page=1');
}

if ($_REQUEST['type'] == "delete_user"){
    $db->delete("users", $_REQUEST['user_id']);

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
                            <h4 class="card-title" id="basic-layout-colored-form-control">Adminni taxrirlash</h4>
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
                                <form action="" method="GET" class="form">
                                    <input type="hidden" name="type" value="edit_user">
                                    <input type="hidden" name="user_id" value="<?=$_REQUEST['user_id']?>">
                                    
                                    <div class="form-group">
                                        <label for="first_name">ismi</label>
                                        <input type="text" name="first_name" class="form-control border-primary" placeholder="ismi" id="first_name" value="<?=$user['first_name']?>">
                                    </div>

                                    <div class="form-group">
                                        <label for="last_name">familiya</label>
                                        <input type="text" name="last_name" class="form-control border-primary" placeholder="familiya" id="last_name" value="<?=$user['last_name']?>">
                                    </div>

                                    <div class="form-group">
                                        <label for="login">login</label>
                                        <input type="text" name="login" class="form-control border-primary" placeholder="telefon" id="login" value="<?=$user['login']?>">
                                    </div>

                                    <div class="form-group">
                                        <label for="password">parol</label>
                                        <input type="text" name="password" class="form-control border-primary" placeholder="parol" id="password" value="<?=($user["password_encrypted"] ? decode($user["password_encrypted"]) : "")?>">
                                    </div>

                                    <div class="form-group">
                                        <label for="birth_date">Tug'ilgan sana</label>
                                        <input type="date" name="birth_date" class="form-control border-primary" placeholder="Tug'ilgan sana" id="birth_date" value="<?=(date("Y-m-d", strtotime($user['birth_date'])))?>">
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
                                            <option value="1" <?=($user["region_id"] == 1 ? 'selected=""' : '')?>>Andijon viloyati</option>
                                            <option value="2" <?=($user["region_id"] == 2 ? 'selected=""' : '')?>>Namangan viloyati</option>
                                            <option value="3" <?=($user["region_id"] == 3 ? 'selected=""' : '')?>>Farg'ona viloyati</option>
                                            <option value="4" <?=($user["region_id"] == 4 ? 'selected=""' : '')?>>Toshkent viloyati</option>
                                            <option value="5" <?=($user["region_id"] == 5 ? 'selected=""' : '')?>>Buxoro viloyati</option>
                                            <option value="6" <?=($user["region_id"] == 6 ? 'selected=""' : '')?>>Jizzax viloyati</option>
                                            <option value="7" <?=($user["region_id"] == 7 ? 'selected=""' : '')?>>Xorazm viloyati</option>
                                            <option value="8" <?=($user["region_id"] == 8 ? 'selected=""' : '')?>>Navoiy viloyati</option>
                                            <option value="9" <?=($user["region_id"] == 9 ? 'selected=""' : '')?>>Qashqadaryo viloyati</option>
                                            <option value="10" <?=($user["region_id"] == 10 ? 'selected=""' : '')?>>Qoraqalpogʻiston</option>
                                            <option value="11" <?=($user["region_id"] == 11 ? 'selected=""' : '')?>>Samarqand viloyati</option>
                                            <option value="12" <?=($user["region_id"] == 12 ? 'selected=""' : '')?>>Sirdaryo viloyati</option>
                                            <option value="13" <?=($user["region_id"] == 13 ? 'selected=""' : '')?>>Surxondaryo viloyati</option>
                                            <option value="14" <?=($user["region_id"] == 14 ? 'selected=""' : '')?>>Toshkent shahri</option>
                                        </select>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="bio">Admin haqida</label>
                                                <input type="text" name="bio" class="form-control border-primary" placeholder="Admin haqida" id="bio" value="<?=$user["bio"]?>">
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="address">Manzil</label>
                                                <input type="text" name="address" class="form-control border-primary" placeholder="Manzil" id="address" value="<?=$user["address"]?>">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="role">Lavozim</label>
                                        <input type="text" name="role" class="form-control border-primary" placeholder="Lavozimi" id="role" value="<?=$user['role']?>">
                                    </div>

                                    <div class="table-responsive col-12">
                                        <table class="table table-hover table-bordered table-responsive-md">
                                            <tbody>
                                                <tr class="bg-dark text-white">
                                                    <th><label class="form-check-label" for="checkAll">Hamasini belgilash</label></th>
                                                    <td><input type="checkbox" class="form-check-input" id="checkAll"></td>
                                                </tr>
                                                <? foreach($menu_pages as $key => $val) { ?>
                                                <tr>
                                                    <th><?=$val["name"]?></th>
                                                    <td>
                                                        <div class="col">
                                                            <div class="form-check custom-checkbox mb-3 check-xs">
                                                                <? if ($user["permissions"] && in_array($val["page"], json_decode($user["permissions"]))) { ?>
                                                                    <input name="permissions[]" value="<?=$val["page"]?>" type="checkbox" class="checkitem form-check-input" id="page_<?=$val["page"]?>" checked="">
                                                                <? } else { ?>
                                                                    <input name="permissions[]" value="<?=$val["page"]?>" type="checkbox" class="checkitem form-check-input" id="page_<?=$val["page"]?>">
                                                                <? } ?>
                                                                <label class="form-check-label" for="page_<?=$val["page"]?>"></label>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <? } ?>
                                            </tbody>
                                        </table>
                                    </div>

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

    var numberOfChecked = $('input:checkbox:checked').length
    // var numberOfCheckbox = $('input:checkbox').length
    // console.log(numberOfCheckbox);

    // $('#el').is(':checked')
    if($(".checkitem:checked").length == $(".checkitem").length) {
        console.log(numberOfChecked);
        $('#checkAll').prop('checked', true);
    }

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
