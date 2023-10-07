<?php
if(isset($list)){
?>

    <div class="row">
    	<div class="col-xs-12">
    	<div class="box <?php echo $this->box['table']['model']; ?>">
    	<div class="box-header">
    		<h3 class="box-title"><?php echo $this->title; ?></h3>
    		<div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa <?php echo $this->box['table']['icon']; ?>"></i></button>
            </div>
    	</div>
    	
    	<div class="box-body table-responsive">
    		<table class="table table-hover display" cellspacing="0" width="100%" role="grid" aria-describedby="example_info" style="width: 100%;">
    			<thead>
    				<tr>
    					<th>Empresa</th>
    					<th>Quantidade</th>
    					<th>A Vista</th>
    					<th>A Prazo</th>
    					<th>Total</th>
    					<th>Frete</th>
    					<th>Custo</th>
    				</tr>
    			</thead>
    			<tbody>
    
    <?php
//     pre($list);
        foreach($list as $key => $rowSales){
            
    	
//         	if($rowProducts['quantidade'] > 0){
        	
            	echo "<tr data-toggle='collapse' data-target='#{$key}' class='clickable' >
                    <th>".trim($rowSales['loja'])."</th>
            		<th>".trim($rowSales['quantidade'])."</th>
            		<th>".trim(number_format($rowSales['avista'], 2, ',', '.'))."</th>
            		<th>".trim(number_format($rowSales['aprazo'], 2, ',', '.'))."</th>
            		<th>".trim(number_format($rowSales['total'], 2, ',', '.'))."</th>
            		<th>".trim(number_format($rowSales['frete'], 2, ',', '.'))."</th>
            		<th>".trim(number_format($rowSales['custo'], 2, ',', '.'))."</th>
            	</tr>";
            	
            	echo "<tr>
                    <td colspan='7'>
                        <div id='{$key}' class='collapse'>";
            	           echo "<table class='table table-hover display'>";
            	           foreach($rowSales['vendas'] as $i => $sale){
            	                echo "<tr>
                                    <td>".trim($sale['vendedor'])."</td>                        
                                    <td>".trim($sale['quantidade'])."</td>
                    		        <td>".trim(number_format($sale['avista'], 2, ',', '.'))."</td>
                    		        <td>".trim(number_format($sale['aprazo'], 2, ',', '.'))."</td>
                    		        <td>".trim(number_format($sale['total'], 2, ',', '.'))."</td>
                    		        <td>".trim(number_format($sale['frete'], 2, ',', '.'))."</td>
                    		        <td>".trim(number_format($sale['custo'], 2, ',', '.'))."</td>
                                </tr>";   
            	           }
                           echo "</table>";
                        echo "</div>
                    </td>
                </tr>";
//         	}
        }
    	
    ?>
    			</tbody>

    		</table>
    	</div>
    	</div>
    	</div>
    </div>
<?php 
}
?>