<?php
$postData = json_decode(file_get_contents('php://input'),true);
$firstName = $postData['firstName'];
$lastName = $postedData['lastName'];

$arr = array('Status'=>'ok','firstName'=>$firstName);
echo json_encode($arr);