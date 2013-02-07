<?php
/*
   Webframework User Module
   PHP Data-Model Implementation
*/


/**
* @author       developpement
*/
class MailMessage
{
    
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

}

/*
   mail_message Class manager
   
   This class is optimized for use with the Webfrmework project (www.webframework.fr)
*/
class MailMessageMgr
{
    /*
      @brief Get entry list
      @param $list Array to receive new instances
      @param $cond SQL Select condition
      @param $db iDataBase derived instance
    */
    public static function getAll(&$list,$cond,$db=null){
       //obtient la base de donnees courrante
       global $app;
       if(!$db && !$app->getDB($db))
         return false;
      
      //execute la requete
       //...
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
       if($db->execute($query, $result)){
            $inst = new MailMessage();
          $inst->from = $db->fetchValue($result,"from");
          $inst->to = $db->fetchValue($result,"to");
          $inst->msg = $db->fetchValue($result,"msg");
          $inst->subject = $db->fetchValue($result,"subject");
          $inst->fromName = $db->fetchValue($result,"from_name");
          $inst->notify = $db->fetchValue($result,"notify");          

          return true;
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
      
       if(is_string($id))
           $id = "'$id'";
           
      //execute la requete
       $query = "SELECT * from mail_message where mail_message_id=$id";
       if($db->execute($query, $result)){
            $inst = new MailMessage();
          $inst->from = $db->fetchValue($result,"from");
          $inst->to = $db->fetchValue($result,"to");
          $inst->msg = $db->fetchValue($result,"msg");
          $inst->subject = $db->fetchValue($result,"subject");
          $inst->fromName = $db->fetchValue($result,"from_name");
          $inst->notify = $db->fetchValue($result,"notify");          

          return true;
       }
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