<?php if ( ! defined('ABSPATH')) exit; ?>
<div class='row'>
	<div class="col-md-12">
		<div class='box box-primary'>
			<div class='box-header'>
		       	<h3 class='box-title'>Informações de produtos</h3>
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
                    		<th>created_at</th>
                            <th>updated_at</th>
                            <th>short_description</th>
                            <th>weight</th>
                            <th>status</th>
                            <th>price</th>
                    		
                    	</tr>
                    	</thead>
                    	<tbody>
                    
                    
                    <?php 
                    
                    if(isset($productInfo)){
      
                        echo "<tr>
                                    <td>{$productInfo->product_id}</td>
                                    <td>{$productInfo->sku}</td>
                                    <td>{$productInfo->name}</td>
                                    <td>{$productInfo->set}</td>
                                    <td>{$productInfo->type}</td>
                                    <td>{$productInfo->created_at}</td>
                                    <td>{$productInfo->updated_at}</td>
                                    <td>{$productInfo->short_description}</td>
                                    <td>{$productInfo->weight}</td>
                                    <td>{$productInfo->status}</td>
                                    <td>{$productInfo->price}</td>
                            </tr>";
                        
                    }
                    

                    ?>
                    </tbody>
                    </table>
                    <?php 
                    pre($productInfo);
                    ?>
                    </div>
			</div>
		</div>
	</div>
</div>