<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_normal_user_is_blocked_from_admin()
    {
        $user = User::factory()->create(['role' => 'user']);
        
        $response = $this->actingAs($user)->get('/admin');
        
        $response->assertStatus(403);
    }
    
    public function test_admin_user_can_access_admin()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        $response = $this->actingAs($admin)->get('/admin');
        
        $response->assertStatus(200);
        $response->assertSee('Dashboard');
        $response->assertSee('Total Users');
    }
}
