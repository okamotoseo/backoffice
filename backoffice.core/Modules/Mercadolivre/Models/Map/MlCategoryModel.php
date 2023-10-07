<?php 

class MlCategoryModel  extends MainModel
{
    
    public $id;

	public $store_id;
	
	public $category;

	public $category_id;

	public $path_from_root;
	
	public $defaultCategories;
	
	public $hierarchy;


	
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
	
	public function CategoryRelationship(){
	    
	    $query = $this->db->query('SELECT * FROM `ml_category_relationship` WHERE store_id = ?', 
	        array($this->store_id)
	        );
	    
	    if ( ! $query ) {
	        return array();
	    }
	    return $query->fetchAll(PDO::FETCH_ASSOC);
	    
	    
	}
	
	public function CategoryRelationshipAttr()
	{
	    $query = $this->db->query('SELECT * FROM `ml_category_relationship` WHERE store_id = ?',
	        array($this->store_id)
	        );
	    
	    if ( ! $query ) {
	        return array();
	    }
	    $mlCategory = array();
	    while($row = $query->fetch(PDO::FETCH_ASSOC)){
	        
	       $sql = "SELECT count(distinct attribute_id) as totalAttr FROM ml_attributes_required
            WHERE store_id = '{$row['store_id']}' AND category_id = '{$row['category_id']}'";
	        $queryAttributes = $this->db->query($sql);
	         $total = $queryAttributes->fetch(PDO::FETCH_ASSOC);
	         $row['total_attributes'] = $total['totalAttr'];
	        
	        
	        $mlCategory[] = $row;
	    }
	    
	    
	    return $mlCategory;
	    
	}
	
	public function getCategoryRelationship()
	{
	    if ( !isset($this->hierarchy) ) {
	        
	        return array();
	    }
	    
	    $sql = "SELECT * FROM `ml_category_relationship` WHERE store_id = ? AND `category` LIKE ? LIMIT 1";
	    
	    $query = $this->db->query($sql, array($this->store_id, trim($this->hierarchy)));
	    
	    $res = $query->fetch(PDO::FETCH_ASSOC);
	    
	    return  $res;
	    
	}
	
