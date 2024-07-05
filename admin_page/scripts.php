<!-- Scripts -->
<script src="../theme/rubust/app-assets/js/core/libraries/jquery.min.js" type="text/javascript"></script>
<script src="../theme/rubust/app-assets/vendors/js/ui/tether.min.js" type="text/javascript"></script>
<script src="../theme/rubust/app-assets/js/core/libraries/bootstrap.min.js" type="text/javascript"></script>
<script src="../theme/rubust/app-assets/vendors/js/ui/perfect-scrollbar.jquery.min.js" type="text/javascript"></script>
<script src="../theme/rubust/app-assets/vendors/js/ui/unison.min.js" type="text/javascript"></script>
<script src="../theme/rubust/app-assets/vendors/js/ui/blockUI.min.js" type="text/javascript"></script>
<script src="../theme/rubust/app-assets/vendors/js/ui/jquery.matchHeight-min.js" type="text/javascript"></script>
<script src="../theme/rubust/app-assets/vendors/js/ui/screenfull.min.js" type="text/javascript"></script>
<script src="../theme/rubust/app-assets/vendors/js/extensions/pace.min.js" type="text/javascript"></script>
<script src="../theme/rubust/app-assets/vendors/js/charts/chart.min.js" type="text/javascript"></script>
<script src="../theme/rubust/app-assets/js/core/app-menu.js?v=1.0.1" type="text/javascript"></script>
<script src="../theme/rubust/app-assets/js/core/app.js" type="text/javascript"></script>
<!-- End Scripts -->

<!-- CK Editor -->
<!-- <script src="https://cdn.ckeditor.com/4.17.1/standard-all/ckeditor.js"></script> -->
  
<script>
    $( window ).on( "resize", function() {
        console.log($(window).width());
        if($(window).width() <= 992 && $(window).width() > 767) {
            $(".brand-logo").attr('src',$(".brand-logo").data('collapse')).attr("width", "42px");
            $(".brand-logo").attr('src',$(".brand-logo").data('collapse')).css("margin-left", "-2px");
            $(".brand-logo").attr('src',$(".brand-logo").data('expend')).removeAttr("height");
        } else {
            $(".brand-logo").attr('src',$(".brand-logo").data('expend')).attr("height", "60px");
            $(".brand-logo").attr('src',$(".brand-logo").data('expend')).css("object-fit", "contain");
        }
    } );
    if($(window).width() <= 992 && $(window).width() > 767) {
        $(".brand-logo").attr('src',$(".brand-logo").data('collapse')).attr("width", "42px");
        $(".brand-logo").attr('src',$(".brand-logo").data('collapse')).css("margin-left", "-2px");
        $(".brand-logo").attr('src',$(".brand-logo").data('expend')).removeAttr("height");
    } else {
        $(".brand-logo").attr('src',$(".brand-logo").data('expend')).attr("height", "60px");
        $(".brand-logo").attr('src',$(".brand-logo").data('expend')).css("object-fit", "contain");
    }
    

$("#search_input_jquery").on("input", function(){
    var word = $(this).val().trim().toLowerCase();

    var pages = $(".main-menu-content").find("*[data-menu='menu-navigation']");

    pages.each(function(){
        var title = $(this).find(".menu-title").text();
        // console.log(title);
        var sub_pages = $(this).find(".menu-content").find("li");

        // console.log(
        //     title.toLowerCase().includes(word),
        //     word
        // );

        if (!title.toLowerCase().includes(word)) {
            $(this).hide();
        } else {
            $(this).show();
        }
    });

    // console.log(pages);
});

$("#search_input").on('input', function(){
    var word = $("#search_input").val();
    var type = $(this).attr('search-in');
    var page = <?=($page ? $page : 1)?>;
    
    if (word.length <= 2) {
        //   console.log(word.length);
        $("#search_result").hide().html('');
    } else {
        $.ajax({
            url: 'search.php',
            type: 'POST',
            dataType: 'text',
            data: {'type': type, 'word': word, 'page': page},
            success: function(data){
                var arr = JSON.parse(data);
                $("#search_result").html('').show();
                for (var i in arr){
                    $("#search_result")
                    .append('<li class="list-group-item"><a href="'+arr[i]['link']+'">'+arr[i]['name']+'</a></li>');
                }
            }
        });
    }
});
</script>

<!-- CK Editor -->
<script src="https://cdn.ckeditor.com/4.20.1/full-all/ckeditor.js"></script>

<!-- // CK EDITOR -->
<script>
var unique_id = 1000;

