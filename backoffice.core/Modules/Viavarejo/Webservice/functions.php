<?php
function deserializeErrors($errorsJson, $apiClient) {

	$errors = null;

	try {
		$errors = $apiClient->deserialize(json_decode($errorsJson), 'Errors');
	} catch (\Exception $e) {}

	return $errors;

}

function formatDateRange($initialDate, $finalDate, $apiClient) {

	$dtIni = '*';
	$dtEnd = '*';

	if ($initialDate != null && $initialDate instanceof \DateTime) {
		$dtIni = $initialDate->format(\DateTime::ISO8601);
	}

	if ($finalDate != null && $finalDate instanceof \DateTime) {
		$dtEnd = $finalDate->format(\DateTime::ISO8601);
	}

	return $dtIni . ',' . $dtEnd;

}