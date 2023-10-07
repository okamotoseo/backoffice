<?php 
class CustomerEntity
{
    /**
     * string
     * Customer email
     */
    	
    public $email;
    /**
     * string
     * Customer first name
     */
    public $firstname;
    
    /**
     * string
     * Customer last name
     */
    public $lastname;
    
    /**
     * string
     * Customer password
     */
    public $password;
    
    /**
     * int
     * Website ID
     */
    public $website_id = 1;
    
    /**
     * int
     * Store ID
     */
    public $store_id = 1;
    
    /**
     * int
     * Group ID
     */
    public $group_id = 1;
    
    /**
     * string
     * Customer prefix (optional)
     */
    public $prefix;
    
    /**
     * string
     * Customer suffix (optional)
     */
    public $suffix;
    
    /**
     * string
     * Customer date of birth (optional)
     */
    public $dob;
    
    /**
     * string
     * Customer tax/VAT number (optional)
     */
    public $taxvat;
    
    /**
     * int
     * Customer gender: 1 - Male, 2 - Female (optional)
     */
    public $gender;
    
    /**
     * string
     * Customer middle name/initial (optional)
     */
    public $middlename;
    
    /**
     * string
     * Customer type
     */
    public $mode = "guest";
    
    
    

    
    public function __construct( $data = array() ) 
    {
        
        
        foreach($data as $key => $value)
        {
            $column_name = str_replace('-','_',$key);
            $this->{$column_name} = $value;
        }
        
    }
    
    
    
}

?>