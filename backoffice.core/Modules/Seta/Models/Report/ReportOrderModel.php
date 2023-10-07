<?php 


class ReportOrderModel extends MainModel
{
    
    private $tableAlias = "Pedidos";
    
    public $report_model = "";
    
    public $date_add;
    
    public $date_add_end;
    
    private $date_add_condition;
    
    public $date_last;
    
    public $date_last_end;
    
    private $date_last_condition;
    
    public $description;
    
    private $description_condition;
    
    public $reference;
    
    private $reference_condition;
    
    public $color;
    
    private $color_condition;
    
    public $status;
    
    private $status_condition;
    
    public $stock_position;
    
    public $turning_status;
    
    public $brand;
    
    public $brand_id;
    
    private $brand_condition;
    
    public $provider;
    
    public $provider_id;
    
    private $provider_condition;
    
    public $department;
    
    public $department_id;
    
    private $department_condition;
    
    public $grid;
    
    public $grid_id;
    
    private $grid_condition;
    
    public $collection;
    
    public $collection_id;
    
    private $collection_condition;
    
    public $company;
    
    public $company_id;
    
    private $company_condition;
    
    public $group;
    
    public $group_id;
    
    private $group_condition;
    
    public $subgroup;
    
    public $subgroup_id;
    
    private $subgroup_condition;
    
    public $report;
    
    private $condition = "";
    
    private $group_by = ", ReportGroup";
    
    private $order_by = "ReportGroup, ";
    
    private $report_group = " 0 As ReportGroup, ";
    
    
    public function __construct($db = false, $controller = null)
    {
        $this->db = $db;
        
        $this->controller = $controller;
        
        $this->parametros = $this->controller->parametros;
        
        $this->userdata = $this->controller->userdata;
        
        $this->pg = new PgConnection($this->db,  $this->userdata['store_id']);
        
        $this->date_add_end = date("Y-m-d");
        
        $this->date_last_end = date("Y-m-d");
        
    }
    
    public function ValidateForm() {
        
        if ( 'POST' == $_SERVER['REQUEST_METHOD'] && ! empty ( $_POST ) ) {
            
            foreach ( $_POST as $property => $value ) {
                if(property_exists($this,$property) AND !empty($value)){
                    $this->{$property} = $value;
                }
                
            }
            
            if( !empty($this->report) ){
                
//              $this->report_checked = array( "{$this->report}" => "checked");
                $this->getReportGroup();
                
            }
            
            $this->date_order 	= 	empty($this->date_order) ? '0001-01-01' : $this->date_order ;
            $this->date_order_end	= 	empty($this->date_order_end) ? date("Y-m-d") : $this->date_order_end ;

            if(empty($this->brand_id) AND !empty($this->brand)){
                $arr = $this->getFilterId("brand", $this->brand, 1);
                $this->brand_id = $arr[0]['id'];
            }

            if(empty($this->provider_id) AND !empty($this->provider)){
                $arr = $this->getFilterId("provider", $this->provider, 1);
                $this->provider_id = $arr[0]['id'];
            }

            if(empty($this->department_id) AND !empty($this->department)){
                $arr = $this->getFilterId("department", $this->department, 1);
                $this->department_id = $arr[0]['id'];
            }

            if(empty($this->grid_id) AND !empty($this->grid)){
                $arr = $this->getFilterId("grid", $this->grid, 1);
                $this->grid_id = $arr[0]['id'];
            }
   
            if(empty($this->collection_id) AND !empty($this->collection)){
                $arr = $this->getFilterId("collection", $this->collection, 1);
                $this->collection_id = $arr[0]['id'];
            }

            if(empty($this->company_id) AND !empty($this->company)){
                $arr = $this->getFilterId("company", $this->company, 1);
                $this->company_id = $arr[0]['id'];
            }
 
            if(empty($this->group_id) AND !empty($this->group)){
                $arr = $this->getFilterId("group", $this->group, 1);
                $this->group_id = $arr[0]['id'];
            }
            
            if(empty($this->subgroup_id) AND !empty($this->subgroup)){
                $arr = $this->getFilterId("subgroup", $this->subgroup, 1);
                $this->subgroup_id = $arr[0]['id'];
            }
            
        }else{
            
            return;
            
        }
    }
    
