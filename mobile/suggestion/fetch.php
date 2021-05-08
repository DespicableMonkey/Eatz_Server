<?php

require '../../connection.php';
global $result;
$result = [];

if(verify_connection($con) == "failed")
    setResponse("result", "Could Not Establish Connection", true);

if(empty($_POST["authentication_key"]))
    setResponse("result", "Error", true);

if(($_POST["request"]) == "fetch_posts"){
    
    
    $sql = selectDataOrdered($con, "RecipeID, Recipe_Creation", "Recipe", "(Recipe_Name != 'INSPIRATION') AND (FK_Community_Recipe='1')", "Recipe_Creation");
    $res = array();
    for($i = 0; $i<count($sql); $i++){
        $res["r-$i"] = $sql[$i]["RecipeID"];
    }

    setResponse("result", "fetched", false);
    setResponse("data", $res, true);
    
}
else {
   setResponse("result", "err_request_not_found", true);
}

function setResponse($label, $response, $die) { global $result; $result[$label] = $response; if($die){ finish(); }}
function finish() { 
    global $result;
    print_r(json_encode($result)); 
    die();
}

?>