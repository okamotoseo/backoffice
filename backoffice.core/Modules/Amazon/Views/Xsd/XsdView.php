<?php if ( ! defined('ABSPATH')) exit;?>
<div class='row'>
	<div class='col-md-12'>
		<div class="message"><?php if(!empty( $xsdModel->form_msg)){ echo  $xsdModel->form_msg;}?></div>
		<div class='box  box-primary'>
			<div class='box-header'>
	        	<h3 class='box-title'><?php echo $this->title; ?></h3>
	        	     	<div class='box-tools pull-right'>
    	        	<div class="form-group">
    	        		<a href='<?php echo HOME_URI ?>/Modules/Amazon/Xsd/Xsd/' class='btn btn-block btn-default btn-xs'><i class='fa fa-ban'></i> Limpar</a>
    	        	</div>
	        	</div>
			</div><!-- /.box-header -->
			<form method="POST" action="<?php echo HOME_URI ?>/Modules/Amazon/Xsd/Xsd/" name="form-messages">
			<input type="hidden" name="id" value="<?php echo $xsdModel->id; ?>" />
			<div class='box-body'>
				<div class="row">
					<div class="col-xs-2">
						<div class="form-group">
							<?php 
							$simpleType = $complexType = '';
							switch($xsdModel->type){
							    case "simpleType": $simpleType = "selected"; break;
							    case "complexType": $complexType = "selected"; break;
							    default : $select = "selected"; break;
							}
							?>
							<label for="type">Tipo:</label>
							<select id="type" name="type" class="form-control btn-sm">
							<option value='select' <?php echo $select; ?>> Selecione</option>
							<option value='simpleType' <?php echo $simpleType; ?>>simpleType</option>
							<option value='complexType' <?php echo $complexType; ?>>complexType</option>
							</select>
						</div>
					</div>
					<div class="col-xs-3">
						<div class="form-group">
							<label>Name:</label> 
							<input type="text" name="name" id="name" class="form-control"  value="<?php echo $xsdModel->name; ?>" />
						</div>
					</div>
					<div class="col-xs-4">
						<div class="form-group">
							<label>Label:</label> 
							<input type="text" name="label" id="label" class="form-control"  value="<?php echo $xsdModel->label; ?>" />
						</div>
					</div>
					<div class="col-xs-3">
						<div class="form-group">
							<label>SetAttribute:</label> 
							<input type="text" name="set_attribute" id="set_attribute" class="form-control"  value="<?php echo $xsdModel->set_attribute; ?>" />
						</div>
					</div>
					<div class="col-xs-8">
						<div class="form-group">
							<label>Xsd:</label> 
							<input type="text" name="xsd" id="subject" class="form-control"  value="<?php echo $xsdModel->xsd; ?>" />
						</div>
					</div>
					
				</div>
			</div>
			
			<div class="box-footer">	
				<button type="submit" class="btn btn-primary btn-sm pull-right" id="btn" name="save"><i class='fa fa-check'></i> Salvar</button>
			</div>
			</form>
		</div>
	</div>
</div>


<div class='row'>
	<div class="col-md-12">
		<div class='box box-primary'>
			<div class='box-header'>
		       	<h3 class='box-title'>XSDs de Categorias</h3>
			</div><!-- /.box-header -->
			<?php 
			
			if($this->userdata['email'] == 'willians.seo@gmail.com'){
				$thActions = "<th>Ações</th>";
			}
			
			?>
			<div class="box-body table-responsive">
				<div class="col-md-12">
    				<table id="search-default" class="table table-bordered  table-hover display" cellspacing="0" width="100%" role="grid" aria-describedby="example_info" style="width: 100%;">
    			        <thead>
    				        <tr>
    				        	<th>Label/Alias</th>
    				            <th>Nome</th>
    				            <th>SetAttribute</th>
    				            <th>Type</th>
    				            <?php echo $thActions; ?>
    				        </tr>
    			        </thead>
    		 		<tbody>
    		 
    	             <?php 
    	             if(isset($listXsd)){
    	             	
	    	             foreach ($listXsd as $fetch){
	    	             
		    	             echo "<tr>
		    	                 <td>{$fetch['label']}</td>
		    	                 <td><a href={$fetch['xsd']} target='_blank'>{$fetch['name']}</a></td>
		    	                 <td> {$fetch['set_attribute']}</td>
		    	                 <td> {$fetch['type']}</td>";
		    	             
			    	             if($this->userdata['email'] == 'willians.seo@gmail.com'){
			    	             	echo "<td align='right'>
			    	             	<a href='/Modules/Amazon/Xsd/Xsd/edit/{$fetch['id']}' class='fa fa-pencil-square-o' />&nbsp;&nbsp;
			    	             	<a href='/Modules/Amazon/Xsd/Xsd/del/{$fetch['id']}' class='fa fa-trash delete' />
			    	             	</td>";
			    	             
			    	             }
		    	                 
		    	             echo "</tr>";
	    	             
	    	             }
	    	             
    	             }
    	             
    	             ?>
    		 
    		 		</tbody>
    				</table>
				</div>
			</div>
		</div>
	</div>

</div>