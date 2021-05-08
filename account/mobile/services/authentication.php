<?php

require '../../../connection.php';
global $result;
$result = [];
if(verify_connection($con) === "failed")
    setResponse("result", "Could Not Establish Connection", true);

if(!isset($_POST["authentication_key"]))
    setResponse("result", "Error", true);

if($_POST["request"] == "email_to_salt" && !empty($_POST["email"])){
    $email = $_POST["email"];
    $query = selectData($con, "Salt", "Person", "Email='{$email}'", 1);
    if($query == "No Records Found"){
        setResponse("result", "No Account Found", true);
    } 
    else {
        setResponse("result", "Success", false);
        setResponse("salt",  $query[0]["Salt"] , true);
    }

}
else if($_POST["request"] == "password_validation" && !empty($_POST["email"]) && !empty($_POST["hash"])){
    
    $email = $_POST["email"];
    $hash = $_POST["hash"];
    $query = selectData($con, "PersonID", "Person", "(Email='{$email}') AND (Authentication_String='{$hash}')", 1);
    if($query == "No Records Found"){
        setResponse("result", "Invalid", false);
        setResponse("info", "", true);
    } 
    else {
        setResponse("result",  "Valid" , false);
        setResponse("info", "".$query[0]["PersonID"], true);
    }
}


else if($_POST["request"] == "sign_up" && !empty($_POST["email"]) && !empty($_POST["password"]) && !empty($_POST["salt"])){
    $email = $_POST["email"];
    $salt = $_POST["salt"];
    $hash = $_POST["password"];
    $dupeAccounts = selectData($con, "PersonID", "Person", "(Email='{$email}')", 1);
    if($dupeAccounts !== "No Records Found"){ 
        setResponse("result", "account_exists", false); 
        setResponse("info", "", true);
    }
    $response = insertData($con, "Person", "Email, Authentication_String, Salt", "'".$email."', '".$hash."', '".$salt."'");
    if($response === true){
        $key = mysqli_insert_id($con);
        setResponse("result", "account_created", false);
        setResponse("info", "".$key, true);
    }
    else {
        setResponse("result", "account_creation_failed", true);
    }
    
}
else if($_POST["request"] == "finish" && count(json_decode($_POST["data"])) > 0) {
    
    $d = json_decode($_POST["data"], true);
    $u = $d["username"];
    $imgCon = $d["hasProfileImage"];
    $bio = $d["bio"];
    $key = $d["user"];
    
    $queryChecker = selectData($con, "PersonID", "Person", "(Username='$u')", 1);
    if($queryChecker !== "No Records Found") {
        setResponse("result", "username_already_exists", true); 
    }
    
    
   $query = updateData($con, "Person", "Username='$u', Has_Profile_Image='$imgCon', Bio='$bio'", "PersonID='$key'");
   if($imgCon == "FALSE" && query == true) {
        setResponse("result", "sign_up_finished", true);
   }
   if($query != true)
        setResponse("result", "err_updating_row", true);
        
        mkdir("../AccountStorage/A-$key-Storage", 0750, true);
        
        $target_dir = "../AccountStorage/A-$key-Storage";
        if(move_uploaded_file($_FILES["file-0"]["tmp_name"], $target_dir . "/" . basename($_FILES["file-0"]["name"]))) 
            setResponse("result", "sign_up_finished", true);
        else
            setResponse("result", "err_uploading_profile_picture", true);
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