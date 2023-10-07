<?php
/**
 * B2W Digital - Companhia Digital
 *
 * Do not edit this file if you want to update this SDK for future new versions.
 * For support please contact the e-mail bellow:
 *
 * sdk@e-smart.com.br
 *
 * @category  SkuHub
 * @package   SkuHub
 *
 * @copyright Copyright (c) 2018 B2W Digital - BSeller Platform. (http://www.bseller.com.br).
 *
 * @author    Tiago Sampaio <tiago.sampaio@e-smart.com.br>
 */

namespace SkyHub\Api\DataTransformer\Sales\Order;

use SkyHub\Api\DataTransformer\DataTransformerAbstract;

class ShipmentException extends DataTransformerAbstract
{

    /**
     * ShipmentException constructor.
     *
     * @param string $orderId
     * @param string $datetime
     * @param string $observation
     * @param string $status
     */
    public function __construct($orderId, $datetime, $observation, $status)
    {
        /**
         * @todo Convert the $datetime to the correct format: '2012-10-06T04:13:00-03:00'
         */
        $shipmentException = [
            'shipment_exception' => [
                'occurrence_date' => $datetime,
                'observation'     => $observation
            ],
            'status' => $status
        ];

        $this->setOutputData($shipmentException);

        parent::__construct();
    }
}
