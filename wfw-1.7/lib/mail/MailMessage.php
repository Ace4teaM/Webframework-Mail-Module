<?php
//
// +------------------------------------------------------------------------+
// | PHP Version 5                                                          |
// +------------------------------------------------------------------------+
// | Copyright (c) All rights reserved.                                     |
// +------------------------------------------------------------------------+
// | This source file is subject to version 3.00 of the PHP License,        |
// | that is available at http://www.php.net/license/3_0.txt.               |
// | If you did not receive a copy of the PHP license and are unable to     |
// | obtain it through the world-wide-web, please send a note to            |
// | license@php.net so we can mail you a copy immediately.                 |
// +------------------------------------------------------------------------+
// | Author:                                                                |
// +------------------------------------------------------------------------+
//
// $Id$
//


/**
* @author       AceTeaM
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
    
    /**
    * @var      String
    */
    public $template;
    
    /**
    * @var      String
    */
    public $contentType;
}

?>