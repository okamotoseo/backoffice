<?php 

class AzBaseXsdModel extends MainModel
{
    
    public $id;

	public $store_id;
	
	public $choice;
	
	public $type;
	
	public $set_attribute;
	
	public $xsdBase;
	
	public $xpathBase;
	
	public $simpleTypeVal = array();

	
	
	public function __construct($db = false,  $controller = null, $storeId = null)
	{
		$this->db = $db;
		 
		$this->store_id = $storeId;
		
		$this->xsdBase = "https://backoffice.sysplace.com.br/Modules/Amazon/Xsd/amzn-base.xsd";
		 
		$this->controller = $controller;
		 
		if(isset($this->controller)){
			 
			$this->parametros = $this->controller->parametros;
			 
			$this->userdata = $this->controller->userdata;
			 
			$this->store_id = $this->controller->userdata['store_id'];
			
			$this->LoadXsdBase();
		    
		}
	}
	
	public function ValidateForm() {
	    
	    if ( 'POST' == $_SERVER['REQUEST_METHOD'] && ! empty ( $_POST ) ) {
	        
	        foreach ( $_POST as $property => $value ) {
	            
	            if(property_exists($this,$property)){
	                
	                if( !empty( $value ) ){
	                    
	                    $this->{$property} = $value;
	                    
	                }else{
	                    $required = array();
	                    
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
	
	
	
	
	public function baseXsdComplexType()
	{
		
		
		function echoElements($xpathBase, $elementDef) {
			global $doc, $xpath;
			 
			$name = !empty($elementDef->getAttribute('name')) ? $elementDef->getAttribute('name') : $elementDef->getAttribute('ref') ;
			$type = !empty($elementDef->getAttribute('type')) ? $elementDef->getAttribute('type') : $elementDef->getAttribute('value') ;
			 
			$query = "/xs:schema/xs:complexType[@name='{$name}']/xs:simpleContent/xs:extension/xs:attribute";
// 			$query = "/xs:schema/xs:complexType[@name='{$name}']";
			$entries = $xpathBase->query($query);
			
			foreach ($entries as $entry) {
				pre($entry);
// 				echo $name." - ".$entry->getAttribute('use')." - ". $entry->getAttribute('name')." - ".$entry->getAttribute('type')."<br>";
				pre(array('element' => $name, 'use' => $entry->getAttribute('use'), "name" => $entry->getAttribute('name'), "type" => $entry->getAttribute('type')));
// 				pre($entry);
// 				pre($entry->nextSibling->nodeValue);
// 				pre($entry->childNodes->nodeValue);
// 				pre($entry->previousSibling->nodeValue);
				
			}
			 
		}
		
		
		
		$elementDefs = $this->xpathBase->evaluate("/xs:schema/xs:complexType");
		foreach($elementDefs as $elementDef) {
			echoElements($this->xpathBase, $elementDef);
		}
		
	}
	
	
	public function simpleTypeValBase($xpathBase, $elementDef) {
		
		$name = !empty($elementDef->getAttribute('name')) ? $elementDef->getAttribute('name') : $elementDef->getAttribute('ref') ;
		$type = !empty($elementDef->getAttribute('type')) ? $elementDef->getAttribute('type') : $elementDef->getAttribute('value') ;
	
		$query = "/xs:schema/xs:simpleType[@name='{$name}']/xs:restriction/xs:enumeration";
		// 			$query = "descendant-or-self::*[@is='{$name}']";
		$entries = $xpathBase->query($query);
		$value = array();
		foreach ($entries as $entry) {
	
// 				echo $name." ".$entry->getAttribute('value')."<br>";
// 				echo $entry->getAttribute('name')."<br>";
// 				echo $entry->getAttribute('type')."<br>";

// 				pre($entry);
			if(!empty($entry->getAttribute('value'))){
				$value[] = $entry->getAttribute('value');
			}	
				
		}
		if(!empty($value)){
			$this->simpleTypeVal[$name] = $value;
		}
	
	}
	
	public function baseXsdSimpleType()
	{
		
	
		$elementDefs = $this->xpathBase->evaluate("/xs:schema/xs:simpleType");
		foreach($elementDefs as $elementDef) {
			$this->simpleTypeValBase($this->xpathBase, $elementDef);
		}
		
		
		return $this->simpleTypeVal;
	
	}
		
		
		
		
	// 			foreach ($elementDef->evaluate("descendant-or-self::*[@is='{$name}']", $entries) as $node) {
	// 				var_dump($node->localName);
	// 			}
		
// 		$query = "/xs:schema/xs:complexType";
		
// 		$entries = $this->xpathBase->query($query);
		
// 		foreach ($entries as $entry) {
			
// 			$query = "/xs:schema/xs:complexType";
			
// 			$entries = $this->xpathBase->query($query);
			
// 			foreach ($entries as $entry) {
				
			
// 			}
			
// 			pre($entry);
// 			pre($entry->localName);

// 			$value = array();
// 			echo $entry->getRootElementName();
// 			echo "<br>";
// 			$name = !empty($entry->getAttribute('name')) ? $entry->getAttribute('name') : $entry->getAttribute('ref') ;
// 			$type = !empty($entry->getAttribute('type')) ? $entry->getAttribute('type') : '' ;
// 			$minOccurs = !empty($entry->getAttribute('minOccurs')) ? $entry->getAttribute('minOccurs') : 0 ;
// 			$maxOccurs = !empty($entry->getAttribute('maxOccurs')) ? $entry->getAttribute('maxOccurs') : '' ;
				
				
// 			pre($name." -> ".$type." - ".$xsdElement);
// 		}
		
	
	
	
	
// 	public function baseXsd()
// 	{
	
// 		$query = "/xs:schema";
	
// 		$entries = $this->xpathBase->query($query);
	
// 		foreach ($entries as $entry) {
				
// 			$value = array();
// 			$name = !empty($entry->getAttribute('name')) ? $entry->getAttribute('name') : $entry->getAttribute('ref') ;
// 			$type = !empty($entry->getAttribute('type')) ? $entry->getAttribute('type') : '' ;
// 			$minOccurs = !empty($entry->getAttribute('minOccurs')) ? $entry->getAttribute('minOccurs') : 0 ;
// 			$maxOccurs = !empty($entry->getAttribute('maxOccurs')) ? $entry->getAttribute('maxOccurs') : '' ;
	
// 			$valueJson = '';
	
// 			if($name == 'ColorMap'){
// 				$colorMap= array();
// 				$queryType = "/xs:schema/xs:element[@name='ColorMap']/xs:simpleType/xs:restriction/xs:enumeration";
// 				$elementDefs = $this->xpathBase->query($queryType);
// 				foreach ($elementDefs as $elementDef) {
// 					$colorMap[] = !empty($elementDef->getAttribute('value')) ? $elementDef->getAttribute('value') : '' ;
// 				}
					
// 				$valueJson = !empty($colorMap) ? json_encode(array('values' => $colorMap)) : '';
// 			}
	
// 			if($type == ''){
// 				$queryType = $query."[@name='{$name}']/xs:simpleType/xs:restriction/xs:enumeration";
// 				$elementDefs = $this->xpath->query($queryType);
// 				foreach ($elementDefs as $elementDef) {
// 					$value[] = !empty($elementDef->getAttribute('value')) ? $elementDef->getAttribute('value') : '' ;
// 				}
					
// 				$valueJson = !empty($value) ? json_encode(array('values' => $value)) : '';
// 			}
	
	
// 			if(!empty($type)){
					
// 				$translateInfo = translate($this->db, $name, $this->store_id);
// 				$translate = isset($translateInfo['translate']) && !empty($translateInfo['translate']) ? $translateInfo['translate'] : '' ;
// 				$description = isset($translateInfo['description']) && !empty($translateInfo['description']) ? $translateInfo['description'] : '' ;
					
// 				$this->attributes[] =  array('name' => $name, 'type' => $type, 'minOccurs' => $minOccurs, 'maxOccurs' => $maxOccurs, 'values' => $valueJson, 'translate' => $translate, 'description' => $description);
// 			}
				
// 		}
	
	
// 		return $this->attributes;
// 	}
	
	
	
	
}

?>