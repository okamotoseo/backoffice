<?php 

class AzAttributesModel extends MainModel
{
    
    public $id;

	public $store_id;
	
	public $category_id;
	
	public $tree_id;
	
	public $attribute_id;
	
	public $category;
	
	public $az_attributes_id;
	
	public $xsdName;
	
	public $xsd;
	
	public $choice;
	
	public $alias;
	
	public $hierarchy;
	
	public $type;
	
	public $set_attribute;
	
	public $attributes = array();
	
	public $xpath;
	
	public $xsdBase;
	
	public $xpathBase;
	
	public $simpleTypeValXsd = array();

	
	
	public function __construct($db = false,  $controller = null, $storeId = null)
	{
		$this->db = $db;
		 
		$this->store_id = $storeId;
		 
		$this->controller = $controller;
		
		$this->xsdBase = "https://backoffice.sysplace.com.br/Modules/Amazon/Xsd/amzn-base.xsd";
		 
		if(isset($this->controller)){
			 
			$this->parametros = $this->controller->parametros;
			 
			$this->userdata = $this->controller->userdata;
			 
			$this->store_id = $this->controller->userdata['store_id'];
			
			 
		    $key = array_search('Xsd', $this->parametros);
		    if(!empty($key)){
		    	$this->xsdName = get_next($this->parametros, $key);
			    $key = array_search($this->xsdName, $this->parametros);
			    $this->choice = get_next($this->parametros, $key);
			    $this->LoadXsd();
			    $this->LoadXsdBase();
		    }
		    
		}
	}
	
	public function ValidateForm() {
	    
	    if ( 'POST' == $_SERVER['REQUEST_METHOD'] && ! empty ( $_POST ) ) {
	        
	        foreach ( $_POST as $property => $value ) {
	            
	            if(property_exists($this,$property)){
	                
	                if( !empty( $value ) ){
	                    
	                    $this->{$property} = $value;
	                    
	                }else{
	                    $required = array('category_id', 'attribute_id', 'attribute', 'ml_attributes_id');
	                    
	                    if(in_array($property, $required)){
	                        
	                        $this->form_msg = "<div class='alert alert-danger alert-dismissable'> There are empty fields. Data has not been sent.</div>";
	                        
	                        return;
	                        
	                    }
	                    
	                }
	                
	            }
	            
	        }
	        
	        return true;
	        
	    } else {
	        
	        return;
	        
	    }
	    
	}
	
	
	public function LoadXsdBase(){
	
		if(empty($this->xsdBase)){
		
			return array();
		}
	
	
		$xml_file = getSSLFile($this->xsdBase);
		$doc = new DOMDocument();
		$doc->preserveWhiteSpace = false;
		$doc->loadXML(mb_convert_encoding($xml_file, 'utf-8', mb_detect_encoding($this->xsdBase)));
		$this->xpathBase = new DOMXPath($doc);
		$this->xpathBase->registerNamespace('xs', 'http://www.w3.org/2001/XMLSchema');
		$this->xpathBase->registerPHPFunctions();
		
		return;
		
	
	}
	
	public function LoadXsd(){
	
		if(empty($this->xsdName)){
	
			$xsdInfo = $this->getXsdFromCategoryRelationship();
				
			if(!empty($xsdInfo['xsd'])){
				$this->xsdName = $xsdInfo['xsd'];
				$this->choice = $xsdInfo['choice'];
				$this->tree_id = $xsdInfo['tree_id'];
				$this->hierarchy = $xsdInfo['hierarchy'];
			}else{
				return false;
			}
		}
	
		$sql = "SELECT * FROM az_category_xsd WHERE name LIKE '{$this->xsdName}'";
		$query = $this->db->query($sql);
	
		foreach($query->fetch(PDO::FETCH_ASSOC) as $key => $value){
			$column_name = str_replace('-','_',$key);
			$this->{$column_name} = $value;
		}
		$xml_file = getSSLFile($this->xsd);
		$doc = new DOMDocument();
		$doc->preserveWhiteSpace = false;
		$doc->loadXML(mb_convert_encoding($xml_file, 'utf-8', mb_detect_encoding($this->xsd)));
		$this->xpath = new DOMXPath($doc);
		$this->xpath->registerNamespace('xs', 'http://www.w3.org/2001/XMLSchema');
		$this->xpath->registerPHPFunctions();
	
		return true;
	
	}
	
