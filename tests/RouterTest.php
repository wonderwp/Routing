<?php

namespace WonderWp\Component\Routing;

use PHPUnit\Framework\TestCase;
use WonderWp\Component\PluginSkeleton\AbstractPluginManager;
use WonderWp\Component\Routing\Route\AbstractRouteService;
use WonderWp\Component\Routing\Route\Route;
use WonderWp\Component\Routing\Router\Router;

class RouterTest extends TestCase
{
    /** @var  Router */
    private $router;

    public function setUp()
    {
        $this->router = new Router();
    }

    public function testAddServiceShouldAdd(){
        $this->router->setServices([]);
        $fakeRouteService = new FakeRouteService();
        $expected = [$fakeRouteService];
        $this->router->addService($fakeRouteService);
        $this->assertEquals($expected,$this->router->getServices());
    }

    private function resetRouteServices(){
        $this->router->setServices([]);
        $fakeManager = new FakeManager('wonderwp',1);
        $fakeRouteService = new FakeRouteService();
        $fakeRouteService->setManager($fakeManager);
        $this->router->addService($fakeRouteService);
        return $fakeRouteService;
    }

    public function testGetRoutes(){
        $fakeRouteService = $this->resetRouteServices();
        $computedRoutes = $this->router->getRoutes();
        $originalRoutes = $fakeRouteService->getRoutes();
        $this->assertNotEmpty($computedRoutes);
        $this->assertEquals(count($computedRoutes),count($originalRoutes));
        $this->assertInstanceOf(Route::class,reset($computedRoutes));
    }

    public function testRegisterQueryVarsWithVarShouldAddVar(){
        $this->resetRouteServices();
        $vars = ['key1'];
        $vars = $this->router->registerQueryVars($vars);
        $expected = ['key1','component'];
        $this->assertEquals($expected,$vars);
    }

    public function testRegisterRulesWithRouteServiceShouldAddRule(){
        global $wp_rewrite;
        $wp_rewrite = new \WP_Rewrite();
        global $wp;
        $wp = new \WP();
        $this->resetRouteServices();
        $this->router->registerRules();
        $this->assertArrayHasKey('^route_name_inmanager$',$wp_rewrite->extra_rules_top);
        $this->assertArrayHasKey('^url-to-catch/(.*)$',$wp_rewrite->extra_rules_top);
    }

    public function testFlushRules(){
        global $wp_rewrite;
        $wp_rewrite = new \WP_Rewrite();
        global $wp;
        $wp = new \WP();
        $this->resetRouteServices();
        $this->router->registerRules();

        $this->router->flushRules();

        $this->assertArrayHasKey('^route_name_inmanager$',$wp_rewrite->extra_rules_top);
        $this->assertArrayHasKey('^url-to-catch/(.*)$',$wp_rewrite->extra_rules_top);
    }

}

class FakeRouteService extends AbstractRouteService
{
    public function getRoutes()
    {
        if (empty($this->routes)) {
            $this
                ->addCallableRoute('route_name_inmanager', 'controllerAction')
                ->addCallableRoute('/url-to-catch/{component}', 'controllerAction')
                ->addFileRoute('route_name_inmanager', 'file_to_redirect_to.php')
            ;
        }

        return $this->routes;
    }
}

class FakeManager extends AbstractPluginManager
{

}
