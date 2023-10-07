<?php if ( ! defined('ABSPATH')) exit; ?>
<div class="row">
	<!-- Default box -->
	<div class="col-md-12">
	
		<div class="message">
			<?php if(!empty( $userModel->form_msg)){ echo  $userModel->form_msg;}?>
		</div>
		
		<div class="box box-primary">
        <form method="post" action="" autocomplete="false" >
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
                                echo "<option value='{$permission['permission']}' {$selected}>{$permission['name']}</option>";
                                }
                            ?>
                            </select>
             			</div>
					</div>
				</div>
			</div><!-- /.box-body -->
			<div class="box-footer">
            	<input type="submit" class='btn btn-sm btn-primary pull-right' value="Save" />
			</div>
			</form>
		</div><!-- /.box -->
	</div>
</div>
 
 
 
 
 
 
 
<?php 
$lista = $userModel->ListUsers(); 

?>
 
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
