<?php
/**
 * BSeller Platform | B2W - Companhia Digital
 *
 * Do not edit this file if you want to update this module for future new versions.
 *
 * @category  ${MAGENTO_MODULE_NAMESPACE}
 * @package   ${MAGENTO_MODULE_NAMESPACE}_${MAGENTO_MODULE}
 *
 * @copyright Copyright (c) 2018 B2W Digital - BSeller Platform. (http://www.bseller.com.br)
 *
 * @author    Tiago Sampaio <tiago.sampaio@e-smart.com.br>
 */

include __DIR__ . '/../../api.php';

/** @var \SkyHub\Api\Handler\Request\Sales\Order\QueueHandler $requestHandler */
$requestHandler = $api->queue();

$orderId = 'xyz';

/**
 * DELETE AN ORDER FROM QUEUE
 * @var SkyHub\Api\Handler\Response\HandlerInterface $response
 */
$response = $requestHandler->delete($orderId);

/**
 * GET A LIST OF ORDERS IN THE QUEUE
 * @var SkyHub\Api\Handler\Response\HandlerInterface $response
 */
$response = $requestHandler->orders();
