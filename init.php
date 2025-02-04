<?php

use WonderWp\Component\PluginSkeleton\Exception\ServiceNotFoundException;
use WonderWp\Component\Routing\Router\Router;
use WonderWp\Component\Service\ServiceInterface;
use WonderWp\Component\PluginSkeleton\ManagerInterface;
use WonderWp\Component\DependencyInjection\Container;
use WonderWp\Component\Routing\Route\RouteServiceInterface;

add_action('wonderwp.loader.load', 'wwp_register_routing_definitions_towards_container', 10, 2);
add_action('wwp.abstract_manager.run', 'wwp_register_route_service_towards_manager', 10, 2);

function wwp_register_routing_definitions_towards_container(Container $container)
{
    $container['wwp.routes.router'] = function () {
        return new Router();
    };
}

function wwp_register_route_service_towards_manager(ManagerInterface $manager, Container $container)
{
    // Routes
    try {
        $routeService = $manager->getService(ServiceInterface::ROUTE_SERVICE_NAME);
        if ($routeService instanceof RouteServiceInterface) {
            $router = $container['wwp.routes.router'];
            $router->addService($routeService);
        }
    } catch (ServiceNotFoundException $e) {
        if ($e->getServiceType() === ServiceInterface::ROUTE_SERVICE_NAME) {
            //No route service found, nothing to do here for now
        } else {
            throw $e;
        }
    }
}
