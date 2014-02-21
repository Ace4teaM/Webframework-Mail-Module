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
 *  Webframework Module
 *  PHP Data-Model Implementation
*/


/**
* @author       developpement
*/
class MailMessage
{
   public function getId(){
      return $this->mailMessageId;
  }
   public function setId($id){
      return $this->mailMessageId = $id;
  }

    
    /**
    * @var      String
    */
    public $from;
    
    /**
    * @var      String
    */
    public $to;
    
    /**
    * @var      String
    */
    public $msg;
    
    /**
    * @var      String
    */
    public $subject;
    
    /**
    * @var      String
    */
    public $fromName;
    
    /**
    * @var      String
    */
    public $notify;
    
    /**
    * @var      String
    */
    public $contentType;    

}

/*
   mail_message Class manager
   
   This class is optimized for use with the Webfrmework project (www.webframework.fr)
*/
class MailMessageMgr
{
    /**
     * @brief Convert existing instance to XML element
     * @param $inst Entity instance (MailMessage)
     * @param $doc Parent document
     * @return New element node
     */
    public static function toXML(&$inst,$doc) {
        $node = $doc->createElement(strtolower("MailMessage"));
        
        $node->appendChild($doc->createTextElement("from",$inst->from));
        $node->appendChild($doc->createTextElement("to",$inst->to));
        $node->appendChild($doc->createTextElement("msg",$inst->msg));
        $node->appendChild($doc->createTextElement("subject",$inst->subject));
        $node->appendChild($doc->createTextElement("from_name",$inst->fromName));
        $node->appendChild($doc->createTextElement("notify",$inst->notify));
        $node->appendChild($doc->createTextElement("content_type",$inst->contentType));       

          
        return $node;
    }
    
    
    /*
      @brief Get entry list
      @param $list Array to receive new instances
      @param $cond SQL Select condition
      @param $db iDataBase derived instance
    */
    public static function getAll(&$list,$cond,$db=null){
       $list = array();
      
       //obtient la base de donnees courrante
       global $app;
       if(!$db && !$app->getDB($db))
         return false;
      
      //execute la requete
       $query = "SELECT * from mail_message where $cond";
       if(!$db->execute($query,$result))
          return false;
       
      //extrait les instances
       $i=0;
       while( $result->seek($i,iDatabaseQuery::Origin) ){
        $inst = new MailMessage();
        MailMessageMgr::bindResult($inst,$result);
        array_push($list,$inst);
        $i++;
       }
       
       return RESULT_OK();
    }
    
    /*
      @brief Get single entry
      @param $inst MailMessage instance pointer to initialize
      @param $cond SQL Select condition
      @param $db iDataBase derived instance
    */
    public static function bindResult(&$inst,$result){
          $inst->from = $result->fetchValue("from");
          $inst->to = $result->fetchValue("to");
          $inst->msg = $result->fetchValue("msg");
          $inst->subject = $result->fetchValue("subject");
          $inst->fromName = $result->fetchValue("from_name");
          $inst->notify = $result->fetchValue("notify");
          $inst->contentType = $result->fetchValue("content_type");          

       return true;
    }
    
    /*
      @brief Get single entry
      @param $inst MailMessage instance pointer to initialize
      @param $cond SQL Select condition
      @param $db iDataBase derived instance
    */
    public static function get(&$inst,$cond,$db=null){
       //obtient la base de donnees courrante
       global $app;
       if(!$db && !$app->getDB($db))
         return false;
      
      //execute la requete
       $query = "SELECT * from mail_message where $cond";
       if($db->execute($query,$result)){
            $inst = new MailMessage();
             if(!$result->rowCount())
                 return RESULT(cResult::Failed,iDatabaseQuery::EmptyResult);
          return MailMessageMgr::bindResult($inst,$result);
       }
       return false;
    }
    
    /*
      @brief Get single entry by id
      @param $inst MailMessage instance pointer to initialize
      @param $id Primary unique identifier of entry to retreive
      @param $db iDataBase derived instance
    */
    public static function getById(&$inst,$id,$db=null){
       //obtient la base de donnees courrante
       global $app;
       if(!$db && !$app->getDB($db))
         return false;
      
      //execute la requete
       $query = "SELECT * from mail_message where mail_message_id=".$db->parseValue($id);
       if($db->execute($query,$result)){
            $inst = new MailMessage();
             if(!$result->rowCount())
                 return RESULT(cResult::Failed,iDatabaseQuery::EmptyResult);
             self::bindResult($inst,$result);
          return true;
       }
       return false;
    }
    
