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

//requis
$fields = array(
    'to'=>'cInputMail',
    'subject'=>'cInputString',
    'msg'=>''
);

//optionnels
$optional_fields = array(
    'from'=>'cInputMail',
    'from_name'=>'cInputString',
    'server'=>'',
    'notify'=>'cInputMail',
    'port'=>'cInputInteger',
    'template'=>'cInputUNIXFileName',    
);
  
//résultat de la requete
$result = NULL;

// exemples JS
if(cInputFields::checkArray($fields, $optional_fields))
{
    $msg = new MailMessage();
    //initialise l'objet
    $msg->to       = $_REQUEST["to"];
    $msg->subject  = $_REQUEST["subject"];
    $msg->msg      = $_REQUEST["msg"];
    $msg->from     = isset($_REQUEST["from"]) ? $_REQUEST["from"] : NULL;
    $msg->fromName = isset($_REQUEST["from_name"]) ? $_REQUEST["from_name"] : NULL;
    $msg->notify   = isset($_REQUEST["notify"]) ? $_REQUEST["notify"] : NULL;
 
    //envoie le message
    if(!MailModule::sendMessage($msg))
        goto failed;

    //retourne le resultat de cette fonction
    $result = cResult::getLast();

    goto success;  
}

failed:
// redefinit le resultat avec l'erreur en cours
$result = cResult::getLast();


success:

// Traduit le nom du champ concerné
if(isset($result->att["field_name"]))
    $result->att["field_name"] = MailModule::translateAttributeName($result->att["field_name"]);

// Traduit le résultat
$att = Application::translateResult($result);

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
    case "html":
        echo $app->makeXMLView("view/mail/pages/send_message.html",$att);
        break;
    default:
        RESULT(cResult::Failed,Application::UnsuportedFeature);
        $app->processLastError();
        break;
}

// ok
exit($result->isOk() ? 0 : 1);

?>