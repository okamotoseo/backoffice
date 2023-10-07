
    	<div class="box <?php echo $this->box['table']['model']; ?>">
    	<div class="box-header">
    		<h3 class="box-title">Listagem de Preços e Estoques</h3>
    		<div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa <?php echo $this->box['table']['icon']; ?>"></i></button>
            </div>
    	</div>
    	
    	<div class="box-body table-responsive">
    		<table id='seta_report_product' class="table table-hover display" cellspacing="0" width="100%" role="grid" aria-describedby="example_info" style="width: 100%;">
    			<thead>
    				<tr>
    					<th>Agrupador</th>
    					<th>Pedido</th>
    					<th>Produto</th>
    					<th>Descrição</th>
    					<th>Cor</th>
    					<th>qtde</th>
    					<th>Pedido</th>
    					<th>Previsão</th>
    					<th>Preço.</th>
    				</tr>
    			</thead>
    			<tbody>
    
    <?php
        foreach($list as $key => $rowProducts){
        	
        	echo "<tr>
                <td>".trim($rowProducts['nome'])."</td>
                <td>".trim($rowProducts['pcodigo'])."</td>
        		<td>".trim($rowProducts['produto'])."</td>
        		<td>".trim($rowProducts['descricao'])."</td>
        		<td>".trim($rowProducts['corx'])."</td>
        		<td>".trim($rowProducts['pquantidade'])."</td>
                <td>".trim($rowProducts['data'])."</td>
        		<td>".trim($rowProducts['previsao'])."</td>
        		<td>".trim($rowProducts['preco2'])."</td>
        	</tr>";
        }
    	
    ?>
    			</tbody>

    		</table>
    	</div>
    	</div>
