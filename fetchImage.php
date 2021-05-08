<?php
if(!(empty($_GET["authentication_key"]) || ($_GET["request"]) != "RecipeMain" || empty($_GET["r"]))) {
       header('content-type: image/png'); 
        //$header = "recipemain://";
        // $theImageURL = str_replace($header, "", ($_GET["url"]));
        $r = $_GET["r"];
        $link = "https://recex.applications.pulkith.com/mobile/recipes/RecipesStorage/R-$r-Storage/recipe-image.jpg";
        //die($link);
        die(file_get_contents($link));
}
else if(!(empty($_GET["authentication_key"]) || ($_GET["request"]) != "ProfilePicture" || empty($_GET["r"]))) {
       header('content-type: image/png'); 
        //$header = "recipemain://";
        // $theImageURL = str_replace($header, "", ($_GET["url"]));
        $r = $_GET["r"];
        $link = "https://recex.applications.pulkith.com/account/mobile/AccountStorage/A-$r-Storage/ProfileImage.jpg";
        //die($link);
        die(file_get_contents($link));
}
die();

?>