<?php
    include "../../connection.php";
    global $result;
    $result = [];
    
    
    if(verify_connection($con) === "failed")
        setResponse("result", "Could Not Establish Connection", true);
    if(!isset($_POST["authentication_key"]))
        setResponse("result", "Error", true);
if($_POST["request"] == "fetch" && !empty($_POST["info"])){
    $id = $_POST["info"];
    $response = selectData($con, "r.RecipeID, r.FK_Person_Recipe, r.Recipe_Name, r.Recipe_Description, r.Recipe_Difficulty, r.Recipe_Time, r.Recipe_Servings, r.Recipe_Number_Of_Steps, r.Recipe_Has_Image, r.Recipe_Categories, r.Recipe_Creation
                                ,p.PersonID, p.Username",
                                "Recipe r 
                                INNER JOIN Person p
                                ON p.PersonID=r.FK_Person_Recipe",
                                "r.RecipeID='$id'",
                                "1");
                                
    setResponse("result", count($response) == 1 ? "fetched" : "err_could_not_find", false);
    if(count($response) != 1)
        setResponse("result", json_decode ("{}"), true);
    
    $dict = array();
    $dict["RecipeID"] = $response[0]["RecipeID"];
    $dict["Title"] = $response[0]["Recipe_Name"];
    $dict["Description"] = $response[0]["Recipe_Description"];
    $dict["Difficulty"] = $response[0]["Recipe_Difficulty"];
    $dict["Time"] = $response[0]["Recipe_Time"];
    $dict["Servings"] = $response[0]["Recipe_Servings"];
    $dict["Number_Of_Steps"] = $response[0]["Recipe_Number_Of_Steps"];
    $dict["Categories"] = $response[0]["Recipe_Categories"];
    $dict["Creation"] = $response[0]["Recipe_Creation"];
    $dict["CreatorID"] = $response[0]["PersonID"];
    $dict["CreatorUsername"] = $response[0]["Username"];
       
    setResponse("data", $dict, true);
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