	public function GetProductType(){
		
		if(!isset($this->xpath)){
			return array();
		}
		$this->attributes = array();
		
// 		$elementDefs = $xpath->evaluate("/xs:schema/xs:element[@name='Home']/xs:complexType/xs:sequence/xs:element");
		$query = "/xs:schema/xs:element[@name='{$this->xsdName}']/xs:complexType/xs:sequence/xs:element";
		
// 		$elementDefs = $xpath->query($query);
		$entries = $this->xpath->query($query);
		// 			pre($entries);die;
		foreach ($entries as $entry) {
			$value = array();
			$name = !empty($entry->getAttribute('name')) ? $entry->getAttribute('name') : $entry->getAttribute('ref') ;
			$type = !empty($entry->getAttribute('type')) ? $entry->getAttribute('type') : '' ;
			$minOccurs = !empty($entry->getAttribute('minOccurs')) ? $entry->getAttribute('minOccurs') : 0 ;
			$maxOccurs = !empty($entry->getAttribute('maxOccurs')) ? $entry->getAttribute('maxOccurs') : '' ;
			if($type == ''){
				$queryType = $query."[@name='{$name}']/xs:simpleType/xs:restriction/xs:enumeration";
				$elementDefs = $this->xpath->query($queryType);
				foreach ($elementDefs as $elementDef) {
					$value[] = !empty($elementDef->getAttribute('value')) ? $elementDef->getAttribute('value') : '' ;
				}
			}
			$valueJson = !empty($value) ? json_encode(array('values' => $value)) : '';
			$this->attributes[] =  array('name' => $name, 'type' => $type, 'minOccurs' => $minOccurs, 'maxOccurs' => $maxOccurs, 'values' => $valueJson);
		}
		
		return $this->attributes;
		
	}
	
	public function GetProductTypeChoice(){
		if(!isset($this->xpath)){
			return array();
		}
		$this->attributes = array();
		// 		$elementDefs = $xpath->evaluate("/xs:schema/xs:element[@name='Home']/xs:complexType/xs:sequence/xs:element");
// 		$query = "/xs:schema/xs:element[@name='{$this->xsdName}']/xs:complexType/xs:sequence/xs:element";
		$query = "/xs:schema/xs:complexType[@name='{$this->choice}']/xs:sequence/xs:element";
	
		// 		$elementDefs = $xpath->query($query);
		$entries = $this->xpath->query($query);
		foreach ($entries as $entry) {
			
			$value = array();
			$name = !empty($entry->getAttribute('name')) ? $entry->getAttribute('name') : $entry->getAttribute('ref') ;
			$type = !empty($entry->getAttribute('type')) ? $entry->getAttribute('type') : '' ;
			
			$minOccurs = !empty($entry->getAttribute('minOccurs')) ? $entry->getAttribute('minOccurs') : 0 ;
			$maxOccurs = !empty($entry->getAttribute('maxOccurs')) ? $entry->getAttribute('maxOccurs') : '' ;
			
			$valueJson = '';
			
			if($name == 'ColorMap'){
				$colorMap= array();
				$queryType = "/xs:schema/xs:element[@name='ColorMap']/xs:simpleType/xs:restriction/xs:enumeration";
				$elementDefs = $this->xpathBase->query($queryType);
				foreach ($elementDefs as $elementDef) {
					$colorMap[] = !empty($elementDef->getAttribute('value')) ? $elementDef->getAttribute('value') : '' ;
				}
				
				$valueJson = !empty($colorMap) ? json_encode(array('values' => $colorMap)) : '';
			}
			
			if($type == ''){
				$queryType = $query."[@name='{$name}']/xs:simpleType/xs:restriction/xs:enumeration";
				$elementDefs = $this->xpath->query($queryType);
				foreach ($elementDefs as $elementDef) {
					$value[] = !empty($elementDef->getAttribute('value')) ? $elementDef->getAttribute('value') : '' ;
				}
				
				$valueJson = !empty($value) ? json_encode(array('values' => $value)) : '';
			}
			
			
			if(!empty($type)){
				
				$translateInfo = translate($this->db, $name, $this->store_id);
				$translate = isset($translateInfo['translate']) && !empty($translateInfo['translate']) ? $translateInfo['translate'] : '' ;
				$description = isset($translateInfo['description']) && !empty($translateInfo['description']) ? $translateInfo['description'] : '' ;
				
				$this->attributes[] =  array('local' => $entry->localName, 'name' => $name, 'type' => $type, 'minOccurs' => $minOccurs, 'maxOccurs' => $maxOccurs, 'values' => $valueJson, 'translate' => $translate, 'description' => $description);
			}
		}
		return $this->attributes;
	
	}
	
