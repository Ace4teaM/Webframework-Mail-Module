<?php
/*
    ---------------------------------------------------------------------------------------------------------------------------------------
    (C)2012-2013 Thomas AUGUEY <contact@aceteam.org>
    ---------------------------------------------------------------------------------------------------------------------------------------
    This file is part of WebFrameWork.

    WebFrameWork is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    WebFrameWork is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with WebFrameWork.  If not, see <http://www.gnu.org/licenses/>.
    ---------------------------------------------------------------------------------------------------------------------------------------
*/

/*
 * Envoie un message
 * Rôle : Administrateur
 * UC   : mail_send_message
 */

require_once("inc/globals.php");
global $app;

//résultat de la requete
RESULT(cResult::Ok,cApplication::Information,array("message"=>"WFW_MSG_POPULATE_FORM"));
$result = cResult::getLast();

//requis
if(!$app->makeFiledList(
        $fields,
        array( 'to', 'subject', 'msg' ),
        cXMLDefault::FieldFormatClassName )
   ) $app->processLastError();

//optionnels
if(!$app->makeFiledList(
        $optional_fields,
        array( 'from', 'from_name', 'server_adr', 'port_num', 'notify', 'content_type' ),
        cXMLDefault::FieldFormatClassName )
   ) $app->processLastError();


if(!empty($_REQUEST)){

    //champs par défauts
    if(!isset($_REQUEST["from"]) || empty($_REQUEST["from"]))
        $_REQUEST["from"]  = $app->getCfgValue("mail_module","from");

    if(!isset($_REQUEST["from_name"]) || empty($_REQUEST["from_name"]))
        $_REQUEST["from_name"]  = $app->getCfgValue("mail_module","from_name");

    if(!isset($_REQUEST["server_adr"]) || empty($_REQUEST["server_adr"]))
        $_REQUEST["server_adr"]  = $app->getCfgValue("mail_module","server_adr");

    if(!isset($_REQUEST["port_num"]) || empty($_REQUEST["port_num"]))
        $_REQUEST["port_num"]  = $app->getCfgValue("mail_module","port_num");

    if(!isset($_REQUEST["content_type"]) || empty($_REQUEST["content_type"]))
        $_REQUEST["content_type"]  = "text/plain";

    if(!isset($_REQUEST["notify"]) || empty($_REQUEST["notify"]))
        $_REQUEST["notify"]  = NULL;

    // exemples JS
    if(!cInputFields::checkArray($fields, $optional_fields))
        goto failed;
    
    //initialise l'objet MailMessage
    $msg = new MailMessage();
    $msg->to       = $_REQUEST["to"];
    $msg->subject  = $_REQUEST["subject"];
    $msg->msg      = $_REQUEST["msg"];
    $msg->from     = $_REQUEST["from"];
    $msg->fromName = $_REQUEST["from_name"];
    $msg->notify   = $_REQUEST["notify"];
    $msg->contentType   = $_REQUEST["content_type"];

    //initialise l'objet MailServer
    $server = new MailServer();
    $server->serverAdr = $_REQUEST["server_adr"];
    $server->portNum   = $_REQUEST["port_num"];

    //envoie le message
    if(!MailModule::sendMessage($msg,$server))
        goto failed;

    //retourne le resultat de la dernière fonction
    $result = cResult::getLast();
}

goto success;
failed:
// redefinit le resultat avec l'erreur en cours
$result = cResult::getLast();

success:

// Traduit le nom du champ concerné
if(isset($result->att["field_name"]) && $app->getDefaultFile($default))
    $result->att["field_name"] = $default->getResultText("fields",$result->att["field_name"]);

// Traduit le résultat
$att = $app->translateResult($result);

// Ajoute les arguments reçues en entrée au template
$att = array_merge($att,$_REQUEST);

/* Génére la sortie */
$format = "html";
if(cInputFields::checkArray(array("output"=>"cInputIdentifier")))
    $format = $_REQUEST["output"] ;

switch($format){
    case "xarg":
        header("content-type: text/xarg");
        echo xarg_encode_array($att);
        break;
    case "xml":
        header("content-type: text/xml");
        $doc = new XMLDocument();
        $rootEl = $doc->createElement('data');
        $doc->appendChild($rootEl);
        $doc->appendAssocArray($rootEl,$att);
        echo '<?xml version="1.0" encoding="UTF-8" ?>'.$doc->saveXML( $doc->documentElement );
        break;
    case "html":
        echo $app->makeFormView($att,$fields,$optional_fields,$_REQUEST);
        break;
    default:
        RESULT(cResult::Failed,Application::UnsuportedFeature);
        $app->processLastError();
        break;
}

// ok
exit($result->isOk() ? 0 : 1);

?>