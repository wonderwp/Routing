<?php

declare(strict_types=1);

namespace WonderWp\Component\Routing\Tests\Unit;

use PHPUnit\Framework\TestCase;
use WonderWp\Component\Routing\Route\Route;
use WonderWp\Component\Routing\Router\Router;
use WonderWp\Component\Routing\Tests\Fixtures\FakeManager;
use WonderWp\Component\Routing\Tests\Fixtures\FakeRouteService;

final class RouterTest extends TestCase
{
    private Router $router;

    protected function setUp(): void
    {
        $this->router = new Router();
    }

    public function testAddServiceShouldAdd(): void
    {
        $this->router->setServices([]);
        $fakeRouteService = new FakeRouteService();
        $this->router->addService($fakeRouteService);
        $this->assertSame([$fakeRouteService], $this->router->getServices());
    }

    public function testGetRoutes(): void
    {
        $this->router->setServices([]);
        $fakeManager = new FakeManager('wonderwp', 1);
        $fakeRouteService = new FakeRouteService();
        $fakeRouteService->setManager($fakeManager);
        $this->router->addService($fakeRouteService);

        $computedRoutes = $this->router->getRoutes();
        $originalRoutes = $fakeRouteService->getRoutes();
        $this->assertNotEmpty($computedRoutes);
        $this->assertCount(count($originalRoutes), $computedRoutes);
        $this->assertInstanceOf(Route::class, reset($computedRoutes));
    }

    public function testRegisterQueryVarsWithVarShouldAddVar(): void
    {
        $this->router->setServices([]);
        $fakeManager = new FakeManager('wonderwp', 1);
        $fakeRouteService = new FakeRouteService();
        $fakeRouteService->setManager($fakeManager);
        $this->router->addService($fakeRouteService);

        $vars = $this->router->registerQueryVars(['key1']);
        $this->assertSame(['key1', 'component'], $vars);
    }
}
