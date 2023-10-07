<?php if ( ! defined('ABSPATH')) exit; ?>
<div class="row">
	<!-- Default box -->
	<div class="col-md-12">
	
		<div class="message">
			<?php if(!empty( $productsModel->form_msg)){ echo $productsModel->form_msg;}?>
		</div>
		
		<div class="box box-primary">
			<form role="form" method="POST" action="<?php echo HOME_URI ?>/Modules/Adj/Products/" name="form-store">
			<div class="box-header with-border">
				<h3 class="box-title"><?php echo $this->title; ?></h3>
				<div class="box-tools pull-right">
					<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
				</div>
			</div>
			<div class="box-body">
				<div class="row">
					<div class="col-xs-6">		
						<div class="form-group">
							<label for="description">Descrição:</label> 
							<input type="text" name="description" id="description" class="form-control description" value="<?php echo $productsModel->description; ?>" />
						</div>
					</div>
					<div class="col-xs-6">		

						
					<!-- Date dd/mm/yyyy -->
                  <div class="form-group">
                    <label for="dataUpdate">Atualizado:</label>
                    <div class="input-group">
                      <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                      </div>
                      <?php 
                      $dateUpdate = isset($productsModel->dataUpdate) ? date( 'd-m-YY-m-d', strtotime($productsModel->dataUpdate)) : null;
                      ?>
                      <input type="text" name="dataUpdate" id="dataUpdate" class="form-control dataUpdate" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask value="<?php echo $dateUpdate ?>" >
                    </div><!-- /.input group -->
                  </div><!-- /.form group -->
					</div>
				</div>
			</div><!-- /.box-body -->
			<div class="box-footer">
				<button type="submit" class="btn btn-info pull-right" id="search" name="search">Buscar</button>
			</div><!-- /.box-footer-->
			</form>
		</div><!-- /.box -->
	</div>
</div>
<?php if(isset($products)){?>
<div class="row">
	<div class="col-xs-12">

    	<div class="box box-primary">
        	<div class="box-header">
        		<h3 class="box-title"><?php echo $this->title; ?></h3>
        	</div>
        	<div class="box-body table-responsive">
        		<div class="col-md-12">
        		<table  id="search-default" class="table table-bordered  table-hover display">
                <thead>
                <tr>
                         <th>id</th>
                         <th>produtoId</th>
                         <th>codigoEan13</th>
                         <th>descricao</th>
                         <th>qtdeEstoqueMinimo</th>
                         <th>dataUltimaCompra</th>
                         <th>dataUltimaVenda</th>
                         <th>vlrVenda</th>
                         <th>largura</th>
                         <th>espessura</th>
                         <th>comprimento</th>
                         <th>mt3</th>
                         <th>pesoBruto</th>
                         <th>pesoLiquido</th>
                         <th>origemProduto</th>
                         <th>stAtual</th>
                         <th>dataUpdate</th>
                         <th>totalizarQtdePedido</th>
                </tr>
                </thead>
         		<tbody>
                     <?php 
                     foreach ($products['body']['value'] as $key => $productsVal){ 
	                     echo "<tr>
							<td>{$productsVal['$id']}</td>
							<td>{$productsVal['produtoId']}</td>
							<td>{$productsVal['codigoEan13']}</td>
							<td>{$productsVal['descricao']}</td>
							<td>{$productsVal['qtdeEstoqueMinimo']}</td>
							<td>{$productsVal['dataUltimaCompra']}</td>
							<td>{$productsVal['dataUltimaVenda']}</td>
							<td>{$productsVal['vlrVenda']}</td>
							<td>{$productsVal['largura']}</td>
							<td>{$productsVal['espessura']}</td>
							<td>{$productsVal['comprimento']}</td>
							<td>{$productsVal['mt3']}</td>
							<td>{$productsVal['pesoBruto']}</td>
							<td>{$productsVal['pesoLiquido']}</td>
							<td>{$productsVal['origemProduto']}</td>
							<td>{$productsVal['stAtual']}</td>
							<td>{$productsVal['dataUpdate']}</td>
							<td>{$productsVal['totalizarQtdePedido']}</td>
	                         
	                         
	                     </tr>";
                     
                     	}
                     ?>
         
         		</tbody>
        		</table>
        		</div>
        	</div>
    	</div>
	</div>
</div>
<?php }?>
