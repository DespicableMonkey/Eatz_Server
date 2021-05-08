<?php
$request = $_GET["request"];
$response_default = array();
$response_default["response"] = "invalid_request";
if($request == "ingredients-master") {
    $ingredients = file_get_contents("../../json/ingedients.json");
    die($ingredients);
}
else {
    die(json_encode($response_default));
}
?>