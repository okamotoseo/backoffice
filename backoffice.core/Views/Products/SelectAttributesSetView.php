<?php if ( ! defined('ABSPATH')) exit;?>

<div class='row'>	
	<div class="col-md-12">
		<div class='box'>
			<div class='box-header'>
		       	<h3 class='box-title'>Selecione o conjunto de atributos</h3>
			</div><!-- /.box-header -->
			
			
              <!-- Block buttons -->
                <div class="box-body table-responsive">
                <div class="col-md-12">
                <?php
	                $key = array_search('Product', $parametros);
	                if(!empty($key)){
		                $value = get_next($parametros, $key);
	                }
	                $productId = isset($value) && $value != "" ? "/Product/{$value}" : "" ;
	                $goups = array();
	                foreach ($list as $fetch){
	                    $goups[$fetch['root_category']][] = $fetch;
	                    
	                }
	                foreach ($goups as $group => $value){
	                    echo "<h4 class='page-header'>{$group}<h4>";
	                    foreach($value as $key => $fetch){
	                       echo "<a href='".HOME_URI."/Products/RegisterProduct/SetAttribute/{$fetch['id']}{$productId}' class='btn btn-app'>{$fetch['set_attribute']}</a>";
	                    }
	                }
                ?>
                </div>
			</div
		</div>
	</div>
</div>