    public function ReceivablesOrder() {
        $this->getFilterProdutos();
        $list = [];
        
         $sql = " Select Produtos.Codigo As Produto, Produtos.Preco, Produtos.Preco1, Produtos.Preco2, Produtos.Descricao,Produtos.CorX, Produtos.Referencia, Produtos.Grade, 
                Pedidos.Codigo as pcodigo, Pedidos.Data, Pedidos.Previsao, Pedidos_Detalhes.Unitario,Fornecedores.Nome,
               Grades.Legenda01, Grades.Legenda02, Grades.Legenda03, Grades.Legenda04, Grades.Legenda05, Grades.Legenda06, Grades.Legenda07, Grades.Legenda08,
               Grades.Legenda09, Grades.Legenda10, Grades.Legenda11, Grades.Legenda12, Grades.Legenda13, Grades.Legenda14, Grades.Legenda15,
               Sum(Pedidos_Detalhes.P01) As P01, Sum(Pedidos_Detalhes.P02) As P02, Sum(Pedidos_Detalhes.P03) As P03, Sum(Pedidos_Detalhes.P04) As P04, Sum(Pedidos_Detalhes.P05) As P05, Sum(Pedidos_Detalhes.P06) As P06,
               Sum(Pedidos_Detalhes.P07) As P07, Sum(Pedidos_Detalhes.P08) As P08, Sum(Pedidos_Detalhes.P09) As P09, Sum(Pedidos_Detalhes.P10) As P10, Sum(Pedidos_Detalhes.P11) As P11, Sum(Pedidos_Detalhes.P12) As P12,
               Sum(Pedidos_Detalhes.P13) As P13, Sum(Pedidos_Detalhes.P14) As P14, Sum(Pedidos_Detalhes.P15) As P15, Sum(Pedidos_Detalhes.PQuantidade) As PQuantidade
        From Pedidos
        Inner Join Pedidos_Detalhes On Pedidos.Codigo = Pedidos_Detalhes.Pedido
        Inner Join Produtos On Pedidos_Detalhes.Produto = Produtos.Codigo
        Inner Join Marcas On Produtos.Marca = Marcas.Codigo
        Inner Join Grades On Produtos.Grade =  Grades.Codigo
        Inner Join Pessoas As Fornecedores On Pedidos.Fornecedor = Fornecedores.Codigo
        Inner Join Departamentos On Produtos.Departamento = Departamentos.Codigo
        Inner Join Grupos On Produtos.Grupo = Grupos.Codigo
        Left Join SubGrupos on Produtos.SubGrupo=SubGrupos.Codigo
        Left Join Colecoes On Produtos.Colecao = Colecoes.Codigo
        Inner Join Pessoas As Empresas On ('0000'||Pedidos.local)::char(6) = Empresas.Codigo
        Left Join Linhas on Produtos.Linha=Linhas.Codigo
        Where Pedidos.Status = 'A' And {$this->condition}
        GROUP BY
               Produtos.Codigo, Produtos.Descricao, Pedidos_Detalhes.Produto, Pedidos.Codigo, Fornecedores.Nome,
               Produtos.CorX, Produtos.Referencia, Produtos.Grade, Pedidos.Data, Pedidos.Previsao, Pedidos_Detalhes.Unitario,
               Grades.Legenda01, Grades.Legenda02, Grades.Legenda03, Grades.Legenda04, Grades.Legenda05, Grades.Legenda06, Grades.Legenda07, Grades.Legenda08,
               Grades.Legenda09, Grades.Legenda10, Grades.Legenda11, Grades.Legenda12, Grades.Legenda13, Grades.Legenda14, Grades.Legenda15,
               Pedidos_Detalhes.P01, Pedidos_Detalhes.P02, Pedidos_Detalhes.P03, Pedidos_Detalhes.P04, Pedidos_Detalhes.P05, Pedidos_Detalhes.P06, Pedidos_Detalhes.P07,
               Pedidos_Detalhes.P08, Pedidos_Detalhes.P09, Pedidos_Detalhes.P10, Pedidos_Detalhes.P11, Pedidos_Detalhes.P12, Pedidos_Detalhes.P13,
               Pedidos_Detalhes.P14, Pedidos_Detalhes.P15, Pedidos_Detalhes.PQuantidade";
        
        $req = $this->pg->query($sql);
        $list = $req->fetchAll(PDO::FETCH_ASSOC);
//                 pre($list);die;
        return $list;
    }
    