function editorOn() {
    $("*[editor]").each(function(){
        unique_id++;
        console.log(unique_id);
        $(this).attr("editor", "on");
        var id = "editor"+unique_id;
        $(this).attr("id", id);

        CKEDITOR.replace(id, {
            // Pressing Enter will create a new <div> element.
            enterMode: CKEDITOR.ENTER_DIV,
            // Pressing Shift+Enter will create a new <p> element.
            shiftEnterMode: CKEDITOR.ENTER_P,
            
            extraPlugins: 'uploadimage,image2',
            image2_disableResizer : true,

            filebrowserUploadMethod: 'form',
            filebrowserUploadUrl: "ck_upload_image.php",
            imageUploadUrl: "ck_upload_image.php",
            uploadUrl: "ck_upload_image.php"
        });

        CKEDITOR.on('dialogDefinition', function(ev) {
            var dialogName = ev.data.name;
            var dialogDefinition = ev.data.definition;
            if (dialogName == 'image2') {

                var infoTab = dialogDefinition.getContents( 'info' );

                infoTab.get('width').validate = function() {
                    return true; //more advanced validation rule should be used here
                }

                infoTab.get('height').validate = function() {
                    return true; //more advanced validation rule should be used here
                }
            }
        });
    })
} editorOn();
// CK EDITOR
</script>


<script> 
    $("form").on("submit", function(e){
        var form = $(this);
        var forms = $(this).find("*[required]");
        var i = 0;
        var error = false;

        if (forms.length == 0) {
            $(form).off("submit");
            $(form).submit();
            $(".loader-wrapper").show();
        } else {
            $(forms).each(function(){
                i++;

                if (!$(this).val()) {
                    error = true;
                }

                if (i === forms.length) {
                    if (error == true) {
                        $(".loader-wrapper").hide();
                    } else {
                        $(form).off("submit");
                        $(form).submit();
                        $(".loader-wrapper").show();
                    }
                }
            });
        }

        e.preventDefault();
    });
    // $(window).on("load", function() {
    //     $(".loader-wrapper").hide();
    // });
// </>

// <!-- Listening and Reading -->

<script>

    var form_radios = $("*[question-type='radios']").clone();
    $("*[question-type='radios']").remove();

    var form_checkboxes = $("*[question-type='checkboxes']").clone();
    $("*[question-type='checkboxes']").remove();
    
    var form_inputs = $("*[question-type='input_texts_in_paragraphs']").clone();
    $("*[question-type='input_texts_in_paragraphs']").remove();

    $("#answer_type").on("change", function(){
        var type = $(this).find("option:selected").val();
        $("#answer_classname_wrapper").hide();

        $("#question-form-wrapper").html("");

        if (type == 'radios') {
            $("#question-form-wrapper").html($(form_radios).show());
            $("#question-form-wrapper").find("*[editor]").attr("editor", "off");
            radios_savol_setting();
        } else if (type == 'checkboxes') {
            $("#question-form-wrapper").html($(form_checkboxes).show());
            $("#question-form-wrapper").find("*[editor]").attr("editor", "off");
            ch_savol_setting();
        } else if (type == 'inputTexts in paragraphs') {
            $("#question-form-wrapper").html($(form_inputs).show());
            $("#question-form-wrapper").find("*[editor]").attr("editor", "off");
            $("#answer_classname_wrapper").show();
        }

        editorOn();
    });

    $(document).on("click", "*[add-checkbox-savol]", function(){
        var savol_html = $(this).parents("#question-form-wrapper").find("#checkbox_savol_wrapper").find("*[savol]").eq(0).clone();
        $(this).parents("#question-form-wrapper").find("#checkbox_savol_wrapper").append(savol_html);
        ch_savol_setting();

        $(this).parents("#question-form-wrapper").find("#checkbox_savol_wrapper").find("*[savol]").last().find("*[arr-name='savol']").parents(".form-group").find("div").remove();

        var elm = $(this).parents("#question-form-wrapper").find("#checkbox_savol_wrapper").find("*[savol]").last();
        $(elm).find("*[editor]").attr("editor", "off");
        $(elm).find("#checkboxes-wrapper").html("");
        $(elm).find("*[savol-input]").each(function(){
            $(this).val("");
        });

        editorOn();
    });
    
    $(document).on("click", "*[add-checkbox]", function(){
        variants_lenth = $(this).parents("*[savol]").find("*[arr-name='variantlar']").length;
        var variant_id = variants_lenth > 0 ? variants_lenth : variants_lenth;

        var input = $(this).parents("*[savol]").find("#add-checkbox-input");
        var val = $(input).val();
        
        if (val.length > 0) {
            variant_id++;
            $(input).val("");

            $(this).parents("*[savol]").find("#checkboxes-wrapper").append(
                '<div class="form-group">'
                    +'<input type="checkbox" name="" savol-input="" arr-name="javoblar" arr-variant="'+variant_id+'" class=""> ' 
                    +'<input type="text" name="" savol-input="" arr-name="variantlar" arr-variant="'+variant_id+'" class="border-primary"   >'
                +'</div>'
            );

            $("*[arr-variant='"+variant_id+"']").val(val);

            ch_savol_setting();
        }
    });


    // checkbox (2 tadan ortiq belgilay olishni o'chirish)
    $(document).on("click", "input[type='checkbox']", function(){
        console.log("test");
        if ($(this).parents("#checkboxes-wrapper").find("input[type='checkbox']:checked").length == 4 && $(this).is(":checked")) {
            // console.warn("stop");
            // console.log($(this).parents("#checkboxes-wrapper").find("input[type='checkbox']:checked").length, $(this).is(":checked"));
            return false;
        }
    });

    // checkbox input keyup bo'lgandan checkbox valueni ham o'zgartirish
    $(document).on("keyup", "input[arr-name='variantlar']", function(){
        $(this).parents(".form-group").find("input[type='checkbox']").val($(this).val());
    });


    var input_num = 1;
    $(document).on("click", "*[add-input-text]", function(){
        input_num++;
        var input = $(this).parents("#question-form-wrapper").find(".form-group").eq(0).clone();
        
        var btn = $(this).clone();
        $(this).remove();

        $("#question-form-wrapper").find("*[question-type]").append(input).append(btn);

        var elm = $("#question-form-wrapper").find(".form-group").last();
        $(elm).find("label > span").text(input_num + "-");
        $(elm).find("textarea").val("");
    });

    $("#answer_type").change();
