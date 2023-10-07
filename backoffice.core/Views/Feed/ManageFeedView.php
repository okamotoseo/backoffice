<?php if ( ! defined('ABSPATH')) exit;?>
<div class='row'>
	<div class="col-md-6">
		<div class="message"><?php if(!empty( $feedModel->form_msg)){ echo  $feedModel->form_msg;}?></div>
		<div class="box box-primary">
			<form role="form" method="POST" action="<?php echo HOME_URI ?>/Feed/ManageFeed" name="feed-filter-products" >
				<input type="hidden" name="id"  value="<?php echo $feedModel->id; ?>">
				<div class="box-header with-border">
					<h3 class="box-title"><?php echo $this->title; ?></h3>
					<div class='box-tools pull-right'>
        	        	<div class="form-group">
        	        		<a href='<?php echo HOME_URI ?>/Feed/ManageFeed' class='btn btn-block btn-default btn-xs'><i class='fa fa-ban'></i> Limpar</a>
        	        	</div>
    	        	</div>
				</div>
				<div class="box-body">
					<div class="row">
						<div class="col-xs-4">
							<div class="form-group">
								<label for="layout">Layout</label>
								<select id="layout" name="layout" class="form-control input-sm">
								<option value='google_shopping'>Google Shopping</option>
								</select>
							</div>
						</div>
						<div class="col-xs-8">
							<div class="form-group">
								<label for="name">Nome do Feed:</label> 
								<input type="text" name="name"  id='name' class="form-control input-sm" value="<?php echo $feedModel->name; ?>">
							</div>
						</div>
						
	
						
						<div class="col-xs-8">
							<div class="form-group">
								<label for="url_store">URL:</label> 
								<input type="text" name="url_store"  id='url_store' class="form-control input-sm" value="<?php echo $feedModel->url_store; ?>">
							</div>
						</div>
			
					</div>
				</div>
				<div class="box-footer">
					<button type='submit' name='feed' class='btn btn-primary pull-right btn-sm' ><i class='fa fa-check'></i> Salvar</button>
				</div>
			</form>
		</div>
	</div>
	<div class="col-md-6">
		<div class="message">
		</div>
		
		<div class="box box-primary">
			<div class="box-header with-border">
				<h3 class="box-title">Feed de Produtos</h3>
				<div class="box-tools pull-right">
					<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
				</div>
			</div>
			<div class="box-body">
				<div class='row'>
    				<div class="col-md-12">
        				<table class="table table-bordered  table-hover display" cellspacing="0" width="100%" role="grid" aria-describedby="example_info" style="width: 100%;">
        					<thead>
        						<tr>
        							<th>Feed</th>
        							<th>Layout</th>
        							<th>Marca</th>
        							<th>Depart.</th>
        							<th>Qty Min.</th>
        							<th>N. Variações</th>
        							<th>Preço Min.</th>
        							<th></th>
        						</tr>
        					</thead>
        					<tbody>
        					<?php 
        					foreach ($list as $fetch): 
        						
        						echo "<tr>
        							<td>{$fetch['name']}</td>
                                    <td>{$fetch['layout']}</td>
        							<td>{$fetch['brand']}</td>
        							<td>{$fetch['category']}</td>
        							<td>{$fetch['min_qty']}</td>
        							<td>{$fetch['min_variations']}</td>
        							<td>{$fetch['min_price']}</td>
                                    <td>
                                    <div class='dropdown'>
                                        <a class='btn dropdown-toggle' data-toggle='dropdown' href='# ariel-expanded='true''><span class='fa fa-ellipsis-v'></span></a>
                                        <ul class='dropdown-menu pull-right' style='min-width:100px'>
                                            <li role='presentation'><a href='#' class='update_feed_products' id='{$fetch['id']}'  class='fa fa-edit'> Processar Feed</a> </li>   
                                            <li role='presentation'><a href='". HOME_URI ."/Feed/ManageFeed/edit/{$fetch['id']}' class='fa fa-edit'> Editar</a> </li>                                   
                                            <li role='presentation'><a href='". HOME_URI ."/Feed/ManageFeed/del/{$fetch['id']}' class='fa fa-trash'> Excluir</a> </li>   
                                        </ul>
                                    </div>
                                    </td>
        							</tr>";
                     
        		             endforeach;
                            ?>	
        					</tbody>
        				</table>
        			</div>
    			</div>
			</div><!-- /.box-body -->
			<div class="overlay feed-filter-products" style='display:none;'>
        		<i class="fa fa-refresh fa-spin"></i>
    		</div>
			
		</div><!-- /.box -->
	</div>
</div>
