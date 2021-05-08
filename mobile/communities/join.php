<?php

require '../../connection.php';
global $result;
$result = [];

if(verify_connection($con) === "failed")
    setResponse("result", "Could Not Establish Connection", true);

if(!isset($_POST["authentication_key"]))
    setResponse("result", "Error", true);

if($_POST["request"] == "join" && isset($_POST["data"])){
    $data = json_decode($_POST["data"], true);
    $user_key = $data["user_key"];
    $code = $data["community_code"];
    
    $findCommunity = selectData($con, "CommunityID", "Community", "(Community_Join_Code='$code')", 1);
    if($findCommunity == "No Records Found")
        setResponse("result", "err_community_not_found", true); 
        
    $com_key = $findCommunity[0]["CommunityID"];
    $checkIfExists = selectData($con, "CommunityRelationID", "CommunityRelation", "(FK_Community_CommunityRelation='$com_key') AND (FK_Person_CommunityRelation='$user_key')", 1);
    if($checkIfExists != "No Records Found")
        setResponse("result", "err_already_in_community", true); 
    
    $query = insertData($con, "CommunityRelation", "FK_Community_CommunityRelation, FK_Person_CommunityRelation, PersonRole", "'$com_key', '$user_key', 'Cook'");
    
    if($query === true)
        setResponse("result", "community_joined", true);
    else 
        setResponse("result", "error", true);
    
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