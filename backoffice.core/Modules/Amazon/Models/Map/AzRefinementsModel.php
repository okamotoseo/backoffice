<?php 

class AzRefinementsModel extends MainModel
{
    
    public $id;

	public $tree_id;
	
	public $category_id;
	
	public $name;
	
	public $refinements;
	
	public $refinementsData;
	
	public $attribute;

	
	
	public function __construct($db = false,  $controller = null, $storeId = null)
	{
		$this->db = $db;
		 
		$this->store_id = $storeId;
		 
		$this->controller = $controller;
		 
		if(isset($this->controller)){
			 
			$this->parametros = $this->controller->parametros;
			 
			$this->userdata = $this->controller->userdata;
			 
			$this->store_id = $this->controller->userdata['store_id'];
		    
		}
	}
	
	
	public function GetCategoryRefinements(){
		
		if(empty($this->tree_id)){
			return array();
		}
		$this->refinamentData = array();
		$sql = "SELECT * FROM `az_refinement` WHERE tree_id = {$this->tree_id}";
		$query = $this->db->query($sql);
		$resRefinement = $query->fetchAll(PDO::FETCH_ASSOC);
		foreach($resRefinement as $key => $refinement){
			
			$translateInfo = translate($this->db, $refinement['attribute'], $this->store_id);
			$translate = isset($translateInfo['translate']) && !empty($translateInfo['translate']) ? $translateInfo['translate'] : '' ;
			$exemple = isset($translateInfo['exemple']) && !empty($translateInfo['exemple']) ? $translateInfo['exemple'] : '' ;
			$description = isset($translateInfo['description']) && !empty($translateInfo['description']) ? $translateInfo['description'] : '' ;
			
				
			$value = array();
			$sqlVal = "SELECT * FROM `az_refinement_values` WHERE attribute LIKE '{$refinement['attribute']}' GROUP BY value";
			$queryVal = $this->db->query($sqlVal);
			$resVal =  $queryVal->fetchAll(PDO::FETCH_ASSOC);
			foreach($resVal as $i => $val){
				$value[] = $val['value'];
			}
			$valueJson = !empty($value) ? json_encode(array('values' => $value)) : '';
			$this->refinamentData[] =  array('local' => 'refinement', 
					'name' => $translateInfo['word'],
					'type' => '', 
					'minOccurs' => 0, 
					'maxOccurs' => '', 
					'values' => $valueJson, 
					'translate' => $translateInfo['translate'],
					'exemple' => $translateInfo['exemple'],
					'alias' => $translateInfo['alias'],
					'required' => $translateInfo['required'],
					'description' => $description);
			
		}
		
		return  $this->refinamentData;
		
	}
	
	public function GetCategoryRefinementsValues(){
		
		if(empty($this->tree_id)){
			return array();
		}
		
		$sql = "SELECT * FROM `az_refinement_values` WHERE tree_id = {$this->tree_id}";
		$query = $this->db->query($sql);
		return $query->fetchAll(PDO::FETCH_ASSOC);
	
	}
	
	
	
	
}

?>