	public function defaultCategoriesML()
	{
	    
// 	    $this->defaultCategories = '
//             [
//               {
//                 "id": "MLB5672",
//                 "name": "Acessórios para Veículos"
//               },
//               {
//                 "id": "MLB1499",
//                 "name": "Agro, Indústria e Comércio"
//               },
//               {
//                 "id": "MLB1403",
//                 "name": "Alimentos e Bebidas"
//               },
//               {
//                 "id": "MLB1071",
//                 "name": "Animais"
//               },
//               {
//                 "id": "MLB1367",
//                 "name": "Antiguidades"
//               },
//               {
//                 "id": "MLB1368",
//                 "name": "Arte e Artesanato"
//               },
//               {
//                 "id": "MLB1384",
//                 "name": "Bebês"
//               },
//               {
//                 "id": "MLB1246",
//                 "name": "Beleza e Cuidado Pessoal"
//               },
//               {
//                 "id": "MLB1132",
//                 "name": "Brinquedos e Hobbies"
//               },
//               {
//                 "id": "MLB1430",
//                 "name": "Calçados, Roupas e Bolsas"
//               },
//               {
//                 "id": "MLB1039",
//                 "name": "Câmeras e Acessórios"
//               },
//               {
//                 "id": "MLB1743",
//                 "name": "Carros, Motos e Outros"
//               },
//               {
//                 "id": "MLB1574",
//                 "name": "Casa, Móveis e Decoração"
//               },
//               {
//                 "id": "MLB1051",
//                 "name": "Celulares e Telefones"
//               },
//               {
//                 "id": "MLB1798",
//                 "name": "Coleções e Comics"
//               },
//               {
//                 "id": "MLB5726",
//                 "name": "Eletrodomésticos"
//               },
//               {
//                 "id": "MLB1000",
//                 "name": "Eletrônicos, Áudio e Vídeo"
//               },
//               {
//                 "id": "MLB1276",
//                 "name": "Esportes e Fitness"
//               },
//               {
//                 "id": "MLB263532",
//                 "name": "Ferramentas e Construção"
//               },
//               {
//                 "id": "MLB3281",
//                 "name": "Filmes e Seriados"
//               },
//               {
//                 "id": "MLB1144",
//                 "name": "Games"
//               },
//               {
//                 "id": "MLB1459",
//                 "name": "Imóveis"
//               },
//               {
//                 "id": "MLB1648",
//                 "name": "Informática"
//               },
//               {
//                 "id": "MLB218519",
//                 "name": "Ingressos"
//               },
//               {
//                 "id": "MLB1182",
//                 "name": "Instrumentos Musicais"
//               },
//               {
//                 "id": "MLB3937",
//                 "name": "Joias e Relógios"
//               },
//               {
//                 "id": "MLB1196",
//                 "name": "Livros"
//               },
//               {
//                 "id": "MLB1168",
//                 "name": "Música"
//               },
//               {
//                 "id": "MLB264586",
//                 "name": "Saúde"
//               },
//               {
//                 "id": "MLB1540",
//                 "name": "Serviços"
//               },
//               {
//                 "id": "MLB1953",
//                 "name": "Mais Categorias"
//               }
//             ]
//         ';
	    $this->defaultCategories = '
	    [
    	    {
    	        "id": "MLB5672",
    	        "name": "Acessórios para Veículos"
    	    },
    	    {
    	        "id": "MLB271599",
    	        "name": "Agro"
    	    },
    	    {
    	        "id": "MLB1403",
    	        "name": "Alimentos e Bebidas"
    	    },
    	    {
    	        "id": "MLB1071",
    	        "name": "Animais"
    	    },
    	    {
    	        "id": "MLB1367",
    	        "name": "Antiguidades e Coleções"
    	    },
    	    {
    	        "id": "MLB1368",
    	        "name": "Arte, Papelaria e Armarinho"
    	    },
    	    {
    	        "id": "MLB1384",
    	        "name": "Bebês"
    	    },
    	    {
    	        "id": "MLB1246",
    	        "name": "Beleza e Cuidado Pessoal"
    	    },
    	    {
    	        "id": "MLB1132",
    	        "name": "Brinquedos e Hobbies"
    	    },
    	    {
    	        "id": "MLB1430",
    	        "name": "Calçados, Roupas e Bolsas"
    	    },
    	    {
    	        "id": "MLB1039",
    	        "name": "Câmeras e Acessórios"
    	    },
    	    {
    	        "id": "MLB1743",
    	        "name": "Carros, Motos e Outros"
    	    },
    	    {
    	        "id": "MLB1574",
    	        "name": "Casa, Móveis e Decoração"
    	    },
    	    {
    	        "id": "MLB1051",
    	        "name": "Celulares e Telefones"
    	    },
    	    {
    	        "id": "MLB1500",
    	        "name": "Construção"
    	    },
    	    {
    	        "id": "MLB5726",
    	        "name": "Eletrodomésticos"
    	    },
    	    {
    	        "id": "MLB1000",
    	        "name": "Eletrônicos, Áudio e Vídeo"
    	    },
    	    {
    	        "id": "MLB1276",
    	        "name": "Esportes e Fitness"
    	    },
    	    {
    	        "id": "MLB263532",
    	        "name": "Ferramentas"
    	    },
    	    {
    	        "id": "MLB12404",
    	        "name": "Festas e Lembrancinhas"
    	    },
    	    {
    	        "id": "MLB1144",
    	        "name": "Games"
    	    },
    	    {
    	        "id": "MLB1459",
    	        "name": "Imóveis"
    	    },
    	    {
    	        "id": "MLB1499",
    	        "name": "Indústria e Comércio"
    	    },
    	    {
    	        "id": "MLB1648",
    	        "name": "Informática"
    	    },
    	    {
    	        "id": "MLB218519",
    	        "name": "Ingressos"
    	    },
    	    {
    	        "id": "MLB1182",
    	        "name": "Instrumentos Musicais"
    	    },
    	    {
    	        "id": "MLB3937",
    	        "name": "Joias e Relógios"
    	    },
    	    {
    	        "id": "MLB1196",
    	        "name": "Livros, Revistas e Comics"
    	    },
    	    {
    	        "id": "MLB1168",
    	        "name": "Música, Filmes e Seriados"
    	    },
    	    {
    	        "id": "MLB264586",
    	        "name": "Saúde"
    	    },
    	    {
    	        "id": "MLB1540",
    	        "name": "Serviços"
    	    },
    	    {
    	        "id": "MLB1953",
    	        "name": "Mais Categorias"
    	    }
	    ]';
	    return json_decode($this->defaultCategories);
	    
	    
	    
	}
	
}

?>