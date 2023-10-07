<?php if ( ! defined('ABSPATH')) exit; ?>
<div class='row'>
	<div class="col-md-12">
		<div class='box box-primary'>
			<div class='box-header'>
		       	<h3 class='box-title'>Listagem de produtos</h3>
			</div><!-- /.box-header -->
			
			<div class="box-body">
				<div class="col-md-12">
                    <table id="search-advanced" class="table table-bordered  table-hover  display" cellspacing="0" width="100%" role="grid" aria-describedby="example_info" style="width: 100%;">
                    	<thead>
                    
                    	<tr>
                    		<th>product_id</th>
                    		<th>sku</th>
                    		<th>name</th>
                    		<th>set</th>
                    		<th>type</th>
                    		<th>Ações</th>
                    		
                    	</tr>
                    	</thead>
                    	<tbody>
                    
                    
                    <?php 
                    
                    
                    if(isset($products)){
                        foreach($products as $key => $product){
                            echo "<tr>
                                        <td>{$product->product_id}</td>
                                        <td>{$product->sku}</td>
                                        <td>{$product->name}</td>
                                        <td>{$product->set}</td>
                                        <td>{$product->type}</td>
                                        <td><a href='".HOME_URI."/Modules/Onbi/Catalog/Informations/Product/{$product->product_id}' class=''><i class='fa fa-info'></i> Info.</a></td>
                                </tr>";
                            
                        }
                    }
                    
//                     echo "<tr>
//                                         <td>{$product->product_id}</td>
//                                         <td>{$product->sku}</td>
//                                         <td>{$product->name}</td>
//                                         <td>{$product->set}</td>
//                                         <td>{$product->type}</td>
//                                         <td>{$product->created_at}</td>
//                                         <td>{$product->updated_at}</td>
//                                         <td>{$product->name}</td>
//                                         <td>{$product->short_description}</td>
//                                         <td>{$product->weight}</td>
//                                         <td>{$product->status}</td>
//                                         <td>{$product->price}</td>
//                                 </tr>";
                    ?>
                    </tbody>
                    </table>
                    </div>
			</div>
		</div>
	</div>
</div>