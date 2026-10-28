<?php

declare(strict_types=1);

namespace WonderWp\Component\Routing\Tests\Fixtures;

use WonderWp\Component\PluginSkeleton\ManagerInterface;
use WonderWp\Component\Routing\Route\AbstractRouteService;

/**
 * Lightweight manager double — avoids constructing real AbstractPluginManager
 * (which boots Container + register() against WordPress).
 */
final class FakeManager implements ManagerInterface
{
    private array $config = [];

    public function __construct(string $name = '', $version = '')
    {
    }

    public function getConfig($key = null, $default = null)
    {
        if ($key === null) {
            return $this->config;
        }

        return $this->config[$key] ?? $default;
    }

    public function setConfig($key, $value = null)
    {
        $this->config[$key] = $value;

        return $this;
    }
}

final class FakeRouteService extends AbstractRouteService
{
    public function getRoutes()
    {
        if (empty($this->routes)) {
            $this
                ->addCallableRoute('route_name_inmanager', 'controllerAction')
                ->addCallableRoute('/url-to-catch/{component}', 'controllerAction')
                ->addFileRoute('route_name_inmanager', 'file_to_redirect_to.php');
        }

        return $this->routes;
    }
}
