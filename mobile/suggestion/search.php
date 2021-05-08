<?php

require '../../connection.php';
global $result;
$result = [];

if(verify_connection($con) == "failed")
    setResponse("result", "Could Not Establish Connection", true);

if(($_POST["request"]) == "fetch_category"){
    $category = $_POST["info"];
    
    $sql = selectDataOrdered($con, "RecipeID, Recipe_Creation, Recipe_Categories", "Recipe", "(Recipe_Name != 'INSPIRATION') AND (FK_Community_Recipe='1') AND Recipe_Categories LIKE '%$category%'", "Recipe_Creation");
        if($sql == "No Records Found") {
         setResponse("result", "fetched", false);
         setResponse("data", json_decode('{}'), true);
    }
    $res = array();
    for($i = 0; $i<count($sql); $i++){
        $res["r-$i"] = $sql[$i]["RecipeID"];
    }

    setResponse("result", "fetched", false);
    setResponse("data", $res, true);
    
}
if(($_POST["request"]) == "fetch_search"){
    $search = $_POST["info"];
    
    $sql = selectDataOrdered($con, "RecipeID, Recipe_Creation", "Recipe", "(Recipe_Name != 'INSPIRATION') AND (FK_Community_Recipe='1') AND ((Recipe_Name LIKE '%$search%') OR (Recipe_Description LIKE '%$search%'))", "Recipe_Creation");
    $res = array();
    if($sql == "No Records Found") {
         setResponse("result", "fetched", false);
         setResponse("data", json_decode('{}'), true);
    }
    for($i = 0; $i<count($sql) && $i < 250; $i++){
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