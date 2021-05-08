<?php
    include "../../connection.php";
    global $result;
    $result = [];
    
    /* use to clear log file */
    //file_put_contents("log.txt","");
    
    if(verify_connection($con) === "failed")
        setResponse("result", "Could Not Establish Connection", true);
    
    
    if(!isset($_POST["authentication_key"]))
        setResponse("result", "Error", true);

if($_POST["request"] == "create" && !empty($_POST["info_one"]) && !empty($_POST["info_two"]) &&  !empty($_POST["info_three"]) &&  !empty($_POST["info_four"])){
    $name = $_POST["info_one"];
    $desc= $_POST["info_two"];
    $img = $_POST["info_three"];
    $person = $_POST["info_four"];
    
    $joinCode = substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'),1, 8);
    
    $query = insertData($con, "Community", "Community_Title, Community_Description, Community_Image, Community_Join_Code", "'$name', '$desc', 'CommunityImage', '$joinCode'");
     if(!($query == "true")){
         setResponse("result", "error creating community", true);
     } 
     
     $key = mysqli_insert_id($con);
     
     $query2 = insertData($con, "CommunityRelation", "FK_Community_CommunityRelation, FK_Person_CommunityRelation, PersonRole", "'$key', '$person', 'Cheif Chef'");
     if(!($query2 == "true")){
         file_put_contents("log.txt", "".mysqli_error($con));
        setResponse("result", "error assigning role", true);
     }
     
     mkdir("CommunitiesStorage/C-$key-Storage", 0750, true);
     
     $target_dir = "CommunitiesStorage/C-$key-Storage";
     $target_dir = $target_dir . "/" . basename($_FILES["file-0"]["name"]);

    if (move_uploaded_file($_FILES["file-0"]["tmp_name"], $target_dir)) 
        setResponse("result", "community_created", true);
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