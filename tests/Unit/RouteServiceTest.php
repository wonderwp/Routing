<?php

declare(strict_types=1);

namespace WonderWp\Component\Routing\Tests\Unit;

use PHPUnit\Framework\TestCase;
use WonderWp\Component\Routing\Tests\Fixtures\FakeManager;
use WonderWp\Component\Routing\Tests\Fixtures\FakeRouteService;

final class RouteServiceTest extends TestCase
{
    private FakeRouteService $routeService;

    protected function setUp(): void
    {
        $fakeManager = new FakeManager('wonderwp', 1);
        $fakeManager->setConfig('testkey', ['fr_FR' => '/testurl/{testkey2}']);
        $this->routeService = new FakeRouteService();
        $this->routeService->setManager($fakeManager);
    }

    public function testGenerateUrlWithRouteAndParamsShouldGenerateParameterizedUrl(): void
    {
        $url = $this->routeService->generateUrl('testkey', ['testkey2' => 'testval2'], 'fr_FR');
        $this->assertSame('/testurl/testval2', $url);
    }
}
