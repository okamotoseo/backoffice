<?php if ( ! defined('ABSPATH')) exit; ?>
<div class="row">
	<div class="col-xs-12">
		<div class="box">
			<div class="box-header">
	        	<h3 class="box-title"><?php echo $this->title; ?></h3>
			</div><!-- /.box-header -->
			<div class="box-body">
				<table class="table table-bordered table-striped">
				<thead>
					<tr>
						<th>#</th>
						<th>Nome</th>
						<th>Email</th>
						<th>Telefone</th>
						<th>Celular</th>
						<th>Plano</th>
						<th>Ação</th>
					</tr>
				</thead>
			<tbody>
<?php
$accounts = $accountManagementModel->ListAccounts();

foreach ($accounts as $accountInformation){
	echo "<tr>
			<td>{$accountInformation['id']}</td>
			<td>{$accountInformation['name']}</td>
			<td>{$accountInformation['email']}</td>
			<td>{$accountInformation['phone']}</td>
			<td>{$accountInformation['mobile']}</td>
			<td>{$accountInformation['plan_id']}</td>
			<td>
				<a href='".HOME_URI."/Admin/Account/edit/{$accountInformation['id']}' class='btn btn-info btn-xs' id='{$accountInformation['id']}' >Editar</a>
				<a href='".HOME_URI."/Admin/Store/AccountId/{$accountInformation['id']}' class='btn btn-info btn-xs' id='{$accountInformation['id']}' >+Loja</a>
			</td>
		</tr>
        <tr>
            <td colspan='7' style='border-top:none !important; '>
                <table class='table table-bordered table-striped'>";
	
	$storeModel->account_id = $accountInformation['id'];
	$stores = $storeModel->listStores();
	
	foreach ($stores as $storeInformation){
	    echo "<tr><td>{$storeInformation['id']}</td>
				<td>{$storeInformation['store']}</td>
				<td>{$storeInformation['email_sac']}</td>
				<td>{$storeInformation['phone']}</td>
				<td>{$storeInformation['cnpj']}</td>
				<td>
				<a class='btn btn-success btn-xs' id='{$storeInformation['id']}' href='".HOME_URI."/Admin/Store/edit/{$storeInformation['id']}/AccountId/{$accountInformation['id']}' > Editar </a>
			</td>
		</tr>";
	}
	echo "</table></td></tr>";
}

?>
			</tbody>
			</table>
			</div>
		</div>
</div>