	public function ListAttributesRequired(){
		
		if(!isset($this->xpath)){
			return array();
		}
		$this->attributes = array();
		if(isset($this->alias)){
	
			switch($this->alias){
					
				case 'casa':
					$query = "/xs:schema/xs:element[@name='Home']/xs:complexType/xs:sequence/xs:element";
					$entries = $this->xpath->query($query);
					foreach ($entries as $entry) {
						$value = array();
						$name = !empty($entry->getAttribute('name')) ? $entry->getAttribute('name') : $entry->getAttribute('ref') ;
						$type = !empty($entry->getAttribute('type')) ? $entry->getAttribute('type') : '' ;
						$minOccurs = !empty($entry->getAttribute('minOccurs')) ? $entry->getAttribute('minOccurs') : 0 ;
						$maxOccurs = !empty($entry->getAttribute('maxOccurs')) ? $entry->getAttribute('maxOccurs') : '' ;
						if($type == ''){
							$queryType = $query."[@name='{$name}']/xs:simpleType/xs:restriction/xs:enumeration";
							$elementDefs = $this->xpath->query($queryType);
							foreach ($elementDefs as $elementDef) {
								$value[] = !empty($elementDef->getAttribute('value')) ? $elementDef->getAttribute('value') : '' ;
							}
						}
						
						$valueJson = !empty($value) ? json_encode(array('values' => $value)) : '';
// 						if(!empty($type)){
						
							$translateInfo = translate($this->db, $name, $this->store_id);
							$translate = isset($translateInfo['translate']) && !empty($translateInfo['translate']) ? $translateInfo['translate'] : '' ;
							$description = isset($translateInfo['description']) && !empty($translateInfo['description']) ? $translateInfo['description'] : '' ;
							$this->attributes[] =  array('local' => $entry->localName, 'name' => $name, 'type' => $type, 'minOccurs' => $minOccurs, 'maxOccurs' => $maxOccurs, 'values' => $valueJson, 'translate' => $translate, 'description' => $description);
// 						}else{
// 							$this->attributes[] =  array('name' => $name, 'type' => $type, 'minOccurs' => $minOccurs, 'maxOccurs' => $maxOccurs, 'values' => $valueJson);
// 						}
					}
					
				break;
				
			}
			
	
		}
		
		return $this->attributes;
		
	}
	