</script>

<script>
    // audio inputda tanlangan audioni oldindan eshitib ko'rish uchun
    function PreviewAudio(inputFile, previewElement) {
        if (inputFile.files && inputFile.files[0] && $(previewElement).length > 0) {

            $(previewElement).stop();

            var reader = new FileReader();

            reader.onload = function (e) {

                $(previewElement).attr('src', e.target.result);
                var playResult = $(previewElement).get(0).play();

                if (playResult !== undefined) {
                    playResult.then(_ => {
                        // Automatic playback started!
                        // Show playing UI.

                        $(previewElement).show();
                    })
                        .catch(error => {
                            // Auto-play was prevented
                            // Show paused UI.

                $(previewElement).hide();
                            alert("File Is Not Valid Media File");
                        });
                }
            };

            reader.readAsDataURL(inputFile.files[0]);
        } else {
            $(previewElement).attr('src', '');
            $(previewElement).hide();
            alert("File Not Selected");
        }
    }
</script>

<script>
    $("#audio_id").on("change", function(){
        var file = $(this).find("option:selected").attr("file-folder");
        
        if (file) {
            $("#audioPreview").attr("src", ""+file).show();
            $("#audio_file_input").hide();
        } else {
            $("#audioPreview").attr("src", "").hide();
            $("#audio_file_input").show();
        }
    }).change();
</script>

<script>
    $(document).ready(function(){
        $("#search_input_jquery").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("table tr").filter(function() {
                if ($(this).index() > 0) {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                }
            });
        });
    });
</script>

<script>
function fan_setting() {
    var id = 0;
    $("#fan_wrapper > *[fan]").each(function(){
        $(this).attr("fan", id);

        $(this).find("#fan-title").text("Blok o'z ichiga oladigan "+(id+1)+"-fannni kiriting");

        $(this).find("*[fan-input]").each(function(){
        $(this).attr("name", $(this).attr("name").split("[")[0] + "[" + id + "]")
        })

        id++;
    });
} fan_setting();

$("#add_fan").click(function(){
    var fan_html = $("#fan_wrapper > *[fan]").eq(0).clone();
    $("#fan_wrapper").append(fan_html);
    fan_setting();

    var savol_html = $("#fan_wrapper > *[fan]").eq(0).find("#savol_wrapper").find("*[savol]").eq(0).clone();
    $("#fan_wrapper > *[fan]").last().find("#savol_wrapper").find("*[savol]").remove();
    $("#fan_wrapper > *[fan]").last().find("#savol_wrapper").html(savol_html);

    savol_setting();

    $("#fan_wrapper > *[fan]").last().find("*[arr-name='savol']").parents(".form-group").find("div").remove();

    $("#fan_wrapper > *[fan]").last().find("*[name]").each(function(){
        $(this).val("");
    });

    editorOn();
});

