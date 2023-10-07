<?php

class AttributesRelationshipController extends MainController
{

    
    public function __construct($db = false, $controller = null)
    {
    	
        $this->db = $db;
        
        $this->parametros = $controller->parametros;
        
        $this->userdata = $controller->userdata;
        
        $this->moduledata = getModuleConfig($this->db, $this->userdata['store_id'], 11);
        
        
    }

    public function AttributesRelationship() 
	{
	    
	    $this->title = 'Attributos: <b>'.$this->userdata['stores'][$this->userdata['store_id']]."</b>"; 
	        
	    $this->includes = array("js" => "/Modules/Magento2/Views/js/ModuleMagento2.js");
	    
	    $attributesModel = parent::load_module_model('Magento2/Models/Products/AttributesRelationshipModel');
	    
	    
	    if ( in_array('SetId', $this->parametros )) {
	    
	    	$key = array_search('SetId', $this->parametros);
	    
	    	$setAttrSetId = get_next($this->parametros, $key);
	    	$setAttributeId  = is_numeric($setAttrSetId) ? $setAttrSetId :  null;
	  
	    	if(isset($setAttributeId)){
	    		
	    		$setAttributesModel = parent::load_model('Products/SetAttributesModel');
	    		$setAttributesModel->id = $setAttributeId;
	    		$productAttributes = $setAttributesModel->GetSetAttributesLikeAttribute();
	    	}
	    	
	    }else{
// 	    	$productAttributesModel = parent::load_model('Products/AttributesModel');
// 	    	$productAttributes = $productAttributesModel->ListAttributes();
	    }
	    
	    $attributes = $attributesModel->ListAttributesMg2();
	    if ( in_array('AttributeSetId', $this->parametros )) {
	    	 
	    	$key = array_search('AttributeSetId', $this->parametros);
	    	 
	    	$attrSetId = get_next($this->parametros, $key);
	    	$attributeSetId  = is_numeric($attrSetId) ? $attrSetId :  null;
	    	
	    	$attributesModel->attribute_set_id = $attributeSetId;
	    	$setAttrRel = $attributesModel->ListSetAttributeRelationshipMg2();
	    }
	    
	    $attrDefault = array();
	    $attrDefault['default']['sku'] = 'SKU - Produto';
	    $attrDefault['default']['parent_id'] = 'Parent Id - Produto';
	    $attrDefault['default']['title'] = 'Título - Produto';
	    $attrDefault['default']['color'] = 'Cor - Variação';
	    $attrDefault['default']['voltagem'] = 'Voltagem - Variação';
	    $attrDefault['default']['tamanho'] = 'Tamanho - Variação';
	    $attrDefault['default']['volume'] = 'Volume - Variação';
	    $attrDefault['default']['unidade'] = 'Unidade - Variação';
	    $attrDefault['default']['brand'] = 'Marca - Produto';
	    $attrDefault['default']['reference'] = 'Referencia - Produto';
	    $attrDefault['default']['collection'] = 'Coleção - Produto';
	    $attrDefault['default']['weight'] = 'Peso da embalagem - Produto';
	    $attrDefault['default']['height'] = 'Altura da embalagem - Produto';
	    $attrDefault['default']['width'] = 'Largura da embalagem - Produto';
	    $attrDefault['default']['length'] = 'Comprimento da embalagem - Produto';
	    $attrDefault['default']['ean'] = 'EAN - Produto';
	    $attrDefault['default']['description'] = 'Descrição - Produto';
	    $attrDefault['default']['price'] = 'Preço - Produto';
	    $attrDefault['default']['sale_price'] = 'Preço de Venda - Produto';
	    $attrDefault['default']['promotion_price'] = 'Preço Promocional - Produto';
	    $attrDefault['default']['cost'] = 'Custo - Produto';
	    $attrDefault['default']['ean'] = 'EAN - Codigo de Barras - Produto';
	    
	    
	    
	    require ABSPATH . "/Modules/Magento2/Views/Products/AttributesRelationshipView.php";
        
        
	}

}
?>