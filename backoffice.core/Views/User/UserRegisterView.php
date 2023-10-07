<?php if ( ! defined('ABSPATH')) exit; 

$tabs = array(
    "users",
	"user-groups"
);


if(isset($_GET['tab']) AND !empty($_GET['tab'])){
	foreach($tabs as $ind){
		if ( $ind == $_GET['tab'] ) {
			$tabs[$ind] = "active";
		}else{
			$tabs[$ind] = "";
		}
	}
}
if(!in_array("active", $tabs)){
    $tabs['users'] = "active";
    
//     $tabs['user-groups'] = "active";
}

?>
<div class="row">
    <div class="col-md-12">
        <div class="nav-tabs-custom">
            
            <ul class="nav nav-tabs">
            	<li class="<?php echo $tabs['users']; ?>"><a href="#tab_1" data-toggle='tab'>Geral</a></li>
            	<li class="<?php echo $tabs['user-groups']; ?>"><a href="#tab_2" data-toggle='tab'>Grupos de Permissões</a></li>
            </ul>
            
            <div class="tab-content">
            
            	<div class="tab-pane <?php echo $tabs['users']; ?>" id="tab_1">
                	<div class="message"><?php if(!empty( $userModel->form_msg)){ echo  $userModel->form_msg;}?></div>
                    <div class='row'>
                       	<div class='col-xs-12'>
                       		<form method="post" action="" name='save-user' autocomplete="false" >
								<div class="box  box-primary">
							    	<div class="box-header with-border">
										<h3 class="box-title"><?php echo $this->title; ?></h3>
										<div class="box-tools pull-right">
											<a href="<?php echo HOME_URI . '/User/Register';?>" class='btn btn-xs btn-primary'>Novo</a>
										</div>
									</div>
	                                <div class="box-body">
		                                <div class="row">
											<div class="col-xs-6">		
												<div class="form-group">
								                   	<label>Nome: </label>
								                   	<input type="text" name="name" class="form-control" value="<?php echo $userModel->name; ?>" />
							            		</div>
				            				</div>
											<div class="col-xs-3">
												<div class="form-group">
						                            <label>Email: </label>
						                            <input type="text" name="email" class="form-control" autocomplete="false" value="<?php echo $userModel->email; ?>"  />
						             			</div>
											</div>
											<div class="col-xs-3">
												<div class="form-group">
						                            <label>Password: </label>
						                            <input type="text" name="password" class="form-control" autocomplete="false" value=""  /> 
						            			</div>
											</div>
											<div class="col-xs-6">
												<div class="form-group">
						                            <label>Lojas: </label>
						                            <select class="form-control select2" multiple="multiple"  name='stores[]'>
						                            <?php 
						                            foreach($storesList as $key => $store){
						                                    $selected = in_array($store['id'], $userModel->stores) ? "selected" : "";
						                                    echo "<option value='{$store['id']}' {$selected}>{$store['store']}</option>";
						                                }
						                            ?>
						                            </select>
						             			</div>
											</div>
											<div class="col-xs-6">
												<div class="form-group">
						                            <label>Permissions: </label>
						                            <select class="form-control select2" multiple="multiple"  name='permissions[]'>
						                            <?php 
						                            foreach($permissions as $key => $permission){
						                                $selected = in_array($permission['permission'], $userModel->permissions) ? "selected" : "";
						                                if($permission['permission'] != 'any'){
						                                	echo "<option value='{$permission['permission']}' {$selected}>{$permission['name']}</option>";
						                                }else{
						                                	if($this->userdata['cpf'] == '30456130802'){
						                                		echo "<option value='{$permission['permission']}' {$selected}>{$permission['name']}</option>";
						                                	}
						                                }
						                            }
						                            ?>
								                     </select>
												</div>
											</div>
		                                 	
		                                </div>
	                            	</div>
	                            	<div class="box-footer">
								    	<button type="submit" class="btn btn-primary btn-sm pull-right" name="save-user"><i class='fa fa-check'></i> Salvar</button>
									</div>
                            	</div>
                        	</form>
                    	</div>
              		</div>
              		
              		
              		<div class="row">
						<div class="col-xs-12">
						<div class="box">
						<div class="box-header">
							<h3 class="box-title"></h3>
						</div>
						<div class="box-body table-responsive">
							<table class="table table-hover">
					        <thead>
					        <tr>
					            <th>ID</th>
					            <th>Usuário</th>
					            <th>Name</th>
					            <th>Permissões</th>
					            <th>Edição</th>
					        </tr>
					        </thead>
					 		<tbody>
					 
					             <?php foreach ($lista as $fetch_userdata): ?>
					             
					             <tr>
					                 <td> <?php echo $fetch_userdata['id'] ?> </td>
					                 <td> <?php echo $fetch_userdata['email'] ?> </td>
					                 <td> <?php echo $fetch_userdata['name'] ?> </td>
					                 <td> <?php echo implode( ',', unserialize( $fetch_userdata['permissions'] ) ) ?> </td>
					                 <td> 
					                     <a href="<?php echo HOME_URI ?>/User/Register/edit/<?php echo $fetch_userdata['id'] ?>">Edit</a>
					                     <a href="<?php echo HOME_URI ?>/User/Register/del/<?php echo $fetch_userdata['id'] ?>">Delete</a>
					                 </td>
					             </tr>
					             
					             <?php endforeach;?>
					 
					 		</tbody>
							</table>
							</div>
						</div>
					</div>
				</div>


                   	
				</div><!-- /.tab-pane -->
                   	
                <div class="tab-pane <?php echo $tabs['user-groups']; ?>" id="tab_2">
                <?php if(!empty($permissionGrouprModel->p_group)){ ?>
                	<div class="message"><?php if(!empty( $permissionGrouprModel->form_msg)){ echo  $permissionGrouprModel->form_msg;}?></div>
                    <div class='row'>
                       	<div class='col-xs-12'>
                       		<form method="post" action="" name='save-usergroups' autocomplete="false" >
								<div class="box  box-primary">
						    		<div class="box-header with-border">
										<h3 class="box-title"><?php echo $this->title; ?></h3>
										<div class="box-tools pull-right">
											<a href="<?php echo HOME_URI . "/User/Register/ResetDefault/{$permissionGrouprModel->p_group}?tab=user-groups"; ?>" class='btn btn-xs btn-default'>Redefinir</a>
										</div>
									</div>
	                                <div class="box-body">
		                                
			                        	<div class="row">
	
											<div class="col-sm-4">
												<div class="form-group">
													<label>Selecione o Grupo:</label>
													<select class='form-control' id='p_group' name="p_group">
													<option value=''> Selecione</option>
													<option value='2' selected> Adiministrador</option>
													<option value='3'> Gerente</option>
													<option value='4'> Faturista</option>
													<option value='5'> Estoquista</option>
													<option value='6'> Editor</option>
													<option value='7'> Colaborador</option>
													</select>
												</div>
											</div>
											
										</div>
										
										<div class="row">
											<div class="col-sm-12">
											<table class='table table-bordered'>
												<tr>
													<th>Modulo</th>
													<th style='text-align:center'>Vizualizar</th>
													<th style='text-align:center'>Criar</th>
													<th style='text-align:center'>Atualizar</th>
													<th style='text-align:center'>Excluir</th>
												</tr>
												<?php 
												foreach($permissionGrouprModel->defaultModules as $key => $label){
													
													
													$p_viewChecked = $modules[$key]['p_view'] == 'T' ? 'checked' : '';
													$p_createChecked = $modules[$key]['p_create'] == 'T' ? 'checked' : '';
													$p_updateChecked = $modules[$key]['p_update'] == 'T' ? 'checked' : '';
													$p_deleteChecked = $modules[$key]['p_delete'] == 'T' ? 'checked' : '';
						
														echo "<tr>
																<td>{$label}</td>
																<td style='text-align:center'><input name='group-permissions[{$key}][p_view]' type='checkbox' class='flat-red' value='{$modules[$key]['p_view']}' {$p_viewChecked}></td>
																<td style='text-align:center'><input name='group-permissions[{$key}][p_create]' type='checkbox' class='flat-red' value='{$modules[$key]['p_create']}' {$p_createChecked}></td>
																<td style='text-align:center'><input name='group-permissions[{$key}][p_update]' type='checkbox' class='flat-red' value='{$modules[$key]['p_update']}' {$p_updateChecked}></td>
																<td style='text-align:center'><input name='group-permissions[{$key}][p_delete]' type='checkbox' class='flat-red' value='{$modules[$key]['p_delete']}' {$p_deleteChecked}></td>
															</tr>";
													
												}
												
												?>
												
											</table>
						    				</div>
										</div> 
									</div>
									<div class="box-footer">
							           	<button type="submit" class="btn btn-primary btn-sm pull-right" name="save-usergroups"><i class='fa fa-check'></i> Salvar</button>
									</div>
                            	</div>
                        	</form>
                    	</div>
              		</div>
              		<?php } ?>
              		<div class="row">
						<div class="col-xs-12">
							<div class="box">
							<div class="box-header">
								<h3 class="box-title"></h3>
							</div>
							<div class="box-body table-responsive">
								<table class="table table-hover">
						        <thead>
						        <tr>
						            <th>Grupo</th>
						            <th>Açoes</th>
						        </tr>
						        </thead>
						 		<tbody>
						 		<?php 
						 		
						             foreach($permissionGrouprModel->defaultGroups as $key => $label){
							             echo "<tr>
							                 <td>{$label}</td>
							                 <td><a href='".HOME_URI."/User/Register/p_group/{$key}?tab=user-groups'><i class='fa fa-pencil-square-o' ></i></a></td>
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
                
            </div><!-- /.tab-content -->
        </div><!-- nav-tabs-custom -->
    </div><!-- /.col -->
</div> <!-- /.row -->
<!-- END CUSTOM TABS -->
 
 
 
 
 
 
 
 