    public function Receivables() {
        $this->getFilterProdutos();
        $list = [];
        
       $sql = " Select Produtos.Codigo As Produto, Produtos.Preco, Produtos.Preco1, Produtos.Preco2, Produtos.Descricao,Produtos.CorX, Produtos.Referencia, Produtos.Grade,  Pedidos.Previsao, Pedidos_Detalhes.Unitario,
               Grades.Legenda01, Grades.Legenda02, Grades.Legenda03, Grades.Legenda04, Grades.Legenda05, Grades.Legenda06, Grades.Legenda07, Grades.Legenda08, 
               Grades.Legenda09, Grades.Legenda10, Grades.Legenda11, Grades.Legenda12, Grades.Legenda13, Grades.Legenda14, Grades.Legenda15, 
               Sum(Pedidos_Detalhes.P01) As P01, Sum(Pedidos_Detalhes.P02) As P02, Sum(Pedidos_Detalhes.P03) As P03, Sum(Pedidos_Detalhes.P04) As P04, Sum(Pedidos_Detalhes.P05) As P05, Sum(Pedidos_Detalhes.P06) As P06, 
               Sum(Pedidos_Detalhes.P07) As P07, Sum(Pedidos_Detalhes.P08) As P08, Sum(Pedidos_Detalhes.P09) As P09, Sum(Pedidos_Detalhes.P10) As P10, Sum(Pedidos_Detalhes.P11) As P11, Sum(Pedidos_Detalhes.P12) As P12, 
               Sum(Pedidos_Detalhes.P13) As P13, Sum(Pedidos_Detalhes.P14) As P14, Sum(Pedidos_Detalhes.P15) As P15, Sum(Pedidos_Detalhes.PQuantidade) As PQuantidade
        From Pedidos
        Inner Join Pedidos_Detalhes On Pedidos.Codigo = Pedidos_Detalhes.Pedido
        Inner Join Produtos On Pedidos_Detalhes.Produto = Produtos.Codigo
        Inner Join Marcas On Produtos.Marca = Marcas.Codigo
        Inner Join Grades On Produtos.Grade =  Grades.Codigo
        Inner Join Pessoas As Fornecedores On Pedidos.Fornecedor = Fornecedores.Codigo
        Inner Join Departamentos On Produtos.Departamento = Departamentos.Codigo
        Inner Join Grupos On Produtos.Grupo = Grupos.Codigo
        Left Join SubGrupos on Produtos.SubGrupo=SubGrupos.Codigo
        Left Join Colecoes On Produtos.Colecao = Colecoes.Codigo
        Inner Join Pessoas As Empresas On ('0000'||Pedidos.local)::char(6) = Empresas.Codigo
        Left Join Linhas on Produtos.Linha=Linhas.Codigo
        Where Pedidos.Status = 'A' and {$this->condition}
        GROUP BY 
               Produtos.Codigo, Produtos.Descricao, Pedidos_Detalhes.Produto, 
               Produtos.CorX, Produtos.Referencia, Produtos.Grade, Pedidos.Previsao, Pedidos_Detalhes.Unitario,
               Grades.Legenda01, Grades.Legenda02, Grades.Legenda03, Grades.Legenda04, Grades.Legenda05, Grades.Legenda06, Grades.Legenda07, Grades.Legenda08, 
               Grades.Legenda09, Grades.Legenda10, Grades.Legenda11, Grades.Legenda12, Grades.Legenda13, Grades.Legenda14, Grades.Legenda15, 
               Pedidos_Detalhes.P01, Pedidos_Detalhes.P02, Pedidos_Detalhes.P03, Pedidos_Detalhes.P04, Pedidos_Detalhes.P05, Pedidos_Detalhes.P06, Pedidos_Detalhes.P07, 
               Pedidos_Detalhes.P08, Pedidos_Detalhes.P09, Pedidos_Detalhes.P10, Pedidos_Detalhes.P11, Pedidos_Detalhes.P12, Pedidos_Detalhes.P13, 
               Pedidos_Detalhes.P14, Pedidos_Detalhes.P15, Pedidos_Detalhes.PQuantidade";
      
        $req = $this->pg->query($sql);
        $list = $req->fetchAll(PDO::FETCH_ASSOC);
//         pre($list);die;
        return $list;
    }
    
    
    public function getReportGroup(){
        
        switch($this->report){
            case "description":  $this->report_group = " VProdutos.Descricao As ReportGroup, "; break;
            case "reference":  $this->report_group = " Trim(vProdutos.Referencia) As ReportGroup, "; break;
            case "color":  $this->report_group = " VProdutos.CorX As ReportGroup, "; break;
            case "brand":  $this->report_group = " Marcas.Descricao As ReportGroup, "; break;
            case "provider":  $this->report_group = "  Fornecedores.Nome As ReportGroup, "; break;
            case "department":  $this->report_group = "  Departamentos.Descricao As ReportGroup, "; break;
            case "group":  $this->report_group = " Grupos.Descricao As ReportGroup, "; break;
            case "subgroup":  $this->report_group = " Coalesce(SubGrupos.Descricao,'') As ReportGroup, "; break;
            case "grid":  $this->report_group = " Grades.Descricao As ReportGroup, "; break;
            case "collection":  $this->report_group = " Coalesce(Colecoes.Descricao,'') As ReportGroup, "; break;
            case "company":  $this->report_group = " 0 As ReportGroup, "; break;
            case "date_add":  $this->report_group = " VProdutos.Cadastro As ReportGroup, "; break;
            case "date_last":  $this->report_group = " 0 As ReportGroup, "; break;
        }
    }
    
