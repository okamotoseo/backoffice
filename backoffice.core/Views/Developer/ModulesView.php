<?php if ( ! defined('ABSPATH')) exit; ?>
<div class="row">
<div class='col-md-6'>
		<div class="message"><?php if(!empty( $modulesModel->form_msg)){ echo  $modulesModel->form_msg;}?></div>
		<div class='box box-primary'>
			<div class='box-header'>
	        	<h3 class='box-title'>Cadastrar marca</h3>
	        	<div class='box-tools pull-right'>
    	        	<div class="form-group">
    	        		<a href='<?php echo HOME_URI ?>/Developer/Modules' class='btn btn-block btn-default btn-xs'><i class='fa fa-plus'></i> Novo</a>
    	        	</div>
	        	</div>
			</div><!-- /.box-header -->
			<form method="POST" action="<?php echo HOME_URI ?>/Developer/Modules" name="form-modules">
			<input type="hidden" name="id" value="<?php echo $modulesModel->id; ?>" />
			<div class='box-body'>
				<div class="row">
				
					<div class="col-xs-12">
						<div class="form-group">
							<label>Nome:</label> 
							<input type="text" name="name" id="name" class="form-control name"  value="<?php echo $modulesModel->name; ?>" />
						</div>
					</div>
					
					<div class="col-xs-5">
							<div class="form-group">
    							<?php 
    							$comparison = $marketplace = $ecommerce = $erp = '';
    							switch($modulesModel->type){
    							    case "marketplace": $marketplace = "selected"; break;
    							    case "ecommerce": $ecommerce = "selected"; break;
    							    case "erp": $erp = "selected"; break;
    							    case "comparison": $comparison = "selected"; break;
    							    default : $marketplace = "selected"; break;
    							}
    							?>
								<label for="type">Tipo de Canal:</label>
								<select id="type" name="type" class="form-control">
    								<option value='Marketplace' <?php echo $marketplace; ?>>Marketplace</option>
    								<option value='Comparison' <?php echo $comparison; ?>>Comparadores</option>
    								<option value='Ecommerce' <?php echo $ecommerce; ?>>Ecommerce</option>
    								<option value='ERP' <?php echo $erp; ?>>ERP</option>
								</select>
							</div>
						</div>
					<div class="col-xs-5">
						<div class="form-group">
							<label>Método:</label> 
							<input type="text" name="method" id="method" class="form-control method"  value="<?php echo $modulesModel->method; ?>" />
						</div>
					</div>
					
                    <div class="col-xs-2">
						<div class="form-group">
							<label>Situação:</label> 
							<input type="text" name="status" id="status" class="form-control status"  value="<?php echo $modulesModel->status; ?>" />
						</div>
					</div>
					<div class="col-xs-12">
						<div class="form-group">
							<label>Descrição:</label> 
							<textarea type="text" rows='10' name="description" id="description" class="form-control description" placeholder="Descrição"><?php echo $modulesModel->description; ?></textarea>
						</div>
					</div>
				</div>
			</div>
			
			<div class="box-footer">	
				<button type="submit" class="btn btn-primary pull-right btn-sm" id="btn" name="save"><i class='fa fa-check'></i> Salvar</button>
			</div>
			</form>
		</div>
	</div>
	<div class="col-md-6">
    	<div class="box box-primary">
        	<div class="box-header">
        		<h3 class="box-title">Modulos Cadastrados</h3>
        	</div>
        	<div class="box-body table-responsive">
        		<table class="table table-hover">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Tipo</th>
                    <th>Método</th>
                    <th>Descrição</th>
                </tr>
                </thead>
         		<tbody>
         
                     <?php foreach ($modules as $moduleVal): ?>
                     
                     
                     <tr>
                         <td> <?php echo $moduleVal['id'] ?> </td>
                         <td> <?php echo $moduleVal['name'] ?> </td>
                         <td> <?php echo $moduleVal['type'] ?> </td>
                         <td> <?php echo $moduleVal['method'] ?> </td>
                         <td> <?php echo $moduleVal['description'] ?> </td>
                         
                     </tr>
                     
                     <?php endforeach;?>
         
         		</tbody>
        		</table>
        	</div>
    	</div>
	</div>
</div>