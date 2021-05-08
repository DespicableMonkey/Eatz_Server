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
    
    file_put_contents("log.txt", "LOL");
    
if($_POST["request"] == "publish" && !empty($_POST["recipe_name"])){
    
    $name = $_POST["recipe_name"];
    $desc = $_POST["recipe_description"];
    $difficulty = $_POST["difficulty"];
    $cookTime = $_POST["cook_time"];
    $servings = $_POST["servings"];
    $ingredients = json_decode($_POST["ingredients"]);
    $steps = json_decode($_POST["steps"]);
    $creator = $_POST["creator_id"];
    $publishedTo = $_POST["publishedTo"];
    $imgCount = $_POST["image_count"];
    $numSteps = count($steps);
    $cats = $_POST["categories"];
    
    
    $com = ($publishedTo == "PUBLIC") ? "1" : $publishedTo;
     
    $query = insertData($con, "Recipe", "FK_Person_Recipe, Recipe_Name, Recipe_Description, Recipe_Difficulty, Recipe_Time, Recipe_Servings, Recipe_Number_Of_Steps, Recipe_Has_Image, FK_Community_Recipe, Recipe_Categories", 
                                        "'$creator', '$name', '$desc', '$difficulty', '$cookTime', '$servings', '$numSteps', FALSE, '$com', '$cats'");
                                                    
     if(!($query == "true"))
         setResponse("result", "error_publishing_recipe", true);
     
     $key = mysqli_insert_id($con);
     
      mkdir("RecipesStorage/R-$key-Storage", 0751, true);
      $target_dir = "RecipesStorage/R-$key-Storage";
      
      for($i = 0; $i < $imgCount; $i++) {
          move_uploaded_file($_FILES["file-$i"]["tmp_name"], $target_dir . "/" . basename($_FILES["file-$i"]["name"]));
      }
      
     if(count($ingredients) > 0) {
         $values = "";
         foreach($ingredients as $ingredient) {
             $values .= "('$key', '$ingredient'),";
         }
         $values = substr($values, 0, -1);
         $queryIngredients = insertDatas($con, "RecipeIngredient", "FK_Recipe_RecipeIngredient, RecipeIngredient_Ingredient", "$values");
     }
     
     if(count($steps) > 0) {
         $values = "";
         foreach($steps as $stepNumber => $stepIdentity) {
             $img_exists = file_exists($target_dir. '/' . 'step-image-'.$stepNumber.'.jpg') ? "TRUE" : "FALSE";
             $values .= "('$key', '$stepNumber', '$stepIdentity', '$img_exists'),";
         }
         $values = substr($values, 0, -1);
         $querySteps = insertDatas($con, "RecipeSteps", "FK_RecipeSteps_Recipe, Step_Number, Step_Description, Step_Has_Image", "$values");
     }
     
     setResponse("result", "recipe_published", true);
}
if($_POST["request"] == "publish_inspiration"){
    
    
    $d = json_decode($_POST["data"], true);
    $user_key = $d["user_id"];
    $com_key = $d["community_id"];
    $desc = $d["description"];
    $ingredients = $d["ingredients"];
    
    $query = insertData($con, "Recipe", "FK_Person_Recipe, FK_Community_Recipe, Recipe_Description, Recipe_Categories, Recipe_Name", "'$user_key', '$com_key', '$desc', '$ingredients', 'INSPIRATION'");
    setResponse("result", "".($query == "true" ? "inspiration_published" : "err_publishing_inspiration"), true);

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