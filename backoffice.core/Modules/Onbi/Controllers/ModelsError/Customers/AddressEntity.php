<?php 
class AddressEntity
{


    /**
     * string
     * Name of the city
     */
    public	$city;
    
    /**
     * string
     * Name of the company
     */
    public $company;
    
    /**
     * string
     * Country ID
     */
    public $country_id = "BR";
    
    /**
     * string
     * Fax
     */
    public $fax;
    
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
     * Customer middle name
     */
    public $middlename;
    
    /**
     * string
     * Postcode
     */
    public $postcode;
    
    /**
     * string
     * Customer prefix
     */
    public $prefix;
    
    
    /**
     * int
     * ID of the region
     */
    public $region_id;
    
    /**
     * string
     * Name of the region
     */
    public $region;
    
    
    /**
     * ArrayOfString
     * Array of street addresses
     */
    public $street = array();
    
    /**
     * string
     * Customer suffix
     */
    public $suffix;
    
    /**
     * string
     * Telephone number
     */
    public $telephone;
    
    /**
     * boolean
     * True if the address is the default one for billing
     */
    public $is_default_billing = true;
    
    /**
     * boolean
     * True if the address is the default one for shipping
     */
    public $is_default_shipping = true;
    
    

    
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