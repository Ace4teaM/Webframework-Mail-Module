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

/**
 * Gestionnaire de courriers électroniques
 * Librairie PHP5
 */


require_once("php/class/bases/iModule.php");
require_once("php/xml_default.php");

    
class MailModule implements iModule
{
    /**
     * @brief Initialise le module
     * @param $local_path Chemin d'accès local vers ce dossier
     */
    public static function load($local_path){
        global $app;
        
        //chemins d'acces 
        //$this_path = dirname(__FILE__);
        //$this_relative_path = relativePath($this_path,$local_path);
        
        //print_r($this_path);
        
        //initialise la configuration
        $modParam = parse_ini_file("$local_path/config.ini", true);
        $app->config = array_merge_recursive($modParam,$app->config);

        //inclue le model de données
        require_path($local_path."/".$app->getCfgValue("mail_module","lib_path"));
    }
    
    public static function libPath(){
        global $app;
        return $app->getLibPath("mail_mod").$app->getCfgValue("mail_module","lib_path");
    }
    
    public static function makeView($name,$attributes,$template_file){ 
    }
    
    /** 
     * Envoi un message
     * 
     * @param type $msg Instance initialisé d'une classe MailMessage
     * @return Résultat de la procédure
     */
    public static function sendMessage(MailMessage $msg){ 
        global $app;
        $db=null;
    
        // obtient les infos sur le serveur et l'expediteur par defaut
        $mail_server = $app->getCfgValue("mail_module","server");
        $mail_port   = $app->getCfgValue("mail_module","port");
        $from        = $app->getCfgValue("mail_module","from");
        $from_name   = $app->getCfgValue("mail_module","from_name");
    
        // sujet
        $subject = '=?UTF-8?B?'.base64_encode($msg->subject).'?=';
        
        // nom de l'expediteur
        if(!empty($msg->from_name))
                $from_name = $msg->from_name;
        if(!empty($from_name))
                $from_name = '=?UTF-8?B?'.base64_encode($from_name).'?=';

        // initialise la connexion
        $sock = new cSocket();
        $rsp  = "";

        //--------------------------------------------------------------
        // fabrique le corps du message

        $content = "";
        $content_type = "text/plain";
        /*if($msg->use_template && !empty($msg->template) && $msg->html_mode))
        {
            //transforme le document 
            $template = new cXMLTemplate();
            if(!$template->Initialise(ROOT_PATH."/private/".$_REQUEST['template'],NULL,NULL,NULL,$_REQUEST))
                    rpost_result(ERR_FAILED,"cant_load_template");
            $content = $template->Make();
            $content_type = "text/html";
        }
        else*/
            $content = $msg->msg;

        // puts
        // envoie une commande et verifie si c'est un succees, dans le cas echeant prepare la fin de la communication avec le socket
        //
        function puts($cmd){
            global $sock;
            global $rsp;

            $rsp = $sock->Puts($cmd);
            //  echo($rsp);
            //verifie le code de retourne, accept les messages 2xx et 3xx. retourne le reste comme erreur
            $code_char = substr($rsp,0,1);
            if($code_char != "2" && $code_char != "3")
                    return 0;

            return $rsp;
        }

        //--------------------------------------------------------------
        // envoie le mail
        $to = $msg->to;

        //ouvre la connection
        if($sock->Open($mail_server,$mail_port)!=ERR_OK)
        {
          return RESULT(cResult::Failed, "MAIL_SOCKET_OPEN", array("errno"=>$sock->errno,"errstr"=>$sock->errstr));
        }

        //authentification
        if(!puts("HELO ".(isset($_SERVER['SERVER_ADDR'])?$_SERVER['SERVER_ADDR']:"noname")."\n")) goto onerror;

        //expediteur
        if(!puts('MAIL FROM: <'.$from.">\n")) goto onerror;

        //destinataire
        if(!puts("RCPT TO: <$to>\n")) goto onerror;

        //passe en mode data pour ecrire le contenu du mail
        if(!puts("DATA\n")) goto onerror;

        //construit et envoie le corps du message     
        $msg = "";
        $msg .= 'Content-Type: '.$content_type.'; charset=UTF-8'."\n";
        $msg .= 'Content-Transfer-Encoding: 8bit'."\n";
        $msg .= "To: $to\n";
        if(!empty($from_name))
          $msg .= 'From: "'.$from_name.'" <'.$from.">\n"; 
        else
          $msg .= 'From: '.$from."\n";
        $msg .= 'Subject: '.$subject."\n";
        $msg .= "\n";
        $msg .= $content."\n";
        $msg .= "\r\n.\r\n"; // fin de corps du message
        if(!puts($msg)) goto onerror;

        // quit 
        $sock->Puts("QUIT\n");
        $sock->Close();

        goto ok;

        // ERREUR 
        onerror:
        // quit 
        $sock->Puts("QUIT\n");
        $sock->Close();
        return RESULT(cResult::Ok, MailModule::MailSending);

        // OK
        ok:
        return RESULT(cResult::Ok, MailModule::MailSended);
    }
    
    /** 
     * Traduit un nom d'attribut
     * 
     * @param string $name Nom de l'attribut
     * @return Texte de remplacement. Si le nom d'attribut est inconnu, l'identifiant est retourné
     */
    public static function translateAttributeName($name){ 
        switch($name){
            case "to":
                return "Adresse du destinataire";
            case "from":
                return "Adresse de l'expéditeur";
            case "subject":
                return "Sujet";
            case "msg":
                return "Message";
            case "from_name":
                return "Nom de l'expéditeur";
            case "server":
                return "Adresse du serveur";
            case "notify":
                return "Adresse de notification";
            case "port":
                return "Numéro de port";
            case "template":
                return "Nom du fichier template";
        }
        return $name;
    }
    
}

?>