function savol_setting() {
    var id = 0;

    $("#fan_wrapper > *[fan]").each(function(){
        var fan_id = $(this).attr("fan");

        $(this).find("#savol_wrapper").find("*[savol]").each(function(){
            $(this).attr("savol", id);

            $(this).find("#savol-title").text((id+1)+"-Savol");

            $(this).find("*[savol-input]").each(function(){

                var name = "";

                var arr_fan = $(this).attr("arr-fan");
                var arr_savol = $(this).attr("arr-savol");
                var arr_name = $(this).attr("arr-name");
                var arr_variant = $(this).attr("arr-variant");
                
                name += "fan";
                name += "["+fan_id+"]";
                name += "["+id+"]";
                name += "["+arr_name+"]";

                if (arr_variant) {
                    name += "["+arr_variant+"]";
                }

                $(this).attr("name", name);
            });

            id++;
        });
    });
} savol_setting();

$(document).on("click", "*[add-savol]", function(){
    console.log("event");
    var savol_html = $(this).parents("*[fan]").find("#savol_wrapper").find("*[savol]").eq(0).clone();
    $(this).parents("*[fan]").find("#savol_wrapper").append(savol_html);
    savol_setting();

    $(this).parents("*[fan]").find("#savol_wrapper").find("*[savol]").last().find("*[arr-name='savol']").parents(".form-group").find("div").remove();
    $(this).parents("*[fan]").find("#savol_wrapper").find("*[savol]").last().find("*[savol-input]").each(function(){
        $(this).val("");
    });

    editorOn();
});

$("#test_btn").on("click", function(){
    $("#test_wrapper").html("");

    var text = $("#test_input").val();
    var text_arr = text.split("\n");

    var savollar = [];

    text_arr.forEach(function(line, key){
        // console.warn(key);
        if (line.trim() != "") {
            // console.log(line.split(" ")[0].trim());
            if (line.split(" ")[0].trim().endsWith(".")) {
                var num, savol, javob;

                num = Number(line.split(" ")[0].trim().replaceAll(".", ""));

                savol = line;

                var variantlar = [];

                if (
                    text_arr[(key + 1)].startsWith("A)") &&
                    text_arr[(key + 2)].startsWith("B)") &&
                    text_arr[(key + 3)].startsWith("C)") &&
                    text_arr[(key + 4)].startsWith("D)")
                ) {
                    variantlar[1] = text_arr[(key + 1)].replaceAll("A)", "").trim();
                    variantlar[2] = text_arr[(key + 2)].replaceAll("B)", "").trim();
                    variantlar[3] = text_arr[(key + 3)].replaceAll("C)", "").trim();
                    variantlar[4] = text_arr[(key + 4)].replaceAll("D)", "").trim();
                    javob = text_arr[(key + 5)].trim();
                } else {
                    var variantlar_all = text_arr[(key + 1)] + " " + text_arr[(key + 2)];

                    variantlar_all = variantlar_all.replaceAll("\n", "");

                    console.warn(variantlar_all);

                    var new_arr = variantlar_all.split("A)").join("{-}").split("B)").join("{-}").split("C)").join("{-}").split("D)").join("{-}").split("{-}");

                    // variantlar = new_arr.shift();
                    variantlar = new_arr;

                    javob = text_arr[(key + 3)].trim();
                }
                
                console.warn("variantlar: ", variantlar);

                // if (text_arr[(key + 1)].length > 0) {
                //     javob = text_arr[(key + 1)];
                // } else if (text_arr[(key + 2)].length > 0) {
                //     javob = text_arr[(key + 2)];
                // } else {
                //     alert("xatoli!!! " + num + "-savolning javobini olishni imkoni bo'lmadi")
                // }

                if (savol.length > 0) {
                    $("#test_wrapper")
                    .append(`<div class="card" style="border-color: #aaa!important;" savol="`+(num + 1)+`">
                                <div class="card-header">
                                    <h4 class="card-title" id="savol-title">`+(num)+`-Savol</h4>
                                    
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
                                        <div class="form-group">
                                            <label>Savol</label>
                                            <textarea
                                                name="test_savol[]"
                                                rows="5"
                                                class="form-control"
                                                placeholder="Savol"
                                            >`+savol+`</textarea>
                                        </div>

                                        <div class="form-group">
                                            <label>To'g'ri variant</label>
                                            <select name="test_javob[]" class="form-control" savol-input="" arr-fan="1" arr-savol="`+(num + 1)+`" arr-name="javob">
                                                <option value="A" `+(javob == "A" ? 'selected=""' : '')+`>A</option>
                                                <option value="B" `+(javob == "B" ? 'selected=""' : '')+`>B</option>
                                                <option value="C" `+(javob == "C" ? 'selected=""' : '')+`>C</option>
                                                <option value="D" `+(javob == "D" ? 'selected=""' : '')+`>D</option>
                                            </select>
                                        </div>

                                        <!-- Variantlar -->
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>A-variant</label>
                                                    <input type="text" name="variant_a[]" class="form-control border-primary" placeholder="A-variant" savol-input="" arr-fan="1" arr-savol="`+(num + 1)+`" arr-name="variant" arr-variant="1" value="`+variantlar[1].trim()+`">
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>B-variant</label>
                                                    <input type="text" name="variant_b[]" class="form-control border-primary" placeholder="B-variant" savol-input="" arr-fan="1" arr-savol="`+(num + 1)+`" arr-name="variant" arr-variant="2" value="`+variantlar[2].trim()+`">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>C-variant</label>
                                                    <input type="text" name="variant_c[]" class="form-control border-primary" placeholder="C-variant" savol-input="" arr-fan="1" arr-savol="`+(num + 1)+`" arr-name="variant" arr-variant="3" value="`+variantlar[3].trim()+`">
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>D-variant</label>
                                                    <input type="text" name="variant_d[]" class="form-control border-primary" placeholder="D-variant" savol-input="" arr-fan="1" arr-savol="`+(num + 1)+`" arr-name="variant" arr-variant="4" value="`+variantlar[4].trim()+`">
                                                </div>
                                            </div>
                                        </div>
                                        <!-- End Variantlar -->

                                        
                                    </div>
                                </div>
                            </div>`);
                }
                // console.log(line.split(" ")[0].trim().split(".")[1]);
            }
        }
    })
});

