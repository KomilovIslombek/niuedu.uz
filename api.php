<?
header("content-type: application/json");
$res_arr = [];

$req = $_POST;

function validate_forms($forms) {
    global $req;
    foreach ($forms as $form) {
        if (!isset($req[$form])) exit("$form is empty");
    }
}

switch ($req["method"]) {
    case "getDistricts":
        validate_forms(["region_id"]);

        $res_arr["districts"] = $db->in_array("SELECT id, name FROM districts WHERE region_id = ?", [ $req["region_id"] ]);

        echo json_encode($res_arr, JSON_UNESCAPED_UNICODE);
        exit;
    break;
    
    case "withdrawal_of_money_agent":
        // validate_forms(["id"]);

        $withdrawal_of_money_agent = $db->assoc("SELECT * FROM withdrawal_of_money_agents WHERE id = ?", [ $req["id"] ]);
        $agent = $db->assoc("SELECT * FROM firms WHERE id = ?", [ $withdrawal_of_money_agent["agent_id"] ]);
        if($withdrawal_of_money_agent["id"]) {
            $db->update("withdrawal_of_money_agents", [
                "status" => $withdrawal_of_money_agent["status"] == 1 ? 0 : 1,
            ], [
                "id" => $withdrawal_of_money_agent["id"]
            ]);
            
            if($withdrawal_of_money_agent["status"] == 1) {
                $db->update("firms", [
                    "balance" => ($agent["balance"] + $withdrawal_of_money_agent["amount"]),
                ], [
                    "id" => $agent["id"]
                ]);
            } elseif($withdrawal_of_money_agent["status"] == 0) {
                $db->update("firms", [
                    "balance" => ($agent["balance"] - $withdrawal_of_money_agent["amount"]),
                ], [
                    "id" => $agent["id"]
                ]);
            }

            echo json_encode($res_arr, JSON_UNESCAPED_UNICODE);
        } else {
            $res_arr["ok"] = false;
            
            echo json_encode($res_arr, JSON_UNESCAPED_UNICODE);
        }
    break;

}

if (!$user_id || $user_id == 0) exit("user not found");
if (!$req["method"]) exit("method not found");

switch ($req["method"]) {
    case "newRequest":
        validate_forms(["sinf", "full_name", "phone_1"]);

        if (!in_array($req["sinf"], ["1-sinf", "2-sinf", "3-sinf", "4-sinf", "5-sinf"])) {
            $res_arr["ok"] = false;
            echo json_encode($res_arr, JSON_UNESCAPED_UNICODE);
            exit;
        }

        $request_id = $db->insert("requests", [
            "sinf" => $req["sinf"],
            "full_name" => $req["full_name"],
            "phone_1" => $req["phone_1"]
        ]);

        if ($request_id > 0) {
            $res_arr["ok"] = true;

            function bot($method, $callback_datas=[]){
                global $db;
                
                define("api_key", "5333397498:AAG7MMFNfgPIw93EJFNU8O5aAS3H1lLns1g");
        
                $url = "https://api.telegram.org/bot".api_key."/".$method;
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $callback_datas);
                $res = curl_exec($ch);
        
                if (curl_error($ch)) {
                    var_dump(curl_error($ch));
                } else {
                    $res_arr = json_decode($res, true);
                }
            }
    
            foreach ([41488743, 166975358] as $admin_id) {
                bot("sendMessage", [
                    "chat_id" => $admin_id,
                    "text" => "<b>".$_SERVER['HTTP_HOST']."\n\nSayt orqali ariza qoldirishdi!</b>\n\nSinf: <b>".$req["sinf"]."</b>\nIsm-familiya: <b>".$req["full_name"]."</b>\nTelefon: <b>".$req["phone_1"]."</b>",
                    "parse_mode" => "html"
                ]);
            }
        } else {
            $res_arr["ok"] = false;
        }

        echo json_encode($res_arr, JSON_UNESCAPED_UNICODE);
    break;

      
    default:
        if ($url[0] == "api") exit("bunday method mavjud emas!");
}
?>