   /*
      @brief Insert single entry with generated id
      @param $inst WriterDocument instance pointer to initialize
      @param $add_fields Array of columns names/columns values of additional fields
      @param $db iDataBase derived instance
    */
    public static function insert(&$inst,$add_fields=null,$db=null){
       //obtient la base de donnees courrante
       global $app;
       if(!$db && !$app->getDB($db))
         return false;
      
       //id initialise ?
       if(!isset($inst->mailMessageId)){
            $table_name = 'mail_message';
            $table_id_name = $table_name.'_id';
           if(!$db->execute("select * from new_id('$table_name','$table_id_name');",$result))
              return RESULT(cResult::Failed, cApplication::EntityMissingId);
           $inst->mailMessageId = intval($result->fetchValue("new_id"));
       }
       
      //execute la requete
       $query = "INSERT INTO mail_message (";
       $query .= " from,";
       $query .= " to,";
       $query .= " msg,";
       $query .= " subject,";
       $query .= " from_name,";
       $query .= " notify,";
       $query .= " content_type,";
       if(is_array($add_fields))
           $query .= implode(',',array_keys($add_fields)).',';
       $query = substr($query,0,-1);//remove last ','
       $query .= ")";
       
       $query .= " VALUES(";
       $query .= $db->parseValue($inst->from).",";
       $query .= $db->parseValue($inst->to).",";
       $query .= $db->parseValue($inst->msg).",";
       $query .= $db->parseValue($inst->subject).",";
       $query .= $db->parseValue($inst->fromName).",";
       $query .= $db->parseValue($inst->notify).",";
       $query .= $db->parseValue($inst->contentType).",";
       if(is_array($add_fields))
           $query .= implode(',',$add_fields).',';
       $query = substr($query,0,-1);//remove last ','
       $query .= ")";
 
       if($db->execute($query,$result))
          return true;

       return false;
    }
    
   /*
      @brief Update single entry by id
      @param $inst WriterDocument instance pointer to initialize
      @param $db iDataBase derived instance
    */
    public static function update(&$inst,$db=null){
       //obtient la base de donnees courrante
       global $app;
       if(!$db && !$app->getDB($db))
         return false;
      
       //id initialise ?
       if(!isset($inst->mailMessageId))
           return RESULT(cResult::Failed, cApplication::EntityMissingId);
      
      //execute la requete
       $query = "UPDATE mail_message SET";
       $query .= " from =".$db->parseValue($inst->from).",";
       $query .= " to =".$db->parseValue($inst->to).",";
       $query .= " msg =".$db->parseValue($inst->msg).",";
       $query .= " subject =".$db->parseValue($inst->subject).",";
       $query .= " from_name =".$db->parseValue($inst->fromName).",";
       $query .= " notify =".$db->parseValue($inst->notify).",";
       $query .= " content_type =".$db->parseValue($inst->contentType).",";
       $query = substr($query,0,-1);//remove last ','
       $query .= " where mail_message_id=".$db->parseValue($inst->mailMessageId);
       if($db->execute($query,$result))
          return true;

       return false;
    }
    
   /** @brief Convert name to code */
    public static function nameToCode($name){
        for($i=strlen($name)-1;$i>=0;$i--){
            $c = substr($name, $i, 1);
            if(strpos("ABCDEFGHIJKLMNOPQRSTUVWXYZ",$c) !== FALSE){
                $name = substr_replace($name,($i?"_":"").strtolower($c), $i, 1);
            }
        }
        return $name;
    }
    
    /**
      @brief Get entry by id's relation table
      @param $inst MailMessage instance pointer to initialize
      @param $obj An another entry class object instance
      @param $db iDataBase derived instance
    */
    public static function getByRelation(&$inst,$obj,$db=null){
        $objectName = get_class($obj);
        $objectTableName  = MailMessageMgr::nameToCode($objectName);
        $objectIdName = lcfirst($objectName)."Id";
        
        /*print_r($objectName.", ");
        print_r($objectTableName.", ");
        print_r($objectIdName.", ");
        print_r($obj->$objectIdName);*/
        
        $select;
        if(is_string($obj->$objectIdName))
            $select = ("mail_message_id = (select mail_message_id from $objectTableName where ".$objectTableName."_id='".$obj->$objectIdName."')");
        else
            $select = ("mail_message_id = (select mail_message_id  from $objectTableName where ".$objectTableName."_id=".$obj->$objectIdName.")");

        return MailMessageMgr::get($inst,$select,$db);
    }

}

?>