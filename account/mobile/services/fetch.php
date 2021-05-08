<?php

require '../../../connection.php';
global $result;
$result = [];

if(verify_connection($con) === "failed")
    setResponse("result", "Could Not Establish Connection", true);

if(!isset($_POST["authentication_key"]))
    setResponse("result", "Error", true);

if($_POST["request"] == "fetch" && !empty($_POST["authentication_key"]) && !empty($_POST["ID"])){
    $id = $_POST["ID"];
    $query = selectData($con, "PersonID, Firstname, Lastname, Username, Email, Created", "Person",  "PersonID='{$id}'", 1);
    if($query == "No Records Found"){
        setResponse("result", "No Account Found", true);
    } 
    else {
        setResponse("result", "Success", false);
        setResponse("PersonID",  $query[0]["PersonID"] , false);
        setResponse("Firstname",  $query[0]["Firstname"] , false);
        setResponse("Lastname",  $query[0]["Lastname"] , false);
        setResponse("Username",  $query[0]["Username"] , false);
        setResponse("Email",  $query[0]["Email"] , false);
        setResponse("Created",  $query[0]["Created"] , false);
        
        $queryCommunities = selectData($con, "FK_Community_CommunityRelation", "CommunityRelation", "FK_Person_CommunityRelation='{$id}'", "");
        $resultCommunitiesData = array();        
        if($queryCommunities == "No Records Found")
            setResponse("Communities", json_decode("{}"), false);
        else {
            foreach($queryCommunities as &$element){ $element = $element['FK_Community_CommunityRelation']; }
            
            $communitiesData = selectData($con, "Community_Image, Privacy, Community_Description, CommunityID, Community_Join_Code, Community_Title", "Community","CommunityID in (".sprintf("'%s'", implode("','", $queryCommunities )).")", "" );
            for($i = 0; $i < sizeof($communitiesData); $i++){
                 $communitiesData[$i]["Community_Image"] = "https://recex.applications.pulkith.com/mobile/communities/CommunitiesStorage/C-".$communitiesData[$i]["CommunityID"]."-Storage/CommunityImage.jpg";
                 $resultCommunitiesData["Community-".($i+1)] = $communitiesData[$i];
            }
            setResponse("Communities", $resultCommunitiesData, false);
        }
        
        setResponse("status", "completed", true);
    }

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