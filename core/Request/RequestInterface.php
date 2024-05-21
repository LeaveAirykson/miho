<?php

namespace App\Core\Request;

interface RequestInterface
{
    /**
     * Get current request method from environment
     * @return string
     */
    public function getCurrentMethod();


    public function getHeaders();

    public function getAuthToken();

    /**
     * Get request uri from environment
     * @return mixed
     */
    public function getCurrentUri();

    /**
     * Attach route params
     * @return mixed
     */
    public function setParams();

    /**
     * get route params
     * @return mixed
     */
    public function getParams();

    /**
     * Get variables from environment
     * @return mixed
     */
    public function getVariables();
}
