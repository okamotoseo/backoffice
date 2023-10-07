<div class="box <?php echo $this->box['table']['model']; ?>">
	<div class="box-header">
		<h3 class="box-title">Listagem de Preços e Estoques</h3>
		<div class="box-tools pull-right">
            <button class="btn btn-box-tool" data-widget="collapse"><i class="fa <?php echo $this->box['table']['icon']; ?>"></i></button>
        </div>
	</div>
	
	<div class="box-body table-responsive">
		<table id="seta_report_product" class="table table-hover display" cellspacing="0" width="100%" role="grid" aria-describedby="example_info" style="width: 100%;">
			<thead>
				<tr>
					<th>Agrupador</th>
					<th>Código</th>
					<th>Descrição</th>
					<th>Cor</th>
					<th>qtde</th>
					<th>Custo Aq.</th>
					<th>Preço.</th>
				</tr>
			</thead>
			<tbody>

<?php
        foreach($list as $key => $rowProducts){
            
    	
//         	if($rowProducts['quantidade'] > 0){
        	
            	echo "<tr>
                    <td>".trim($rowProducts['reportgroup'])."</td>
            		<td>".trim($rowProducts['codigo'])."</td>
            		<td>".trim($rowProducts['descricao'])."</td>
            		<td>".trim($rowProducts['corx'])."</td>
            		<td>".trim($rowProducts['quantidade'])."</td>
            		<td>".trim($rowProducts['custoaquisicao'])."</td>
            		<td>".trim($rowProducts['preco2'])."</td>
            	</tr>";
//         	}
        }
    	
    ?>
			</tbody>

		</table>
	</div>
</div>