    public function getFilterId($type, $term, $limit){
        $term = strtoupper($term);
        switch ($type){
            case "brand":
                $sql = "SELECT codigo, descricao FROM marcas
				WHERE desativar = 'f' AND  descricao LIKE '{$term}%' ORDER BY descricao LIMIT {$limit}";
                break;
            case "provider":
                $sql = "SELECT codigo, nome as descricao FROM pessoas
				WHERE pessoa = 1 AND nome LIKE '{$term}%' ORDER BY descricao LIMIT {$limit}";
                break;
            case "department":
                $sql = "SELECT codigo, descricao FROM departamentos
				WHERE desativar = 'f' AND descricao LIKE '{$term}%' ORDER BY descricao LIMIT {$limit}";
                break;
            case "grid":
                $sql = "SELECT codigo, descricao FROM grades
				WHERE desativar = 'f' AND descricao LIKE '%{$term}%' ORDER BY descricao LIMIT {$limit}";
                break;
            case "collection":
                $sql = "SELECT codigo, descricao FROM colecoes
				WHERE desativar = 'f' AND descricao LIKE '%{$term}%' ORDER BY codigo DESC LIMIT {$limit}";
                break;
            case "company":
                $sql = "SELECT apelido as descricao, substr(codigo,5,2)::char(2) as codigo FROM pessoas
				WHERE filial AND apelido LIKE '%{$term}%' LIMIT {$limit} ORDER BY codigo ASC";
                break;
            case "group":
                $sql = "SELECT codigo, descricao FROM grupos
				WHERE desativar = 'f' AND descricao LIKE '{$term}%' ORDER BY descricao LIMIT {$limit}";
                break;
            case "subgroup":
                $sql = "SELECT codigo, descricao FROM subgrupos
				WHERE desativar = 'f' AND descricao LIKE '{$term}%' ORDER BY descricao LIMIT {$limit}";
                break;
        }
        
        if(!empty($sql)){
            
            $pgQuery = $this->pg->query($sql);
            while ($row = $pgQuery->fetch(PDO::FETCH_ASSOC)){
                $arr[] = array(
                    "id" => "{$row['codigo']}",
                    "label" => trim("{$row['descricao']}"),
                    "value" => trim("{$row['descricao']}")
                );
            }
            return $arr;
            
        }else{
            return false;
        }
        
    }
    
    public function getFilterProdutos(){
        foreach ($this as $key => $value){
            if(!empty($value)){
                switch ($key){
                    case "date_end":
                        $this->condition .= $this->date_end_condition = " AND {$this->tableAlias}.Cadastro BETWEEN '{$this->date}' AND '{$value}'";
                        break;
                        
                    case "brand_id":
                        $this->condition .= $this->brand_condition = " AND Produtos.Marca='{$this->brand_id}'";
                        break;
                        
                    case "provider":
                        $this->condition .= $this->provider_condition = " AND {$this->tableAlias}.Fornecedor='{$this->provider_id}'";
                        break;
                        
                    case "department":
                        $this->condition .= $this->department_condition = " AND {$this->tableAlias}.Departamento='{$this->department_id}'";
                        break;
                        
                    case "grid":
                        $this->condition .= $this->grid_condition = " AND {$this->tableAlias}.Grade='{$this->grid_id}'";
                        break;
                        
                    case "collection":
                        $this->condition .= $this->collection_condition = " AND {$this->tableAlias}.Colecao='{$this->collection_id}'";
                        break;
                        
                    case "company":
                        $this->condition .= $this->company_condition = " AND {$this->tableAlias}.Empresa='{$this->company_id}'";
                        break;
                        
                    case "group":
                        $this->condition .= $this->group_condition = " AND {$this->tableAlias}.Grupo='{$this->group_id}'";
                        break;
                        
                    case "subgroup":
                        $this->condition .= $this->subgroup_condition = " AND {$this->tableAlias}.Subgrupo='{$this->subgroup_id}'";
                        break;
                        
                    case "reference":
                        $this->condition .= $this->reference_condition = " AND Trim({$this->tableAlias}.Referencia) LIKE '{$value}%'";
                        break;
                        
                    case "description":
                        $this->condition .= $this->description_condition = " AND Trim(Produtos.Descricao) LIKE '{$value}%'";
                        break;
                        
                    case "color":
                        $this->condition .= $this->color_condition = " AND Trim({$this->tableAlias}.Corx) LIKE '{$value}%'";
                        break;
                        
                }
            }
        }
        
        $this->condition = substr($this->condition, 4);
        
        return $this->condition;
    }
    

    
    
} 