<?php
if(!(empty($_GET["authentication_key"]) || ($_GET["request"]) != "FetchImage" || empty($_GET["url"]))) {
        header('content-type: image/png'); 
        $header = "https://recex.applications.pulkith.com/mobile/communities/";
        $theImageURL = str_replace($header, "", ($_GET["url"]));
        die(file_get_contents($theImageURL));
}
die();

?>