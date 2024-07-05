<?php


$type = $_POST["type"];
$word = $_POST["word"];
$page = $_POST["page"];

$res_arr = [];

switch ($type) {
    case "search_learner":
        $users = $db->in_array("SELECT * FROM users WHERE admin = 0 AND (id = '".$word."' OR first_name LIKE :first_name OR last_name LIKE :last_name OR phone LIKE :phone)",
        [
            ':first_name' => "%$word%",
            ':last_name' => "%$word%",
            ':phone' => "%$word%"
        ]);
        
        $num = 0;
        foreach ($users as $user){
            $res_arr[$num]["link"] = "edit_learner.php?user_id=" . $user["id"] . "&page=" . $page;
            $res_arr[$num]["name"] = $user["id"] . ") " . $user["first_name"] . " " . $user["last_name"] . " (".$user["phone"].")";
            $num++;
        }
    break;

    case "search_admin":
        $users = $db->in_array("SELECT * FROM users WHERE admin = 1 AND (id = '".$word."' OR first_name LIKE :first_name OR last_name LIKE :last_name OR phone LIKE :phone)",
        [
            ':first_name' => "%$word%",
            ':last_name' => "%$word%",
            ':phone' => "%$word%"
        ]);
        
        $num = 0;
        foreach ($users as $user){
            $res_arr[$num]["link"] = "edit_admin.php?user_id=" . $user["id"] . "&page=" . $page;
            $res_arr[$num]["name"] = $user["id"] . ") " . $user["first_name"] . " " . $user["last_name"] . " (".$user["phone"].")";
            $num++;
        }
    break;

    case "search_group":
        $groups = $db->in_array("SELECT * FROM users_groups WHERE id = '".$word."' OR name LIKE :name OR created_date LIKE :created_date",
        [
            ':name' => "%$word%",
            ':created_date' => "%$word%"
        ]);
        
        $num = 0;
        foreach ($groups as $group){
            $res_arr[$num]["link"] = "edit_group.php?group_id=" . $group["id"] . "&page=" . $page;
            $res_arr[$num]["name"] = $group["id"] . ") " . $group["name"] . " (".$group["created_date"].")";
            $num++;
        }
    break;

    case "search_mock":
        $mocks = $db->in_array("SELECT * FROM mocks WHERE id = '".$word."' OR name LIKE :name OR created_date LIKE :created_date",
        [
            ':name' => "%$word%",
            ':created_date' => "%$word%"
        ]);
        
        $num = 0;
        foreach ($mocks as $mock){
            $res_arr[$num]["link"] = "edit_mock.php?mock_id=" . $mock["id"] . "&page=" . $page;
            $res_arr[$num]["name"] = $mock["id"] . ") " . $mock["name"] . " (".$mock["created_date"].")";
            $num++;
        }
    break;

    case "search_writing_test":
        $writing_tests = $db->in_array("SELECT * FROM writing_tests WHERE id = '".$word."' OR page LIKE :page OR name LIKE :name OR rule LIKE :rule OR question LIKE :question OR created_date LIKE :created_date",
        [
            ':page' => "%$word%",
            ':rule' => "%$word%",
            ':question' => "%$word%",
            ':name' => "%$word%",
            ':created_date' => "%$word%"
        ]);
        
        $num = 0;
        foreach ($writing_tests as $writing_test){
            $res_arr[$num]["link"] = "edit_writing_test.php?writing_test_id=" . $writing_test["id"] . "&page=" . $page;
            $res_arr[$num]["name"] = $writing_test["id"] . ") " . $writing_test["name"] . " (".$writing_test["created_date"].")";
            $num++;
        }
    break;

    case "search_listening_test":
        $tests = $db->in_array("SELECT * FROM listening_tests WHERE id = '".$word."' OR page LIKE :page OR name LIKE :name OR question_numbers LIKE :question_numbers OR created_date LIKE :created_date",
        [
            ':page' => "%$word%",
            ':name' => "%$word%",
            ':question_numbers' => "%$word%",
            ':created_date' => "%$word%"
        ]);
        
        $num = 0;
        foreach ($tests as $test){
            $res_arr[$num]["link"] = "edit_listening_test.php?listening_test_id=" . $test["id"] . "&page=" . $page;
            $res_arr[$num]["name"] = $test["id"] . ") " . $test["name"] . " (".$test["created_date"].")";
            $num++;
        }
    break;

    case "search_reading_test":
        $tests = $db->in_array("SELECT * FROM reading_tests WHERE id = '".$word."' OR page LIKE :page OR name LIKE :name OR question_numbers LIKE :question_numbers OR created_date LIKE :created_date",
        [
            ':page' => "%$word%",
            ':name' => "%$word%",
            ':question_numbers' => "%$word%",
            ':created_date' => "%$word%"
        ]);
        
        $num = 0;
        foreach ($tests as $test){
            $res_arr[$num]["link"] = "edit_reading_test.php?reading_test_id=" . $test["id"] . "&page=" . $page;
            $res_arr[$num]["name"] = $test["id"] . ") " . $test["name"] . " (".$test["created_date"].")";
            $num++;
        }
    break;
}


if ($res_arr) echo json_encode($res_arr);
?>