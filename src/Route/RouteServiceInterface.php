<?php

namespace WonderWp\Component\Routing\Route;

interface RouteServiceInterface
{
    /**
     * Compute your array of rules then return them
     * @return Route[]
     */
    public function getRoutes();
}
