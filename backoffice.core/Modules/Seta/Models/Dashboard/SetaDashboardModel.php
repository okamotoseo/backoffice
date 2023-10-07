<?php 

class SetaDashboardModel extends MainModel
{

	public $total_orders = "23";
    public $total_revenues = "1700,56";
    public $total_clients = 180;
    public $total_products = 2500;


	public function __construct($db = false, $controller = null)
	{
	    $this->db = $db;
	    
	    $this->controller = $controller;
	    
	    $this->parametros = $this->controller->parametros;
	    
	    $this->userdata = $this->controller->userdata;
	    
	    $this->pg = new PgConnection($this->db, $this->userdata['store_id']);
	    
	    
	}
	
	
	public function SalesResume(){
	    $sql = "SELECT * FROM vvendas";
	    $req = $this->pg->query($sql);
	    foreach($req->fetchAll(PDO::FETCH_ASSOC) as $storeSale) {
	        
	        $list[] = $storeSale;
	        
	    }
	    
	    return $list;
	    
	}
	public function StoreSales() {
	    

	    $sql = "Select Cast(SubStr(Empresas.Codigo,5,2)||' - '||Empresas.Apelido as Char(80))  As ReportGroup, Sum(Case When Movimento.Movimento = 'S'::Char Then Movimento.Quantidade Else - Movimento.Quantidade End)::Numeric(6) As Quantidade,
        Sum(Movimento.Total*(Vendas.AVista/Vendas.SubTotal))::Numeric(10,2) as AVista,
        Sum(Movimento.Total*(Vendas.APrazo/Vendas.SubTotal))::Numeric(10,2) as APrazo,
        Sum(Movimento.Total*(Vendas.Total/Vendas.SubTotal))::Numeric(10,2) as Total,
        Sum(Movimento.Frete) As Frete,
        Sum(Movimento.Quantidade*Movimento.Custo) As Custo
                From Movimento
                Inner Join Vendas on Substr(Movimento.Auxiliar,3,8)::Char(8)=Vendas.Codigo
                Inner Join Produtos on Substr(Movimento.Produto,1,6)::Char(6)=Produtos.Codigo
                Inner Join Pessoas Empresas on LPad(Vendas.Empresa,6,'0')::Char(6)=Empresas.Codigo
                Inner Join Pessoas Clientes on Vendas.Cliente=Clientes.Codigo
                Inner Join Pessoas Vendedores on Vendas.Vendedor=Vendedores.Codigo
                Inner Join Condicoes on Vendas.Condicoes=Condicoes.Codigo
                Left Join Marcas on Produtos.Marca=Marcas.Codigo
                Left Join Pessoas Fornecedores on Produtos.Fornecedor=Fornecedores.Codigo
                Left Join Pessoas Avalista on Vendas.Avalista=Avalista.Codigo
                Left Join Departamentos on Produtos.Departamento=Departamentos.Codigo
                Left Join Grupos on Produtos.Grupo=Grupos.Codigo
                Left Join SubGrupos on Produtos.SubGrupo=SubGrupos.Codigo
                Left Join Grades on Produtos.Grade=Grades.Codigo
                Left Join Colecoes on Produtos.Colecao=Colecoes.Codigo
        Where Case When Vendas.Tipo='03' Then Vendas.Status='P' Else Vendas.Status='S' End And
              Case When Vendas.Tipo='03' Then (Movimento.Operacao='VC') Else (Movimento.Operacao='VE' Or Movimento.Operacao='DV') End And Movimento.Estoque=True 
                And  Vendas.Data Between '04/11/2018' And '04/12/2018' And Vendas.Tipo='01'
        Group By ReportGroup Order By ReportGroup";
	    
	    $req = $this->pg->query($sql);
	    foreach($req->fetchAll(PDO::FETCH_ASSOC) as $storeSale) {
	        
	        $list[] = $storeSale;
	        
	    }
	    
	    return $list;
	    
	}
	
