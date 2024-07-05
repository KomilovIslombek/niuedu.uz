<?php

if ($systemUser->admin!=1 || !$user_id || $user_id == 0){
    header('Location:/login');
    exit;
}

include('filter.php');

if ($_REQUEST['type'] == "add_block_science"){
    $name = isset($_REQUEST['name']) ? $_REQUEST['name'] : null;
    if (!$name) {echo"error [name]";exit;}

    $science_id = $db->insert("quiz_sciences", [
        "creator_user_id" => $user_id,
        "name" => $name
    ]);

    header('Location: block_test_sciences_list.php?page=1');
}

include('head.php');
?>

<div class="app-content content container-fluid">
    <div class="content-wrapper">
        <div class="content-body">

                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title" id="basic-layout-colored-form-control">Yangi blok test fan qo'shish</h4>
                            
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
                                <form action="" method="POST" class="form" enctype="multipart/form-data">
                                    <input type="hidden" name="type" value="add_block_science" required>

                                    <div class="form-group">
                                        <label>fan nomi</label>
                                        <textarea name="name" rows="5" class="form-control" placeholder="fan nomi" required></textarea>
                                    </div>
                                    
                                    <div class="form-actions right">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="icon-check2"></i> saqlash
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

<style>
    .js .input--file {
        width: 0.1px;
        height: 0.1px;
        opacity: 0;
        overflow: hidden;
        position: absolute;
        z-index: -1;
    }

    .no-js .input--file + label {
        display: none;
    }
    .js .input--file + label {
        display: inline-block;
        cursor: pointer;
        background: orange;
        color: #fff;
        padding: 10px;
    }

    .js .input--file:focus + label {
        outline: 1px dotted #000;
        outline: -webkit-focus-ring-color auto 5px;
    }
</style>

<script>
    [].slice.call(document.querySelectorAll(".input--file")).forEach(function(el,i){
		el.addEventListener( 'change', function( e ){
			var fileName = '';
			var label = document.querySelectorAll('label[for="' + el.getAttribute('id') + '"]')[0];
			var currentVal = label.innerHTML;
			if (this.files && this.files.length > 1)
				fileName = this.files.length + ' ta fayl';
			else
				fileName = e.target.value.split('\\').pop();

			if (fileName)
				label.innerHTML = fileName;
			else
				label.innerHTML = currentVal;
		});
	});
</script>

<? include "scripts.php"; ?>

<? include("end.php"); ?>