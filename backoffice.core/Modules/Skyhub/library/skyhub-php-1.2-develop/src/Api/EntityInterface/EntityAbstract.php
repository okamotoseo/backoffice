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

namespace SkyHub\Api\EntityInterface;

use SkyHub\Api;
use SkyHub\Api\Helpers;

abstract class EntityAbstract implements EntityInterface
{
    
    use Helpers;
    
    protected $data = [];
    
    /** @var Api */
    protected $api;

    /** @var Api\Handler\Request\HandlerAbstract */
    protected $requestHandler;
    
    
    /**
     * EntityAbstract constructor.
     *
     * @param Api\Handler\Request\HandlerAbstract $handler
     */
    public function __construct(Api\Handler\Request\HandlerAbstract $handler)
    {
        if (!empty($handler)) {
            $this->requestHandler = $handler;
            $this->api            = $handler->api();
        }
    }
    
    
    /**
     * @param string     $key
     * @param null|mixed $default
     *
     * @return array|bool|mixed|string
     */
    public function getData($key = null, $default = null)
    {
        if (is_null($key)) {
            return $this->data;
        }
        
        return $this->arrayExtract($this->data, $key, $default);
    }
    
    
    /**
     * @param string       $key
     * @param string|array $value
     *
     * @return $this
     */
    public function setData($key, $value)
    {
        $this->data[$key] = $value;
        return $this;
    }
    
    
    /**
     * @return array
     */
    public function export()
    {
        return $this->getData();
    }


    /**
     * @return Api
     */
    protected function api()
    {
        return $this->api;
    }


    /**
     * @return \SkyHub\Api\Service\ServiceAbstract
     */
    protected function service()
    {
        return $this->api()->service();
    }


    /**
     * @return Api\Handler\Request\HandlerAbstract
     */
    protected function requestHandler()
    {
        return $this->requestHandler;
    }


    public function validate()
    {
        return true;
    }
}