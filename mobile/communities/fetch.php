<?php

require '../../connection.php';
global $result;
$result = [];

file_put_contents("log.txt", $_POST["info"]);

if(verify_connection($con) === "failed")
    setResponse("result", "Could Not Establish Connection", true);

if(!isset($_POST["authentication_key"]))
    setResponse("result", "Error", true);

if($_POST["request"] == "fetch_posts" && isset($_POST["info"])){
    $id = $_POST["info"];
    
    $sql1 = selectData($con, "CommunityID, Community_Title, Community_Description, Privacy, Community_Join_Code", "Community", "CommunityID='$id'", "");
    $sql3 = selectDataOrdered($con, "RecipeID, Recipe_Creation", "Recipe", "FK_Community_Recipe='$id'", "Recipe_Creation");
    $sql2 = selectData($con, "FK_Person_CommunityRelation", "CommunityRelation", "FK_Community_CommunityRelation='$id'", "");
    $res1 = array();
    $res2 = array();
    foreach($sql3 as $r) 
        array_push($res1, $r["RecipeID"]);
    foreach($sql2 as $r) 
        array_push($res2, $r["FK_Person_CommunityRelation"]);
    
    
    setResponse("result", "fetched", false);
    setResponse("community_information", ($sql1[0]), false);
    setResponse("community_people", ($res2), false);
    setResponse("community_posts", ($res1), true);
    
    
    
    
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