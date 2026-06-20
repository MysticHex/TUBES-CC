<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_root_points_to_dashboard_and_guests_reach_login(): void
    {
        $this->get('/')->assertRedirect(route('dashboard'));

        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }

    public function test_login_screen_renders(): void
    {
        $this->get(route('login'))->assertOk()->assertSee('Sign In');
    }
}
