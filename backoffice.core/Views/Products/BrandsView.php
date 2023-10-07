<?php if ( ! defined('ABSPATH')) exit;?>
<div class='row'>
	
	<div class="col-md-8">
		<div class='box box-primary'>
			<div class='box-header'>
		       	<h3 class='box-title'>Listagem de marcas</h3>
		       	<div class="row">
					<div class="col-sm-6"></div>
					<div class="col-sm-6"></div>
				</div>
			</div><!-- /.box-header -->
			
			<div class="box-body ">
				<table id="search-default" class="table table-hover table-condensed">
				<thead>
				        <tr>
				            <th>ID</th>
				            <th>Marca</th>
				            <th style='text-align:center;'>Produtos</th>
				            <th style='text-align:center;'>Estoque</th>
				            <th style='text-align:center;'>Variações</th>
				            <th style='text-align:center;'>Ticket Médio</th>
				            
				            <th></th>
				        </tr>
			        </thead>
		 		<tbody>
		 
	             <?php 
	             $itemsCount = $totalItems = $totalStock = 0 ; 
	             foreach ($list as $fetch): 
	             	$linkProductsBrand =  str_replace(" ", "_", removeAcentosNew($fetch['brand']));
	             	$totalItems += $fetch['items'];
	             	$totalStock += $fetch['stock'];
	             	$itemsCount++;
	             ?>
	             <tr>
	                 <td> <?php echo $fetch['id'] ?> </td>
	                 <td> <?php echo $fetch['brand'] ?> </td>
	                 <td align='center'><a href='<?php echo "/Products/AvailableProducts/Page/1/brand/{$linkProductsBrand}";?>' title='Listar produtos associados nesta marca' target='_blank'><?php echo $fetch['items'] ?></a></td>
	                 <td align='center'> <?php echo $fetch['stock'] ?> </td>
	                 <td align='center'> <?php echo $fetch['variations'] ?> </td>
	                 <td align='center'> <?php echo $fetch['ticket'] ?> </td>
	                 <td align='right'>
	                	<div class='dropdown'>
			            <a class='btn dropdown-toggle' data-toggle='dropdown' href='# ariel-expanded='true''><span class='fa fa-ellipsis-v'></span></a>
				            <ul class='dropdown-menu pull-right'>
                     			<li role='presentation'><a href="/Products/Brands/edit/<?php echo $fetch['id'] ?>"  /><i class='fa fa-pencil-square-o'></i> Editar</li>
                     			<li role='presentation'><a href="/Products/Brands/del/<?php echo $fetch['id'] ?>" /><i class='fa fa-trash delete' ></i> Remover</li>
                     		</ul>
                     	</div>
	                 </td>
	             </tr>
	             
	             <?php endforeach;?>
		 
		 		</tbody>
		 		<tfoot>
		 				<tr>
				            <th><?php echo $itemsCount; ?></th>
				            <th></th>
				            <th style='text-align:center;'><?php echo $totalItems; ?></th>
				            <th style='text-align:center;'><?php echo $totalStock; ?></th>
				            <th style='text-align:center;'></th>
				            <th style='text-align:center;'></th>
				            
				            <th></th>
				        </tr>
		 		</tfoot>
				</table>
			</div>
			<div class="overlay" style='display:none;'>
            	<i class="fa fa-refresh fa-spin"></i>
            </div>
		</div>
	</div>
	<div class='col-md-4'>
		<div class="message"><?php if(!empty( $brandsModel->form_msg)){ echo  $brandsModel->form_msg;}?></div>
		<div class='box box-primary'>
			<div class='box-header'>
	        	<h3 class='box-title'>Cadastrar marca</h3>
	        	<div class='box-tools pull-right'>
    	        	<div class="form-group">
    	        		<a href='/Products/Brands' class='btn btn-block btn-default btn-xs'><i class='fa fa-ban'></i>Limpar</a>
    	        	</div>
	        	</div>
			</div><!-- /.box-header -->
			<form method="POST" action="/Products/Brands/" name="form-brands">
			<input type="hidden" name="id" value="<?php echo $brandsModel->id; ?>" />
			<div class='box-body'>
				<div class="row">
					<div class="col-xs-12">
						<div class="form-group">
							<label>Marca:</label> 
							<input type="text" name="brand" id="brand" class="form-control brand"  value="<?php echo $brandsModel->brand; ?>" />
						</div>
					</div>

					<div class="col-xs-12">
						<div class="form-group">
							<label>Descrição:</label> 
							<textarea type="text" rows='4' name="description" id="description" class="form-control description" placeholder="Descrição"><?php echo $brandsModel->description; ?></textarea>
						</div>
					</div>
				</div>
			</div>
			<div class="overlay" style='display:none;'>
            	<i class="fa fa-refresh fa-spin"></i>
            </div>
			<div class="box-footer">	
				<button type="submit" class="btn btn-primary pull-right btn-sm" id="btn" name="save"><i class='fa fa-check'></i> Salvar</button>
			</div>
			</form>
		</div>
	</div>
</div>