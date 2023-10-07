<?php if ( ! defined('ABSPATH')) exit; 

	foreach ($modules as $moduleVal){
		
		
		$checked = $link = '';
		if( in_array($moduleVal['id'], $storeModel->modules) ){
			$checked ='checked';
			$link = "<a href='/Modules/Configuration/{$moduleVal['method']}/Setup/' class='btn btn-primary btn-xs pull-right'>Setup</a>";
		}else{
			$link = "<a href='/Modules/Configuration/{$moduleVal['method']}/Setup/' class='btn btn-default btn-xs pull-right'>Habilitar</a>";
		}
		
		echo "<div class='col-md-3'>
			<div class='box box-primary'>
				<div class='box-header with-border'>
					<label>{$moduleVal['name']}</label>
				</div>
				<div class='box-body' style='height:100px;'>
					<p>{$moduleVal['description']}</p>
				</div>
				<div class='box-footer'>
					{$link}
				</div>
			</div>
		</div>";
	}



?>