	public function ListAttributesRequiredOld()
	{
		
		if(!isset($this->xpath)){
			return array();
		}
		$this->attributes = array();
		
		if($this->type == 'complexType'){
			
			if(!isset($this->choice)){
				
				switch($this->set_attribute){
					
					
					case 'ClassificationData':
						$query = "/xs:schema/xs:element/xs:complexType/xs:sequence/xs:element[@name='{$this->set_attribute}']/xs:complexType/xs:sequence/xs:element";
						break;
					default:
						$query = "/xs:schema/xs:element/xs:complexType/xs:sequence/xs:element[@name='{$this->set_attribute}']/xs:complexType/xs:choice/xs:element";
						break;
				}
				
			}else{
				
				switch($this->set_attribute){
					
					case 'ProductType':
						$query = "/xs:schema/xs:complexType[@name='{$this->choice}']/xs:sequence/xs:element";
						break;
					
					
					case 'ClassificationData':
						$query = "/xs:schema/xs:element/xs:complexType/xs:sequence/xs:element[@name='{$this->set_attribute}']/xs:complexType/xs:sequence/xs:element";
						break;
					default:
						$query = "/xs:schema/xs:element[@name='{$this->choice}']/xs:complexType/xs:sequence/xs:element";
						break;
				}
				
			}
			$entries = $this->xpath->query($query);
// 			pre($entries);die;
			foreach ($entries as $entry) {
				$value = array();
				$name = !empty($entry->getAttribute('name')) ? $entry->getAttribute('name') : $entry->getAttribute('ref') ;
				$type = !empty($entry->getAttribute('type')) ? $entry->getAttribute('type') : '' ;
				$minOccurs = !empty($entry->getAttribute('minOccurs')) ? $entry->getAttribute('minOccurs') : 0 ;
				$maxOccurs = !empty($entry->getAttribute('maxOccurs')) ? $entry->getAttribute('maxOccurs') : '' ;
				if($type == ''){
					$queryType = $query."[@name='{$name}']/xs:simpleType/xs:restriction/xs:enumeration";
					$elementDefs = $this->xpath->query($queryType);
					foreach ($elementDefs as $elementDef) {
						$value[] = !empty($elementDef->getAttribute('value')) ? $elementDef->getAttribute('value') : '' ;
					}
				}
				$valueJson = !empty($value) ? json_encode(array('values' => $value)) : '';
				$this->attributes[] =  array('name' => $name, 'type' => $type, 'minOccurs' => $minOccurs, 'maxOccurs' => $maxOccurs, 'values' => $valueJson);
			}
		}
		
		if($this->type == 'simpleType'){
			
			switch($this->set_attribute){
				
				case 'ClothingType':
					if($this->xsdName == 'Shoes'){
						$query = "/xs:schema/xs:element/xs:complexType/xs:sequence/xs:element[@name='ClassificationData']/xs:complexType/xs:sequence/xs:element";
					}
					break;
				case 'ProductClothing':
					$query = "/xs:schema/xs:element/xs:complexType/xs:sequence/xs:element/xs:complexType/xs:sequence/xs:element[@name='{$this->set_attribute}']/xs:simpleType/xs:restriction/xs:enumeration";
					break;
				default:
					$query = "/xs:schema/xs:element/xs:complexType/xs:sequence/xs:element[@name='{$this->set_attribute}']/xs:simpleType/xs:restriction/xs:enumeration";
					break;
			}
		
			$entries = $this->xpath->query($query);
			foreach ($entries as $entry) {
				$value = array();
				$name = !empty($entry->getAttribute('name')) ? $entry->getAttribute('name') : $entry->getAttribute('value') ;
				$type = !empty($entry->getAttribute('type')) ? $entry->getAttribute('type') : '' ;
				$minOccurs = !empty($entry->getAttribute('minOccurs')) ? $entry->getAttribute('minOccurs') : 0 ;
				$maxOccurs = !empty($entry->getAttribute('maxOccurs')) ? $entry->getAttribute('maxOccurs') : '' ;
				if($type == ''){
					$queryType = $query."[@name='{$name}']/xs:simpleType/xs:restriction/xs:enumeration";
					$elementDefs = $this->xpath->query($queryType);
					foreach ($elementDefs as $elementDef) {
						$value[] = !empty($elementDef->getAttribute('value')) ? $elementDef->getAttribute('value') : '' ;
					}
				}
				$valueJson = !empty($value) ? json_encode(array('values' => $value)) : '';
				$this->attributes[] =  array('name' => $name, 'type' => $type, 'minOccurs' => $minOccurs, 'maxOccurs' => $maxOccurs, 'values' => $valueJson);
			}
		}
		return $this->attributes;
	}
	
	public function getXsdFromCategoryRelationship(){
		
		if(empty($this->category)){
			return array();
		}
		
		$sql = "SELECT * FROM az_category_relationship WHERE store_id = {$this->store_id} AND category LIKE '{$this->category}'";
		$query = $this->db->query($sql);
		return $query->fetch(PDO::FETCH_ASSOC);
		
	}
	
	
	
	public function GetAzAttributesRelationship(){
		
		if(empty($this->xsdName)){
			return array();
		}
		
		$sql = "SELECT * FROM az_attributes_relationship WHERE store_id = {$this->store_id} AND xsdName LIKE '{$this->xsdName}' AND choice LIKE '{$this->choice}'";
		$query = $this->db->query($sql);
		return $query->fetchAll(PDO::FETCH_ASSOC);
		
		
		
		
	}
	
	
	
	
	public function simpleTypeValXsd($elementDef) {
	
		$name = !empty($elementDef->getAttribute('name')) ? $elementDef->getAttribute('name') : $elementDef->getAttribute('ref') ;
		$type = !empty($elementDef->getAttribute('type')) ? $elementDef->getAttribute('type') : $elementDef->getAttribute('value') ;
	
		$query = "/xs:schema/xs:simpleType[@name='{$name}']/xs:restriction/xs:enumeration";
		// 			$query = "descendant-or-self::*[@is='{$name}']";
		$entries = $this->xpath->query($query);
		$value = array();
		foreach ($entries as $entry) {
	
			if(!empty($entry->getAttribute('value'))){
				$value[] = $entry->getAttribute('value');
			}
	
		}
		if(!empty($value)){
			$this->simpleTypeValXsd[$name] = $value;
		}
	
	}
	
	public function xsdSimpleType()
	{
	
	
		$elementDefs = $this->xpath->evaluate("/xs:schema/xs:simpleType");
		foreach($elementDefs as $elementDef) {
			$this->simpleTypeValXsd( $elementDef );
		}
	
	
		return $this->simpleTypeValXsd;
	
	}
	
	
	
	
}

?>