<?php 


class MWS extends ConfigMWS{
    
    public $db;
    
    public $store_id;
    
    public $seller_id;
    
    public $site_id;
    
    public $token;
    
    public $description;
    
    public $MWS;
    
    public $library;
    
    
    
//     public $library = array('/var/www/html/app_mvc/Modules/Amazon/library/amazon-mws-v20090101-php-2016-09-21/src/',
// '/var/www/html/app_mvc/Modules/Amazon/library/MWSProductsPHPClientLibrary-2011-10-01/src/');
    
    /************************************************************************
     * Uncomment to configure the client instance. Configuration settings
     * are:
     *
     * - MWS endpoint URL
     * - Proxy host and port.
     * - MaxErrorRetry.
     ***********************************************************************/
    // IMPORTANT: Uncomment the approiate line for the country you wish to
    // sell in:
    // United States:
    public $serviceUrl = "https://mws.amazonservices.com/";
    // United Kingdom
    //$serviceUrl = "https://mws.amazonservices.co.uk";
    // Germany
    //$serviceUrl = "https://mws.amazonservices.de";
    // France
    //$serviceUrl = "https://mws.amazonservices.fr";
    // Italy
    //$serviceUrl = "https://mws.amazonservices.it";
    // Japan
    //$serviceUrl = "https://mws.amazonservices.jp";
    // China
    //$serviceUrl = "https://mws.amazonservices.com.cn";
    // Canada
    //$serviceUrl = "https://mws.amazonservices.ca";
    // India
    //$serviceUrl = "https://mws.amazonservices.in";
    
//     public $config = array (
//         'ServiceURL' => "https://mws.amazonservices.com",
//         'ProxyHost' => null,
//         'ProxyPort' => -1,
//         'MaxErrorRetry' => 3,
//     );
    
//     public $service;
    
    
    public function __construct( $db = false, $library, $storeId = null ) {
        
        $this->db = $db;
        
        $this->store_id = $storeId;
        
        $this->library = $library;
        
        if(isset($this->db) AND isset($this->store_id)){
            
            $this->Load();
            
            /************************************************************************
             * OPTIONAL ON SOME INSTALLATIONS
             *
             * Set include path to root of library, relative to Samples directory.
             * Only needed when running library from local directory.
             * If library is installed in PHP include path, this is not needed
             ***********************************************************************/
            
            set_include_path(get_include_path() . PATH_SEPARATOR . $this->library);
           
            
            /************************************************************************
             * OPTIONAL ON SOME INSTALLATIONS
             * Autoload function is reponsible for loading classes of the library on demand
             *
             * NOTE: Only one __autoload function is allowed by PHP per each PHP installation,
             * and this function may need to be replaced with individual require_once statements
             * in case where other framework that define an __autoload already loaded.
             *
             * However, since this library follow common naming convention for PHP classes it
             * may be possible to simply re-use an autoload mechanism defined by other frameworks
             * (provided library is installed in the PHP include path), and so classes may just
             * be loaded even when this function is removed
             ***********************************************************************/
            
            function __autoload($className){
                $filePath = str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
                $includePaths = explode(PATH_SEPARATOR, get_include_path());
                foreach($includePaths as $includePath){
                    if(file_exists($includePath . DIRECTORY_SEPARATOR . $filePath)){
                        require_once $includePath . DIRECTORY_SEPARATOR . $filePath;
                    }
                }
            }
            
            
            
            
        }
        
    }
    
    
    
    
    
    public function Load()
    {
        
        if(!empty($this->store_id) ){
            
            $query = $this->db->query('SELECT * FROM module_amazon WHERE store_id = ?',
                array($this->store_id ) );
            
            $fetch = $query->fetch(PDO::FETCH_ASSOC);
            if(!empty($fetch)){
                foreach($fetch as $key => $value)
                {
                    $column_name = str_replace('-','_',$key);
                    $this->{$column_name} = $value;
                }
            }else{
                return;
            }
            
        }else{
            
            return;
            
        }
        
        
    }
    
    
    
    

    
}