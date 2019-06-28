<?php

$USE = $_GET;

if($USE["action"] === "getdata" && $USE["form_id"] && $USE["form_token"] && $USE["form_build_id"] && $USE["path"]){
    $path = realpath("/drupal/sites/default/files/folder1/test1.txt");
    
    if(file_exists($path)){
        $data = file_get_contents($path);
		echo $data;
    }else{
        echo "nada";
    }
    //die();
}
?>