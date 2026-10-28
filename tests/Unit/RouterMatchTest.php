<?php

declare(strict_types=1);

namespace WonderWp\Component\Routing\Tests\Unit;

use PHPUnit\Framework\TestCase;
use WonderWp\Component\HttpFoundation\Request;
use WonderWp\Component\Routing\Route\Route;
use WonderWp\Component\Routing\Router\Router;
use WonderWp\Component\Routing\Tests\Fixtures\FakeManager;
use WonderWp\Component\Routing\Tests\Fixtures\FakeRouteService;

final class RouterMatchTest extends TestCase
{
    private Router $router;

    protected function setUp(): void
    {
        $GLOBALS['wwp_test_redirects'] = [];
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/';
        $_SERVER['SCRIPT_NAME'] = '/index.php';

        $this->resetRequestSingleton();
        $this->router = new Router();
        $this->router->setServices([]);
    }

    protected function tearDown(): void
    {
        $this->resetRequestSingleton();
    }

    public function testMatchReturnsMissingRouteVariableWhenRouteKeyAbsent(): void
    {
        $result = $this->router->match([], 'GET');

        $this->assertInstanceOf(\WP_Error::class, $result);
        $this->assertSame(['missing_route_variable'], $result->get_error_codes());
    }

    public function testMatchReturnsRouteNotFoundForUnknownRouteName(): void
    {
        $result = $this->router->match(['route' => 'does-not-exist'], 'GET');

        $this->assertInstanceOf(\WP_Error::class, $result);
        $this->assertSame(['route_not_found'], $result->get_error_codes());
    }

    public function testMatchReturnsMethodNotAuthorizedWhenHttpMethodDiffers(): void
    {
        $this->seedRoute('post-only', new Route(['/post-only', 'handler', 'POST']));

        $result = $this->router->match(['route' => 'post-only'], 'GET');

        $this->assertInstanceOf(\WP_Error::class, $result);
        $this->assertSame(['method_not_authorized'], $result->get_error_codes());
    }

    public function testMatchReturnsRouteWhenMethodMatchesOrAll(): void
    {
        $this->seedRoute('get-route', new Route(['/get-route', 'handler', 'GET']));

        $matched = $this->router->match(['route' => 'get-route'], 'GET');

        $this->assertInstanceOf(Route::class, $matched);
        $this->assertSame('GET', $matched->getMethod());
    }

    public function testMatchRequestRedirectsTo404WhenRouteNotFound(): void
    {
        $environment = new \WP();
        $environment->query_vars = ['route' => 'missing-route-name'];

        $this->router->matchRequest($environment);

        $this->assertSame('/404', $GLOBALS['wwp_test_redirects'][0]['location'] ?? null);
    }

    public function testMatchRequestRedirectsTo503WhenMethodNotAuthorized(): void
    {
        $this->seedRoute('verbs', new Route(['/verbs', 'handler', 'PUT']));
        Request::getInstance()->setMethod('GET');

        $environment = new \WP();
        $environment->query_vars = ['route' => 'verbs'];

        $this->router->matchRequest($environment);

        $this->assertSame('/503', $GLOBALS['wwp_test_redirects'][0]['location'] ?? null);
    }

    public function testMatchRequestSetsWildcardQueryParamsOnRequest(): void
    {
        $fakeRouteService = new FakeRouteService();
        $fakeRouteService->setManager(new FakeManager('wonderwp', 1));
        $this->router->addService($fakeRouteService);
        $routes = $this->router->getRoutes();
        $routeName = (string) array_key_first($routes);
        /** @var Route $wildcardRoute */
        $wildcardRoute = $routes[$routeName];
        if (strpos($wildcardRoute->getPath(), '{') === false) {
            foreach ($routes as $name => $route) {
                if (strpos($route->getPath(), '{') !== false) {
                    $routeName = $name;
                    break;
                }
            }
        }

        $environment = new \WP();
        $environment->query_vars = [
            'route' => $routeName,
            'component' => 'news',
        ];

        $this->router->matchRequest($environment);
        $request = Request::getInstance();

        $this->assertSame('news', $request->query->get('component'));
    }

    private function seedRoute(string $name, Route $route): void
    {
        $reflection = new \ReflectionClass(Router::class);
        $property = $reflection->getProperty('routes');
        $property->setValue($this->router, [$name => $route]);
    }

    private function resetRequestSingleton(): void
    {
        $reflection = new \ReflectionClass(Request::class);
        $property = $reflection->getProperty('singletonInstance');
        $property->setValue(null, null);
    }
}
