<?php
require_once("inc/globals.php");
global $app;

//print_r($app);

// charge une vue
if(cInputFields::checkArray(array("page"=>"cInputName")))
{
    $param = array();
    $app->showXMLView("view/mail/pages/".$_REQUEST["page"].".html",$param);
    exit;
}

$att = array();

// accueil
$app->showXMLView("view/mail/pages/index.html",$att);

?>