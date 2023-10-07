<?php
define ('DATE_FORMAT', 'Y-m-d\TH:i:s\Z');

class ConfigMws {
    

   
//    public $DATE_FORMAT = 'Y-m-d\TH:i:s\Z';
   /************************************************************************
    * REQUIRED
    *
    * * Access Key ID and Secret Acess Key ID, obtained from:
    * http://aws.amazon.com
    *
    * IMPORTANT: Your Secret Access Key is a secret, and should be known
    * only by you and AWS. You should never include your Secret Access Key
    * in your requests to AWS. You should never e-mail your Secret Access Key
    * to anyone. It is important to keep your Secret Access Key confidential
    * to protect your account.
    ***********************************************************************/
//     define('AWS_ACCESS_KEY_ID', 'AKIAJFM5UN5ACQ5H3KVQ');
//     define('AWS_SECRET_ACCESS_KEY', 'Y9g96+CCMzI/hZSHDK7c2Ob0Y5qsrcnDX7QnnWTO');

    public $AWS_ACCESS_KEY_ID = 'AKIAJFM5UN5ACQ5H3KVQ';
    public $AWS_SECRET_ACCESS_KEY = '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';
    
   /************************************************************************
    * REQUIRED
    * 
    * All MWS requests must contain a User-Agent header. The application
    * name and version defined below are used in creating this value.
    ***********************************************************************/
//     define('APPLICATION_NAME', 'Sysplace');
//     define('APPLICATION_VERSION', '1.0');
    
    public $APPLICATION_NAME = 'Sysplace';
    public $APPLICATION_VERSION = '1.0';
    
   /************************************************************************
    * REQUIRED
    * 
    * All MWS requests must contain the seller's merchant ID and
    * marketplace ID.
    ***********************************************************************/
//     define ('MERCHANT_ID', 'A2Q3Y263D00KWC');
//     define ('MERCHANT_ID', 'AX3BXOBQKW3QC');
//     public $MERCHANT_ID = 'AX3BXOBQKW3QC';
    
//     public $marketplaceIdArray = array("Id" => array('A2Q3Y263D00KWC'));
    
     
    
    
}