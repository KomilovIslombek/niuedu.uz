<? if ($oqishni_kochirish_off) { ?>
    <div class="container" style="padding-top:110px;">
        <div class="row justify-content-center cv-row">
            <div class="col-11 col-sm-10 col-md-8 col-lg-7 col-xl-6 text-center p-0 mt-3 mb-2">
                <div class="card px-0 pt-4 pb-0 mt-3 mb-3">
                    <h1 id="heading text-danger"><?=t("Tez kunda o'qishni ko'chirish bo'yicha qabul ochiladi!")?></h1>
                </div>
            </div>
        </div>
    </div>
<? } else { ?>
    <div class="container" style="padding-top:110px;">
        <div class="row justify-content-center cv-row">
            <div class="card px-0 pt-4 pb-0 mt-3 mb-3 text-center">
                <h2 id="heading"><?=t("ARIZA TOPSHIRISH")?></h2>
                <p><?=t("o'qishni ko'chirishga")?></p>
                <form action="/<?=$url2[0]?>/<?=$url2[1]?>/<?=$url2[2]?>" method="POST" id="msform" enctype="multipart/form-data">
                    <div class="form-card">
                        <div class="row">
                            <h2 class="fs-title text-danger text-center mb-4"><?=t("Shaxsiy ma'lumotlar")?></h2>
                        </div>
    
                        <? if ($error) { ?>
                            <h3 class="text-center text-danger"><?=$error?></h3>
                        <? } ?>
    
                        <div class="row">
                            <div class="col-12 col-sm-12 col-md-4 col-lg-4 col-xl-4 mb-2">
                                <? if ($file_1_error) { ?>
                                    <h5 class="text-danger"><?=$file_1_error?></h5>
                                <? } ?>
                                <label class="fieldlabels" for="file_1"><?=t("3.5X4.5 hajmdagi rasmingizni yuklang")?> <label class="text-danger">*</label></label>
                                <input type="file" name="file_1" id="file_1" style="display:none" required="">
                                <div class="input-image" id="input-image">
                                    <i class="fa fa-plus"></i>
                                    <span><?=t("Yuklash")?></span>
                                </div>
                            </div>
                            
                            <div class="col-12 col-sm-12 col-md-8 col-lg-8 col-xl-8">
                                <div class="row">
                                    <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
                                        <label class="fieldlabels" for="first_name"><?=t("Ismingiz (Lotin alifbosida)")?> <label class="text-danger">*</label></label>
                                        <input type="text" name="first_name" placeholder="<?=t("Ismingiz (Lotin alifbosida)")?>" id="first_name" required="" value="<?=htmlspecialchars($_POST["first_name"])?>">
                                    </div>
    
                                    <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
                                        <label class="fieldlabels" for="last_name"><?=t("Familiyangiz (Lotin alifbosida)")?> <label class="text-danger">*</label></label>
                                        <input type="text" name="last_name" placeholder="<?=t("Familiyangiz (Lotin alifbosida)")?>" id="last_name" required="" value="<?=htmlspecialchars($_POST["last_name"])?>">
                                    </div>
    
                                    <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
                                        <label class="fieldlabels" for="father_first_name"><?=t("Otangizninig ismi (Lotin alifbosida)")?> <label class="text-danger">*</label></label>
                                        <input type="text" name="father_first_name" placeholder="<?=t("Otangizninig ismi (Lotin alifbosida)")?>" id="father_first_name" required="" value="<?=htmlspecialchars($_POST["father_first_name"])?>">
                                    </div>
    
                                    <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
                                        <label class="fieldlabels" for="birth_date"><?=t("Tug'ilgan sanangiz (20.01.2001)")?> <label class="text-danger">*</label></label>
                                        <input type="date" name="birth_date" placeholder="<?=t("Tug'ilgan sanangiz (20.01.2001)")?>" id="birth_date" required="" value="<?=htmlspecialchars($_POST["birth_date"])?>">
                                    </div>
    
                                    <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
                                        <label for="sex" class="fieldlabels"><?=t("Jinsingiz")?> <b id="d_h"></b><label class="text-danger">*</label></label>
                                        <select name="sex" id="sex" required="">
                                            <option value="erkak" <?=($_POST["sex"] == "erkak" ? 'selected=""' : '')?>><?=t("Erkak")?></option>
                                            <option value="ayol" <?=($_POST["sex"] == "ayol" ? 'selected=""' : '')?>><?=t("Ayol")?></option>
                                        </select>
                                    </div>
    
                                    <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
                                        <label for="region_id" class="fieldlabels"><?=t("Viloyat")?> <b id="d_h"></b><label class="text-danger">*</label></label>
                                        <select name="region_id" id="region_id" required="">
                                            <?
                                            $regions = $db->in_array("SELECT * FROM regions");
                                            foreach ($regions as $region) {
                                                echo '<option value="'.$region["id"].'" '.($_POST["region_id"] == $region["id"] ? 'selected=""' : '').' data-region-id="'.$region["id"].'">'.$region["name"].'</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
    
                            <div class="col-12 col-sm-12 col-md-3 col-lg-3 col-xl-3">
                                <label for="district_id" class="fieldlabels"><?=t("Tuman")?> <b id="d_h"></b><label class="text-danger">*</label></label>
                                <select name="district_id" id="district_id" required=""></select>
                            </div>
    
                            <div class="col-12 col-sm-12 col-md-3 col-lg-3 col-xl-3">
                                <label class="fieldlabels" for="adress"><?=t("Manzil")?> <label class="text-danger">*</label></label>
                                <input type="text" name="adress" placeholder="<?=t("Manzil")?>" id="adress" required="" value="<?=htmlspecialchars($_POST["adress"])?>">
                            </div>
    
                            <div class="col-12 col-sm-12 col-md-3 col-lg-3 col-xl-3">
                                <label class="fieldlabels" for="phone_1"><?=t("Telefon raqamingiz")?> <label class="text-danger">*</label></label>
                                <input type="text" name="phone_1" placeholder="+998" id="phone_1" minlength="17" required="" value="<?=($_POST["phone_1"] ? htmlspecialchars($_POST["phone_1"]) : "+998")?>">
                            </div>
    
                            <div class="col-12 col-sm-12 col-md-3 col-lg-3 col-xl-3">
                                <label class="fieldlabels" for="phone_2"><?=t("Qo'shimcha telefon raqam")?> <label class="text-danger">*</label></label>
                                <input type="text" name="phone_2" placeholder="+998" id="phone_2" minlength="17" required="" value="<?=($_POST["phone_2"] ? htmlspecialchars($_POST["phone_2"]) : "+998")?>">
                            </div>
    
                            <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6 mt-4">
                                <h2 class="fs-title text-danger text-center mb-4"><?=t("Passport ma'lumotlar")?></h2>
    
                                <div class="row">
                                    <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
                                        <div class="row no-gutters">
                                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                                <label class="fieldlabels" for="passport_serial"><?=t("Passport seriyasi")?> <label class="text-danger">*</label></label>
                                                <input type="text" name="passport_serial" placeholder="- -" id="passport_serial" required="" value="<?=htmlspecialchars($_POST["passport_serial"])?>">
                                            </div>
        
                                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                                <label class="fieldlabels" for="passport_number"><?=t("Passport raqami")?> <label class="text-danger">*</label></label>
                                                <input type="text" name="passport_number" placeholder="- - - - - - -" id="passport_number" required="" value="<?=htmlspecialchars($_POST["passport_number"])?>">
                                            </div>
    
                                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                                <? if ($file_2_error) { ?>
                                                    <h5 class="text-danger"><?=$file_2_error?></h5>
                                                <? } ?>
                                                <label class="fieldlabels" for="file_1"><?=t("Passport rangli nusxasi")?> <label class="text-danger">*</label></label>
                                                <input type="file" name="file_1" id="file_1" required="">
                                            </div>
                                        </div>
                                    </div>
    
                                    <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
                                        <img src="images/jshr.jpg" alt="jshr" width="100%">
                                    </div>
                                </div>
                            </div>
    
                            <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6 mt-4">
                                <h2 class="fs-title text-danger text-center mb-4"><?=t("Ta'lim ma'lumotlar")?></h2>
    
                                <div class="col-12">
                                    <? if ($file_3_error) { ?>
                                        <h5 class="text-danger"><?=$file_3_error?></h5>
                                    <? } ?>
                                    <label class="fieldlabels" for="file_3"><?=t("Transkript yoki akademik ma'lumotnoma")?> <label class="text-danger">*</label></label>
                                    <input type="file" name="file_3" id="file_3" required="">
                                </div>
    
                                <div class="col-12">
                                    <? if ($file_4_error) { ?>
                                        <h5 class="text-danger"><?=$file_4_error?></h5>
                                    <? } ?>
                                    <label class="fieldlabels" for="file_4"><?=t("Agar DTM dan imtihon topshirgan bo'lsangiz natijani yuklang")?> </label>
                                    <input type="file" name="file_4" id="file_4">
                                </div>
                            </div>
    
                            <h2 class="fs-title text-danger text-center mb-4"><?=t("Ta'lim ma'lumotlar")?></h2>

                            <div class="col-12 col-sm-12 col-md-4 col-lg-4 col-xl-4">
                                <label for="to_course" class="fieldlabels"><?=t("Qaysi kursga o'tmoqchisiz?")?> <label class="text-danger">*</label></label>
                                <select name="to_course" id="to_course" required="">
                                    <option value="2-kurs" <?=($_POST["to_course"] == "2-kurs" ? 'selected=""' : "")?>><?=t("2-kurs")?></option>
                                    <option value="3-kurs" <?=($_POST["to_course"] == "3-kurs" ? 'selected=""' : "")?>><?=t("3-kurs")?></option>
                                </select>
                            </div>
    
                            <div class="col-12 col-sm-12 col-md-4 col-lg-4 col-xl-4">
                                <label for="direction" class="fieldlabels"><?=t("Ta'lim yo'nalishi")?> <b id="d_h"></b><label class="text-danger">*</label></label>
                                <select name="direction_id" id="direction" required="">
                                    <?
                                    $directions = $db->in_array("SELECT * FROM directions WHERE active = 1");
                                    foreach ($directions as $direction) {
                                        echo '<option value="'.$direction["id"].'" '.($_POST["direction_id"] == $direction["id"] ? 'selected=""' : '').' data-direction-id="'.$direction["id"].'">'.lng($direction["short_name"]).'</option>';
                                    }
                                    ?>
                                </select>
                            </div>
    
                            <div class="col-12 col-sm-12 col-md-4 col-lg-4 col-xl-4">
                                <label for="learn_type" class="fieldlabels"><?=t("Ta'lim shakli")?> <b id="html"></b><label class="text-danger">*</label></label>
                                <select name="learn_type" id="learn_type" required="">
                                    <option value="Kunduzgi" id="option_kunduzgi" <?=($_POST["learn_type"] == "Kunduzgi" ? 'selected=""' : "")?>>Kunduzgi</option>
                                    <option value="Kechki" id="option_kechki" <?=($_POST["learn_type"] == "Kechki" ? 'selected=""' : "")?>>Kechki</option>
                                    <option value="Sirtqi" id="option_sirtqi" <?=($_POST["learn_type"] == "Sirtqi" ? 'selected=""' : "")?>>Sirtqi</option>
                                </select>
                            </div>
    
                            <div class="col-12 col-sm-12 col-md-4 col-lg-4 col-xl-4">
                                <label for="exam_lang" class="fieldlabels"><?=t("Test imtihonini qaysi tilda topshirasiz?")?> <label class="text-danger">*</label></label>
                                <select name="exam_lang" id="exam_lang" required="">
                                    <option value="uz" <?=($_POST["exam_lang"] == "uz" ? 'selected=""' : "")?>><?=t("O'zbek tili")?></option>
                                    <option value="ru" <?=($_POST["exam_lang"] == "ru" ? 'selected=""' : "")?>><?=t("Rus tili")?></option>
                                </select>
                            </div>
    
                            <div class="col-12 col-sm-12 col-md-4 col-lg-4 col-xl-4" style="display:none;">
                                <label for="exam_foreign_lang" class="fieldlabels"><?=t("Chet tili imtihonini qaysi tilda topshirasiz?")?> <label class="text-danger">*</label></label>
                                <select name="exam_foreign_lang" id="exam_foreign_lang" required="">
                                    <option value="en" <?=($_POST["exam_foreign_lang"] == "en" ? 'selected=""' : "")?> selected=""><?=t("Ingliz tili")?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                            
                    <input type="submit" name="submit" class="next action-button" value="<?=t("Jo'natish")?> »" style="width:150px;">
                </form>
            </div>
        </div>
    </div>
<? } ?>
