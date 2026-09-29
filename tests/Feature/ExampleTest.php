<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_application_route_is_registered(): void
    {
        $route = app('router')->getRoutes()->match(
            \Illuminate\Http\Request::create('/login', 'GET')
        );

        $this->assertSame('login', $route->getName());
    }
}
