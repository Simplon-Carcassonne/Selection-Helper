<?php
require 'SoloLearnParser.php';
$debugOn = true;

if(isset($_POST['id']) || $debugOn){
    $parser = new SoloLearnParser($debugOn);
    $id = $debugOn ? "9271485" : $_POST['id'];
    $rawDatas = $parser->getPageContent($id);
    
    //return json from the parser to the JS client
    echo $parser->getJSONInfos($rawDatas);
}