$("#savol_javob").on("click", function(){
    $("#savol_wrapper").html("");
    
    var text = $("#savol_javob_input").val();
    var text_arr = text.split("\n");

    var savollar = [];

    text_arr.forEach(function(line, key){
        // console.warn(key);
        if (line.trim() != "") {
            // console.log(line.split(" ")[0].trim());
            if (line.split(" ")[0].trim().endsWith(".")) {
                var num, savol, javob;

                num = line.split(" ")[0].trim().replaceAll(".", "");

                savol = line;

                if (text_arr[(key + 1)].length > 0) {
                    javob = text_arr[(key + 1)];
                } else if (text_arr[(key + 2)].length > 0) {
                    javob = text_arr[(key + 2)];
                } else {
                    alert("xatoli!!! " + num + "-savolning javobini olishni imkoni bo'lmadi")
                }

                if (num.length > 0 && savol.length > 0  && javob.length > 0) {
                    $("#savol_wrapper").append(`<div class="card" style="border-color: #aaa!important;">
                        <div class="card-header">
                            <h4 class="card-title" id="savol-title">`+num+`-Savol</h4>
                            
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
                                <div class="form-group">
                                    <label>Savol</label>
                                    <textarea
                                        rows="5"
                                        name="savol[]"
                                        rows="5"
                                        class="form-control"
                                        placeholder="Savol"
                                    >`+savol+`</textarea>
                                </div>
                                
                                <div class="form-group">
                                    <label>Javob</label>
                                    <textarea
                                        rows="5"
                                        name="javob[]"
                                        rows="5"
                                        class="form-control"
                                        placeholder="Javob"
                                    >`+javob+`</textarea>
                                </div>
                            </div>
                        </div>
                    </div>`);
                }
                // console.log(line.split(" ")[0].trim().split(".")[1]);
            }
        }
    });

    savol_setting();
});

$("#savol_javob").click();
$("#test_btn").click();
</script>

<script>
    $("#flag-icon").change(function(){
        $("#lang_icon").attr("class", "flag-icon flag-icon-"+$(this).find(":selected").val());
    });

    $("*[select-form-lang]").click(function(){
        var lang = $(this).attr('select-form-lang');
        console.log(lang);
        $("*[form-lang]").hide();
        $("*[form-lang='"+lang+"']").show();
    });
</script>