<?php 

class MlColorsModel extends MainModel
{
    
    /**
     * @var string
     */
    public $id;

	/**
	 * @var string
	 * Class Unique ID
	 */
	public $store_id;
	

	/**
	 * @var string
	 */
	public $color;

	/**
	 * @var string
	 */
	public $information_1 = null;

	/**
	 * @var string
	 */
	public $information_2 = null;

	


	
	public function __construct($db = false, $controller = null)
	{
	    $this->db = $db;
	    
	    $this->controller = $controller;
	    
	    $this->parametros = $this->controller->parametros;
	    
	    $this->userdata = $this->controller->userdata;
	    
	    $this->store_id = $this->controller->userdata['store_id'];
	    
	    
	    
	}
	
	
	public function ListAllowedColors()
	{
	    // Simplesmente seleciona os dados na base de dados
	    $query = $this->db->query('SELECT * FROM `ml_allowed_colors` ORDER BY id DESC');
	    
	    // Verifica se a consulta está OK
	    if ( ! $query ) {
	        return array();
	    }
	    // Preenche a tabela com os dados do usuário
	    return $query->fetchAll(PDO::FETCH_ASSOC);
	    
	}
	
	
	public function ListMlAttributeColors()
	{
	    // Simplesmente seleciona os dados na base de dados
	    $query = $this->db->query("SELECT distinct value FROM `ml_attributes_required` 
        WHERE attribute_id LIKE 'COLOR' GROUP BY value ORDER BY value ASC");
	    
	    // Verifica se a consulta está OK
	    if ( ! $query ) {
	        return array();
	    }
	    // Preenche a tabela com os dados do usuário
	    return $query->fetchAll(PDO::FETCH_ASSOC);
	    
	}
	
	
	
	public function ColorsRelationship(){
	    
	    $query = $this->db->query('SELECT * FROM `ml_color_relationship` WHERE store_id = ?', 
	        array($this->store_id)
	        );
	    
	    if ( ! $query ) {
	        return array();
	    }
	    return $query->fetchAll(PDO::FETCH_ASSOC);
	    
	    
	}
	
}

?>