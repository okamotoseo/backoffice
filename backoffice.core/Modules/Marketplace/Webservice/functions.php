<?php 

function splitCategory($hierarchy){
	global $cat;

	$names = explode('/', $hierarchy);
	
	$cat[] = array('name' => end($names), 'hierarchy' => $hierarchy);
	
	$parts = explode('/', $hierarchy, -1);

	if(count($parts) >= 1){
		$hierarchy = '';
		foreach($parts as $key => $value){

			$hierarchy .= trim($value).'/';
		}
		$hierarchy = substr($hierarchy, 0,-1);
		splitCategory($hierarchy);
	}

	return $cat;

}

function getMenuXsd($db){
	
	$sql = "SELECT * FROM az_category_xsd";
	$query = $db->query($sql);
	return $query->fetchAll(PDO::FETCH_ASSOC);
	
}


// 		$parts = explode('/',$url);
// 		$parts2 = explode('._', end($parts));
// 		echo $productId = $parts2[0];
// 		echo '<br>';
// 		$alias = titleFriendly($key);

// 		$db->insert('az_category_xsd', array(
// 				'name' => $productId,
// 				'label' => $key,
// 				'alias' => $alias,
// 				'set_attribute' => 'ProductType',
// 				'xsd' => $url,
// 				'created' => date('Y-m-d H:i:s')
// 		));
?>