	public function Sales() {
	    
	    
	    $sql = "Select Cast(SubStr(Empresas.Codigo,5,2)||' - '||Empresas.Apelido as Char(80))  As ReportGroup,
        Vendedores.Apelido As Vendedor, Movimento.Empresa,
        Sum(Case When Movimento.Movimento = 'S'::Char Then Movimento.Quantidade Else - Movimento.Quantidade End)::Numeric(6) As Quantidade,
    	Sum(Movimento.Total*(Vendas.AVista/Vendas.SubTotal))::Numeric(10,2) as AVista,
    	Sum(Movimento.Total*(Vendas.APrazo/Vendas.SubTotal))::Numeric(10,2) as APrazo,
    	Sum(Movimento.Total*(Vendas.Total/Vendas.SubTotal))::Numeric(10,2) as Total,
    	Sum(Movimento.Frete) As Frete,
    	Sum(Movimento.Quantidade*Movimento.Custo) As Custo
    	From Movimento
    	Inner Join Vendas on Substr(Movimento.Auxiliar,3,8)::Char(8)=Vendas.Codigo
    	Inner Join Produtos on Substr(Movimento.Produto,1,6)::Char(6)=Produtos.Codigo
    	Inner Join Pessoas Empresas on LPad(Vendas.Empresa,6,'0')::Char(6)=Empresas.Codigo
    	Inner Join Pessoas Clientes on Vendas.Cliente=Clientes.Codigo
    	Inner Join Pessoas Vendedores on Vendas.Vendedor=Vendedores.Codigo
    	Inner Join Condicoes on Vendas.Condicoes=Condicoes.Codigo
    	Left Join Marcas on Produtos.Marca=Marcas.Codigo
    	Left Join Pessoas Fornecedores on Produtos.Fornecedor=Fornecedores.Codigo
    	Left Join Pessoas Avalista on Vendas.Avalista=Avalista.Codigo
    	Left Join Departamentos on Produtos.Departamento=Departamentos.Codigo
    	Left Join Grupos on Produtos.Grupo=Grupos.Codigo
    	Left Join SubGrupos on Produtos.SubGrupo=SubGrupos.Codigo
    	Left Join Grades on Produtos.Grade=Grades.Codigo
    	Left Join Colecoes on Produtos.Colecao=Colecoes.Codigo
    	Where Case When Vendas.Tipo='03' Then Vendas.Status='P' Else Vendas.Status='S' End And
    	Case When Vendas.Tipo='03' Then (Movimento.Operacao='VC') Else (Movimento.Operacao='VE' Or Movimento.Operacao='DV') End 
        And Movimento.Estoque=True And  Vendas.Data Between '04/11/2018' And '04/12/2018' And Vendas.Tipo='01'
    	Group By Vendedores.Apelido, ReportGroup, Movimento.Empresa Order By ReportGroup";
	
	    $req = $this->pg->query($sql);
	    foreach($req->fetchAll(PDO::FETCH_ASSOC) as $storeSale) {
	       
	        if(isset($storeSale['reportgroup'])){
	           $list[$storeSale['empresa']]['loja'] =  $storeSale['reportgroup'];
	           unset($storeSale['reportgroup']);
	        }
	        
	        $list[$storeSale['empresa']]['quantidade'] = empty($list[$storeSale['empresa']]['quantidade']) ? $storeSale['quantidade'] : $list[$storeSale['empresa']]['quantidade'] + $storeSale['quantidade'];
	        $list[$storeSale['empresa']]['avista'] = empty($list[$storeSale['empresa']]['avista']) ? $storeSale['avista'] : $list[$storeSale['empresa']]['avista'] + $storeSale['avista'];
	        $list[$storeSale['empresa']]['aprazo'] = empty($list[$storeSale['empresa']]['aprazo']) ? $storeSale['aprazo'] : $list[$storeSale['empresa']]['aprazo'] + $storeSale['aprazo'];
	        $list[$storeSale['empresa']]['total'] = empty($list[$storeSale['empresa']]['total']) ? $storeSale['total'] : $list[$storeSale['empresa']]['total'] + $storeSale['total'];
	        $list[$storeSale['empresa']]['frete'] = empty($list[$storeSale['empresa']]['frete']) ? $storeSale['frete'] : $list[$storeSale['empresa']]['frete'] + $storeSale['frete'];
	        $list[$storeSale['empresa']]['custo'] = empty($list[$storeSale['empresa']]['custo']) ? $storeSale['custo'] : $list[$storeSale['empresa']]['custo'] + $storeSale['custo'];
	        $list[$storeSale['empresa']]['vendas'][] = $storeSale;
	        
// 	        $list[$storeSale['empresa']]['quantidade'] += $storeSale['quantidade'];
// 	        $list[$storeSale['empresa']]['avista'] += $storeSale['avista'];
// 	        $list[$storeSale['empresa']]['aprazo'] += $storeSale['aprazo'];
// 	        $list[$storeSale['empresa']]['total'] += $storeSale['total'];
// 	        $list[$storeSale['empresa']]['frete'] += $storeSale['frete'];
// 	        $list[$storeSale['empresa']]['custo'] += $storeSale['custo'];
	        
// 	        $list[] = $storeSale;
	        
	    }
	    
	    return $list;
	    
	}
	
}

?>