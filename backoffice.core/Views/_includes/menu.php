<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      <!-- Sidebar user panel -->
      <div class="user-panel">
        <div class="pull-left image">
        <?php 
                $file = UP_ABSPATH . "/images/store/160x160/{$this->userdata['store_id']}.png";
        	if(@getimagesize($file)){
        	    $src =  HOME_URI . "/Views/_uploads/images/store/160x160/{$this->userdata['store_id']}.png";
           	    
        	}else{
        	    $file = UP_ABSPATH . "/images/store/160x160/{$this->userdata['store_id']}.jpg";
        	    if(@getimagesize($file)){
        	        $src = HOME_URI . "/Views/_uploads/images/store/160x160/{$this->userdata['store_id']}.jpg";
        	    }else{
        	       $src = HOME_URI . "/Views/_uploads/images/store/160x160/default.jpg";
        	    }
        	}
        
        ?>
          <img src="<?php  echo $src; ?>" class="img-circle" alt="Store logo">
        </div>
        <div class="pull-left info">
            <form action="" id='store_session' method="POST" >
	          	<select class='form-control input-sm select_store_session' name='store_session' style='margin-bottom:3px;'>
	            <?php

    	            foreach ($this->userdata['stores'] as $storeId => $store){
    	            	$selected = $this->userdata['store_id'] == $storeId ? "selected" : "";
    	            	echo "<option value='{$storeId}' {$selected}>{$store}</option>";
    	            }
		         ?>
		          </select>
		         <i class="fa fa-circle text-success"></i> Ativa
	         </form>
        </div>
      </div>
      
      <!-- search form -->
          <form action="/Products/AvailableProducts/" method="POST" name='filter-product' class="sidebar-form">
            <div class="input-group">
              <input type="text" name="sku" class="form-control" placeholder="SKU...">
              <span class="input-group-btn">
                <button type="submit" name="available-products-filter" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i></button>
              </span>
            </div>
          </form>
          <!-- /.search form -->
      <!-- sidebar menu: : style can be found in sidebar.less -->
      <ul class="sidebar-menu">
        <li class="header">BACKOFFICE</li>
        <li class="<?php echo $this->menu['Dashboard'];?>" >
        	<a href="<?php echo HOME_URI;?>/Home/Dashboard"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a>
        </li>
        <li class="<?php echo $this->menu['Customer'];?> treeview" >
        	<a href="#"><i class="fa fa-users"></i> <span>Clientes</span></a>
        	<ul class="treeview-menu">
        		<li class="<?php echo $this->menu['ManageCustomers'];?>"><a href="<?php echo HOME_URI;?>/Customers/ManageCustomers"><i class="fa fa-circle-o"></i> <span>Gerenciar Clientes</span></a></li>
        		<li class="<?php echo $this->menu['RegisterCustomers'];?>"><a href="<?php echo HOME_URI;?>/Customers/RegisterCustomers"><i class="fa fa-circle-o"></i> <span>Cadastrar Cliente</span></a></li>
        	</ul>
        </li>

        <li class="<?php echo $this->menu['Orders']; ?> treeview">
        	<a href="#"><i class="fa  fa-cube"></i> <span>Pedidos</span> <i class="fa fa-angle-left pull-right"></i></a>
        	<ul class="treeview-menu">
            	<li class="<?php echo $this->menu['Manage']; ?>"><a href="/Orders/Manage/"><i class="fa fa-circle-o"></i> Gerenciar Pedidos</a></li>
            	<li class="<?php echo $this->menu['RegisterOrder']; ?>"><a href="/Orders/RegisterOrder/"><i class="fa fa-circle-o"></i> Televendas</a></li>
            	<li class="<?php echo $this->menu['Picking']; ?>"><a href="/Shipping/Picking/"><i class="fa fa-circle-o"></i> Separação</a></li>
            	<li class="<?php echo $this->menu['Packing']; ?>"><a href="/Shipping/Picking/"><i class="fa fa-circle-o"></i> Pacote</a></li>
            	<li class="<?php echo $this->menu['Shipping']; ?>"><a href="/Shipping/Send/"><i class="fa fa-circle-o"></i> Expedição</a></li>
            	<li class="<?php echo $this->menu['Returns']; ?>"><a href="/Orders/Returns"><i class="fa fa-circle-o"></i> Trocas e Devoluções</a></li>
        	</ul>
        </li>
		<li class="<?php echo $this->menu['Sac'];?> treeview">
            <a href="#"><i class="fa fa-comments"></i> <span>SAC</span><i class="fa fa-angle-left pull-right"></i></a>
            <ul class="treeview-menu">
            	<li class="<?php echo $this->menu['Questions'];?>"><a href="/Sac/Questions/" ><i class="fa fa-circle-o"></i> Perguntas<span class="label label-primary pull-right"></span></a></li>
        	</ul>
        </li>
        <li class="<?php echo $this->menu['Products']; ?> treeview">
        	<a href="#"><i class="fa  fa-cubes"></i> <span>Produtos</span> <i class="fa fa-angle-left pull-right"></i></a>
        	<ul class="treeview-menu">
            	<li class="<?php echo $this->menu['AvailableProducts']; ?>"><a href="<?php echo HOME_URI;?>/Products/AvailableProducts/"><i class="fa fa-circle-o"></i> Produtos Disponiveis</a></li>
            	<li class="<?php echo $this->menu['Product']; ?>"><a href="<?php echo HOME_URI;?>/Products/Product/"><i class="fa fa-circle-o"></i> Cadastrar Produto</a></li>
        		<li class="<?php echo $this->menu['Category']; ?>"><a href="<?php echo HOME_URI;?>/Products/Category/"><i class="fa fa-circle-o"></i> Categorias</a></li>
        		<li class="<?php echo $this->menu['Brands']; ?>"><a href="<?php echo HOME_URI;?>/Products/Brands/"><i class="fa fa-circle-o"></i> Marcas</a></li>
        		<li class="<?php echo $this->menu['Colors']; ?>"><a href="<?php echo HOME_URI;?>/Products/Colors/"><i class="fa fa-circle-o"></i> Cores</a></li>
        		<li class="<?php echo $this->menu['Attributes']; ?>"><a href="<?php echo HOME_URI;?>/Products/Attributes/"><i class="fa fa-circle-o"></i> Atributos</a></li>
        		<li class="<?php echo $this->menu['SetAttributes']; ?>"><a href="<?php echo HOME_URI;?>/Products/SetAttributes/"><i class="fa fa-circle-o"></i> Conjunto de Atributos</a></li>
        	</ul>
        </li>
        <li class="<?php echo $this->menu['Prices']; ?> treeview">
        	<a href="#"><i class="fa  fa-tags"></i> <span>Preços</span> <i class="fa fa-angle-left pull-right"></i></a>
        	<ul class="treeview-menu">
            	<li class="<?php echo $this->menu['Rules']; ?>"><a href="<?php echo HOME_URI;?>/Prices/Rules/"><i class="fa fa-circle-o"></i> Regras de Preços</a></li>
            	<li class="<?php echo $this->menu['PriceManager']; ?>"><a href="<?php echo HOME_URI;?>/Prices/PriceManager/"><i class="fa fa-circle-o"></i> Atualização de Preços</a></li>
        	</ul>
        </li>
        
        
        <li  class="<?php echo $this->menu['Report'];?> treeview" >
        	<a href="#"><i class="fa  fa-line-chart"></i> <span>Relatórios</span> <i class="fa fa-angle-left pull-right"></i></a>
        	<ul class="treeview-menu">
        			<li class="<?php echo $this->menu['Questions'];?>"><a href="/Report/Questions/"><i class="fa fa-file-text-o"></i> <span>Perguntas</span></a></li>
        			<li class="<?php echo $this->menu['Inventory'];?>"><a href="/Report/Inventory/"><i class="fa fa-file-text-o"></i> <span>Inventário</span></a></li>
        	       	<li class="<?php echo $this->menu['Sales'];?> treeview">
					<a href="#"><i class="fa fa-file-text-o"></i> Vendas <i class="fa fa-angle-left pull-right"></i></a>
                  	<ul class="treeview-menu">
                    	<li class="<?php echo $this->menu['ProductSales'];?>"><a href="/Report/ProductSales/"><i class="fa fa-circle-o"></i> Produtos</a></li>
                		<li class="<?php echo $this->menu['ReportSales'];?>"><a href="/Report/Sales/"><i class="fa fa-circle-o"></i> Pedidos</a></li>
                		<li class="<?php echo $this->menu['ReportReturns'];?>"><a href="/Report/Returns/"><i class="fa fa-circle-o"></i> Devoluções</a></li>
                		<li class="<?php echo $this->menu['BrandSales'];?>"><a href="/Report/BrandSales/"><i class="fa fa-circle-o"></i> Marcas</a></li>
                	</ul>
            	</li>
        	</ul>
        </li>
		<?php if($this->storedata['id'] == 3){?>
		
		 <li class="<?php echo $this->menu['Pluggto'];?> treeview">
        	<a href="#"><i class="fa  fa-code-fork"></i> <span>Plugg.To</span> <i class="fa fa-angle-left pull-right"></i></a>
        	<ul class="treeview-menu">
        		<li class="<?php echo $this->menu['ManageProducts'];?> treeview"><a href="/Modules/Pluggto/Products/ManageProducts/"><i class="fa fa-circle-o"></i> <span>Feed de Produtos</span></a></li>
        		<li class="<?php echo $this->menu['Categories']; ?>"><a href="/Modules/Pluggto/Products/Categories/"><i class="fa fa-circle-o"></i> <span>Mapear Categories</span></a></li>
        		<li class="<?php echo $this->menu['ExportProducts'];?> treeview"><a href="/Modules/Pluggto/Products/ExportProducts/"><i class="fa fa-circle-o"></i> <span>Exportar Produtos</span></a></li>
        		<li class="<?php echo $this->menu['UpdateProducts'];?> treeview"><a href="/Modules/Pluggto/Products/UpdateProducts/"><i class="fa fa-circle-o"></i> <span>Atualizar Produtos</span></a></li>
        	</ul> 
        </li>
		
		<?php }?>
       	<?php 
       	if( $developer){?>
       	<li class="<?php echo $this->menu['Marketplace']; ?> treeview">
        	<a href="#"><i class="fa  fa-map-marker"></i><span>Sysplace Marketplace</span> <i class="fa fa-angle-left pull-right"></i></a>
        	<ul class="treeview-menu">
        		<?php if($this->storedata['id'] != 7){?>
        			<li class="<?php echo $this->menu['Products'];?>"><a href="/Modules/Marketplace/Products/Products/"><i class="fa fa-circle-o"></i> <span>Produtos</span></a></li>
        		<?php }?>
        		<?php if($this->storedata['id'] == 7){?>
        			<li class="<?php echo $this->menu['ManageProducts'];?>"><a href="<?php echo HOME_URI;?>/Modules/Marketplace/Products/ManageProducts/"><i class="fa fa-circle-o"></i> <span>Gerenciar Produtos</span></a></li>		
        		<?php }?>
        	</ul>
        </li>
       	<?php }?>
        <?php if(in_array(8, $this->storedata['modules']) OR $developer){?>
        <li class="<?php echo $this->menu['Google'];?> treeview">
        	<a href="#"><i class="fa  fa-google"></i> <span>Google XML</span> <i class="fa fa-angle-left pull-right"></i></a>
        	<ul class="treeview-menu">
        		<li class="<?php echo $this->menu['ManageProducts'];?> treeview"><a href="<?php echo HOME_URI;?>/Modules/Google/Products/ManageProducts"><i class="fa fa-circle-o"></i> <span>Prdutos do XML</span></a></li>
        		<li class="<?php echo $this->menu['Setup'];?> treeview"><a href="<?php echo HOME_URI;?>/Modules/Configuration/Google/Setup/"><i class="fa fa-circle-o"></i> <span>Configurar XML</span></a></li>
        	</ul>
        </li>
		<?php } ?>
        <?php if(in_array(2, $this->storedata['modules']) OR $developer){?>
        <li class="<?php echo $this->menu['Mercadolivre']; ?> treeview">
        	<a href="#"><i class="fa  fa-legal"></i> <span>Mercadolivre</span> <i class="fa fa-angle-left pull-right"></i></a>
        	<ul class="treeview-menu">
        		<li class="<?php echo $this->menu['Adverts']; ?>"><a href="/Modules/Mercadolivre/Adverts/Adverts/"><i class="fa fa-circle-o"></i> Anúncios</a></li>
            	<li class="<?php echo $this->menu['Colors']; ?>"><a href="/Modules/Mercadolivre/Map/Colors/"><i class="fa fa-circle-o"></i> Mapear Cores</a></li>
				<li class="<?php echo $this->menu['Category']; ?>"><a href="/Modules/Mercadolivre/Map/Category/"><i class="fa fa-circle-o"></i> Mapear Categorias</a></li>
				<li class="<?php echo $this->menu['SalesMessages']; ?>"><a href="/Modules/Mercadolivre/Messages/SalesMessages/"><i class="fa fa-circle-o"></i> Mensagens Pós Venda</a></li> 
				<li class="<?php echo $this->menu['ImportAdverts']; ?>"><a href="/Modules/Mercadolivre/Adverts/ImportAdverts/"><i class="fa fa-circle-o"></i> Importar Anúncios</a></li>
				<li class="<?php echo $this->menu['AdvertsRelationship']; ?>"><a href="/Modules/Mercadolivre/Adverts/AdvertsRelationship/"><i class="fa fa-circle-o"></i> Relacionar Anúncios</a></li>
				<li class="<?php echo $this->menu['Log']; ?>"><a href="/Modules/Mercadolivre/Setup/Log/"><i class="fa fa-code"></i> Log</a></li>    	
        	</ul>
        </li>
        <?php } ?>
        
        <?php if(in_array(6, $this->storedata['modules']) OR $developer){ ?>
        <li class="<?php echo $this->menu['Amazon']; ?> treeview">
        	<a href="#"><i class="fa  fa-amazon"></i> <span>Amazon</span> <i class="fa fa-angle-left pull-right"></i></a>
        	<ul class="treeview-menu">
        	<li class="<?php echo $this->menu['SubmittedFeed'];?>"><a href="/Modules/Amazon/Feed/SubmittedFeed/"><i class="fa fa-circle-o"></i> <span>Feed</span></a></li>
        		<li class="<?php echo $this->menu['ProductsFeed'];?>"><a href="/Modules/Amazon/Products/ProductsFeed/"><i class="fa fa-circle-o"></i> <span>Produtos do Feed</span></a></li>
        		<li class="<?php echo $this->menu['Colors']; ?>"><a href="/Modules/Amazon/Map/Colors/"><i class="fa fa-circle-o"></i> Mapear Cores</a></li>
				<li class="<?php echo isset($this->menu['Category']) ? $this->menu['Category'] : $this->menu['Attributes'] ; ?>"><a href="/Modules/Amazon/Map/Category/"><i class="fa fa-circle-o"></i> Mapear Categorias</a></li>
				<li class="<?php echo $this->menu['Xsd']; ?>"><a href="/Modules/Amazon/Xsd/Xsd/"><i class="fa fa-circle-o"></i> XSD de Categorias</a></li>
				<li class="<?php echo $this->menu['Setup']; ?>"><a href="/Modules/Configuration/Amazon/Setup/"><i class="fa fa-circle-o"></i> Setup</a></li>
        	</ul>
        </li>
        <?php } ?>
        <?php if(in_array(9, $this->storedata['modules']) OR $developer){ ?>
        <li class="<?php echo $this->menu['Skyhub']; ?> treeview">
        	<a href="#"><i class="fa  fa-cloud"></i> <span>Skyhub</span> <i class="fa fa-angle-left pull-right"></i></a>
        	<ul class="treeview-menu">
        		<li class="<?php echo $this->menu['ManageProducts'];?> treeview">
        			<a href="<?php echo HOME_URI;?>/Modules/Skyhub/Products/ManageProducts/"><i class="fa fa-circle-o"></i> <span>Produtos</span></a>
        		</li>
        		<li class="<?php echo $this->menu['SkyhubOrders'];?> treeview">	
        			<a href="<?php echo HOME_URI;?>/Modules/Skyhub/Orders/SkyhubOrders/"><i class="fa fa-circle-o"></i> <span>Pedidos</span></a>
        		</li>
        		<li class="<?php echo empty($this->menu['Shipments']) ? $this->menu['PlpView'] : $this->menu['Shipments'] ; ?> treeview">	
        			<a href="<?php echo HOME_URI;?>/Modules/Skyhub/Shipments/Shipments/"><i class="fa fa-circle-o"></i> <span>Entregas</span></a>
        		</li>
        	</ul>
        </li>
        <?php  } ?>
        <?php if(in_array(16, $this->storedata['modules']) OR $developer){ ?>
        <li class="<?php echo $this->menu['Shopee'];?> treeview">
        	<a href="#"><i class="fa fa-ticket"></i> <span>Shopee</span> <i class="fa fa-angle-left pull-right"></i></a>
        	<ul class="treeview-menu">
        		<li class="<?php echo $this->menu['ManageProducts'];?> treeview"><a href="/Modules/Shopee/Products/ManageProducts/"><i class="fa fa-circle-o"></i> <span>Feed de Produtos</span></a></li>
        		<li class="<?php echo $this->menu['Categories']; ?>"><a href="/Modules/Shopee/Products/Categories/"><i class="fa fa-circle-o"></i> <span>Mapear Categories</span></a></li>
        		<li class="<?php echo $this->menu['ExportProducts'];?> treeview"><a href="/Modules/Shopee/Products/ExportProducts/"><i class="fa fa-circle-o"></i> <span>Exportar Produtos</span></a></li>
        		<li class="<?php echo $this->menu['UpdateProducts'];?> treeview"><a href="/Modules/Shopee/Products/UpdateProducts/"><i class="fa fa-circle-o"></i> <span>Atualizar Produtos</span></a></li>
        	</ul> 
        </li>
		<?php } ?>
        <?php if(in_array(10, $this->storedata['modules']) OR $developer){ ?>
        <li class="<?php echo $this->menu['Viavarejo']; ?> treeview">
        	<a href="#"><i class="fa  fa-viacoin"></i> <span>Viavarejo</span> <i class="fa fa-angle-left pull-right"></i></a>
        	<ul class="treeview-menu">
        		<li class="<?php echo $this->menu['ManageProducts'];?> treeview"><a href="/Modules/Viavarejo/Products/ManageProducts/"><i class="fa fa-circle-o"></i> <span>Produtos</span></a></li>
        		<li class="<?php echo $this->menu['Categories']; ?>  treeview"><a href="/Modules/Viavarejo/Products/Categories/"><i class="fa fa-circle-o"></i> <span>Categories</span></a></li>
        		<li class="<?php echo $this->menu['Setup']; ?>"><a href="/Modules/Configuration/Viavarejo/Setup/"><i class="fa fa-circle-o"></i> Setup</a></li>
        	</ul>
        </li>
        <?php  } 
         if($developer){ ?>
                <li class="<?php echo $this->menu['Plugnotas']; ?> treeview">
                	<a href="#"><i class="fa  fa-plug"></i> <span>PlugNotas</span> <i class="fa fa-angle-left pull-right"></i></a>
                	<ul class="treeview-menu">
                		<li class="<?php echo $this->menu['Setup']; ?>"><a href="/Modules/Configuration/Plugnotas/Setup/"><i class="fa fa-circle-o"></i> Setup</a></li>
                	</ul>
                </li>
        <?php  } 
		if( $developer){?>
        <li class="<?php echo $this->menu['Magento2'];?> treeview">
        	<a href="#"><i class="fab fa-magento"></i> <span> Magento2</span> <i class="fa fa-angle-left pull-right"></i></a>
        	<ul class="treeview-menu">
        			<li class="treeview">
                		<?php $moduleMg2 = getModuleConfig($this->db, $this->storedata['id'], 11);?>
            			<a href="<?php echo $moduleMg2['api_host']?>" target='_blank'><i class="fa fa-circle-o"></i> <span>Loja Online</span></a>
            		</li>
        			<li class="<?php echo $this->menu['ProductsTemp'];?> treeview"><a href="/Modules/Magento2/Products/ProductsTemp/"><i class="fa fa-circle-o"></i> <span>Produtos</span></a></li>
        			<li class="<?php echo $this->menu['Categories']; ?>"><a href="/Modules/Magento2/Products/Categories/"><i class="fa fa-circle-o"></i> <span>Categories</span></a></li>
        			<li class="<?php echo $this->menu['AttributesRelationship']; ?>"><a href="/Modules/Magento2/Products/AttributesRelationship/"><i class="fa fa-circle-o"></i> <span>Atributos</span></a></li>
        			<li class="<?php echo $this->menu['SetAttributes']; ?>"><a href="/Modules/Magento2/Products/SetAttributes/"><i class="fa fa-circle-o"></i> <span>Conjunto de Atributos</span></a></li>
        			<li class="<?php echo $this->menu['Setup']; ?>"><a href="/Modules/Configuration/Magento2/Setup/"><i class="fa fa-circle-o"></i> Setup</a></li>
        			<!-- <li class="<?php // echo $this->menu['Product']; ?>"><a href="<?php // echo HOME_URI;?>/Modules/Onbi/Catalog/Products/"><i class="fa fa-circle-o"></i> <span>Catalogo</span></a></li>  -->
        	</ul>
        </li>
		<?php } ?>
		<?php if(in_array(5, $this->storedata['modules']) OR $developer){?>
        <li class="<?php echo $this->menu['Onbi'];?> treeview">
        	<a href="#"><i class="fa  fa-shopping-cart"></i> <span>Magento</span> <i class="fa fa-angle-left pull-right"></i></a>
        	<ul class="treeview-menu">
        			<li class="<?php echo $this->menu['EcommerceOnbi'];?> treeview">
                		<?php $moduleOnbi = getModuleConfig($this->db, $this->storedata['id'], 5);?>
            			<a href="<?php echo $moduleOnbi['wsdl']?>" target='_blank'><i class="fa fa-circle-o"></i> <span>Loja Online</span></a>
            		</li>
        			<li class="<?php echo $this->menu['ProductsTemp'];?> treeview"><a href="/Modules/Onbi/Products/ProductsTemp/"><i class="fa fa-circle-o"></i> <span>Produtos</span></a></li>
        			<li class="<?php echo $this->menu['Categories']; ?>"><a href="/Modules/Onbi/Products/Categories/"><i class="fa fa-circle-o"></i> <span>Categories</span></a></li>
        			<li class="<?php echo $this->menu['AttributesRelationship']; ?>"><a href="/Modules/Onbi/Products/AttributesRelationship/"><i class="fa fa-circle-o"></i> <span>Atributos</span></a></li>
        			<li class="<?php echo $this->menu['SetAttributes']; ?>"><a href="/Modules/Onbi/Products/SetAttributes/"><i class="fa fa-circle-o"></i> <span>Conjunto de Atributos</span></a></li>
        			<!-- <li class="<?php // echo $this->menu['Product']; ?>"><a href="<?php // echo HOME_URI;?>/Modules/Onbi/Catalog/Products/"><i class="fa fa-circle-o"></i> <span>Catalogo</span></a></li>  -->
        	</ul>
        </li>
<!--         <li class="treeview"> -->
<!--         	<a href="#"><i class="fa  fa-gg"></i> <span>TOTVS ERP</span> <i class="fa fa-angle-left pull-right"></i></a> -->
<!--         </li> -->
		<?php } ?>

		<?php if(in_array(7, $this->storedata['modules']) OR $developer){?>
        <li class="<?php echo $this->menu['Tray'];?> treeview">
        	<a href="#"><i class="fa   fa-opencart"></i> <span>Tray Corp</span> <i class="fa fa-angle-left pull-right"></i></a>
        	<ul class="treeview-menu">
        		<li class="<?php echo $this->menu['ManageProducts'];?> treeview"><a href="/Modules/Tray/Products/ManageProducts"><i class="fa fa-circle-o"></i> <span>Produtos</span></a></li>
        		<li class="<?php echo $this->menu['Categories']; ?>"><a href="/Modules/Tray/Products/Categories/"><i class="fa fa-circle-o"></i> <span>Categories</span></a></li>
    			<li class="<?php echo $this->menu['EcommerceTray'];?> treeview">
            		<?php $moduleOnbi = getModuleConfig($this->db, $this->storedata['id'], 6);?>
        			<a href="<?php echo $moduleTray['site']?>" target='_blank'><i class="fa fa-circle-o"></i> <span>Loja Online</span></a>
        		</li>
        	</ul>
        </li>
		<?php } ?>
		
		<?php if(in_array(17, $this->storedata['modules']) OR $developer){?>
        <li class="<?php echo $this->menu['Vtex'];?> treeview">
        	<a href="#"><i class="fa   fa-opencart"></i> <span>Vtex</span> <i class="fa fa-angle-left pull-right"></i></a>
        	<ul class="treeview-menu">
        		<li class="<?php echo $this->menu['Category']; ?>"><a href="/Modules/Vtex/Products/Category/"><i class="fa fa-circle-o"></i> <span>Categories</span></a></li>
        	</ul>
        </li>
		<?php } ?>
		<?php if(in_array(10, $this->storedata['modules']) OR $developer){?>
        <li class="<?php echo $this->menu['Feed'];?> treeview">
        	<a href="#"><i class="fa  fa-feed"></i> <span>Feed de Produtos</span> <i class="fa fa-angle-left pull-right"></i></a>
        	<ul class="treeview-menu">
        		<li class="<?php echo $this->menu['Feed']; ?>"><a href="<?php echo HOME_URI;?>/Feed/ManageFeed"><i class="fa fa-circle-o"></i> Gerenciar</a></li>
        	</ul>
        </li>
		<?php } ?>
		<?php if(in_array(14, $this->storedata['modules']) OR $developer){?>
        <li class="<?php echo $this->menu['Mercadopago'];?> treeview">
        	<a href="/Modules/Configuration/Mercadopago/Setup/"><i class="fa  fa-shopping-cart"></i> <span>Mercadopago</span> <i class="fa fa-angle-left pull-right"></i></a>
        	<ul class="treeview-menu">
        		<li class="<?php echo $this->menu['Suscriptions']; ?>"><a href="/Modules/Mercadopago/Suscriptions/"><i class="fa fa-circle-o"></i> Assinaturas</a></li>
        		<li class="<?php echo $this->menu['Setup']; ?>"><a href="/Modules/Configuration/Mercadopago/Setup/"><i class="fa fa-circle-o"></i> Setup</a></li>
        	</ul>
        </li>
		<?php } ?>
		 <?php if(in_array(3, $this->storedata['modules']) OR $developer){?>
        <li class="<?php echo $this->menu['Adj'];?> treeview">
        	<a href="#"><i class="fa  fa-gg"></i> <span>Adj SIG</span> <i class="fa fa-angle-left pull-right"></i></a>
        	<ul class="treeview-menu">
        		<li class="<?php echo $this->menu['ProductsAdj'];?> treeview">
        			<a href="<?php echo HOME_URI;?>/Modules/Adj/Products/"><i class="fa fa-circle-o"></i> <span>Produtos</span></a>
        		</li>

        	</ul>
        </li>
		<?php } ?>
		<?php if(in_array(4, $this->storedata['modules']) OR $developer){?>
        <li class="<?php echo $this->menu['Sysemp'];?> treeview">
        	<a href="#"><i class="fa  fa-institution"></i> <span>Sysemp</span> <i class="fa fa-angle-left pull-right"></i></a>
        	<ul class="treeview-menu">
        		<li class="<?php echo $this->menu['ProductsAdj'];?> treeview">
        			<a href="<?php echo HOME_URI;?>/Modules/Sysemp/Products/"><i class="fa fa-circle-o"></i> <span>Produtos</span></a>
        		</li>

        	</ul>
        </li>
		<?php } ?>
        <?php if($developer){?>
        <li class="<?php echo $this->menu['Seta'];?> treeview">
        	<a href="#"><i class="fa  fa-location-arrow"></i> <span>Seta ERP</span> <i class="fa fa-angle-left pull-right"></i></a>
        	<ul class="treeview-menu">
        		<li class="<?php echo $this->menu['Dashboard'];?> treeview">
        			<a href="<?php echo HOME_URI;?>/Modules/Seta/Dashboard/"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a>
        		</li>
        		<li class="<?php echo $this->menu['Report'];?> treeview">
					<a href="#"><i class="fa fa-file-text-o"></i> Relatórios <i class="fa fa-angle-left pull-right"></i></a>
                  	<ul class="treeview-menu">
                    	<li class="<?php echo $this->menu['ReportProduct'];?>"><a href="<?php echo HOME_URI;?>/Modules/Seta/Report/ReportProduct/"><i class="fa fa-circle-o"></i> Produtos</a></li>
                	</ul>
            	</li>
        	</ul>
        </li>
		<?php } ?>
		<li  class="<?php echo $this->menu['Modules'];?> treeview" >
        	<a href="#"><i class="fa fa-th"></i> Módulos <i class="fa fa-angle-left pull-right"></i></a>
        	<ul class="treeview-menu">
				<li class="<?php echo $this->menu['Available'];?>"><a href="<?php echo HOME_URI;?>/Modules/Available/"><i class="fa fa-circle-o"></i> <span>Módulos</span></a>
            	<li class="<?php echo $this->menu['Documents'];?>"><a href="<?php echo HOME_URI;?>/Modules/Documents/"><i class="fa fa-circle-o"></i> Documentação</a></li>
        	</ul>
        </li>
        <li  class="<?php echo $this->menu['Configurations'];?>" >
        	<a href="<?php echo HOME_URI;?>/Configurations/Management"><i class="fa fa-sliders"></i> <span>Configurações</span></a>
        </li>
        <?php if($developer){?>
        
        <li  class="<?php echo $this->menu['Admin'];?> treeview" >
        	<a href="#"><i class="fa  fa-cog"></i> <span>Administração</span> <i class="fa fa-angle-left pull-right"></i></a>
        	<ul class="treeview-menu">
        		<li class='<?php echo $this->menu['Management']; ?>'><a href="/Admin/AccountManagement/"><i class="fa fa-circle-o"></i> Gerênciar Contas</a></li>
            	<li class='<?php echo $this->menu['Account']; ?>'><a href="/Admin/Account/"><i class="fa fa-circle-o"></i> Criar Conta</a></li>
        		<li class="<?php echo $this->menu['Print']; ?>"><a href="/Orders/Print"><i class="fa fa-circle-o"></i> Imprimir Pedidos</a></li>
        		<li class='<?php echo $this->menu['Documentations']; ?>'><a href="/Admin/Documentations/"><i class="fa fa-circle-o"></i> Gerênciar Documentação</a></li>
        		<li class='<?php echo $this->menu['Translates']; ?>'><a href="/Admin/Translates/"><i class="fa fa-circle-o"></i> Traduções</a></li>
        		<li class='<?php echo $this->menu['ManageCharge']; ?>'><a href="/Admin/ManageCharge/"><i class="fa fa-circle-o"></i> Gerenciar Cobranças</a></li>
        	</ul>
        </li>
        
            
        <li  class="<?php echo $this->menu['Developer'];?> treeview" >
        	<a href="#"><i class="fa  fa-linux"></i> <span>Desenvolvimento</span> <i class="fa fa-angle-left pull-right"></i></a>
        	<ul class="treeview-menu">
        		<li class="<?php echo $this->menu['Modules']; ?>"><a href="/Developer/Modules"><i class="fa fa-circle-o"></i> Cadastrar Modulo</a></li>
        		<li class="<?php echo $this->menu['Modules']; ?>"><a href="/Developer/infoPhp"><i class="fa fa-circle-o"></i> PHP</a></li>
            	<li><a href="<?php  echo HOME_URI . "/library/themes/AdminLTE-2.3.0"; ?>" target='_blank'><i class="fa fa-circle-o"></i> AdminLTE-2.3.0</a></li>
        		<li class="<?php echo $this->menu['NewProduct']; ?>"><a href="/Developer/Product/287025"><i class="fa fa-circle-o"></i> Cadastrar Produto</a></li>
        	</ul>
        </li>
        <?php } ?>
        
      </ul>
    </section>
    <!-- /.sidebar -->
  </aside>
<!-- Content Wrapper. Contains page content -->