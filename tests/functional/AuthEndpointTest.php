<?php

namespace app\tests\functional;

use PHPUnit\Framework\TestCase;

class AuthEndpointTest extends TestCase
{
    private TestHttpClient $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = new TestHttpClient();
    }

    public function testLoginViewDisplaysForm()
    {
        $response = $this->client->get('/login');
        $this->assertEquals(200, $response['statusCode']);
        $this->assertStringContainsString('Admin Login', $response['body']);
        $this->assertStringContainsString('name="LoginForm[username]"', $response['body']);
        $this->assertStringContainsString('name="LoginForm[password]"', $response['body']);
    }

    public function testLoginFailureWithWrongPassword()
    {
        // First GET /login to retrieve CSRF token
        $this->client->get('/login');

        // Post invalid credentials
        $response = $this->client->post('/login', [
            'LoginForm[username]' => 'admin',
            'LoginForm[password]' => 'wrong_password_123',
        ]);

        $this->assertEquals(200, $response['statusCode']);
        $this->assertStringContainsString('Incorrect username or password.', $response['body']);
    }

    public function testLoginSuccessAndLogout()
    {
        // 1. GET /login to extract CSRF token
        $this->client->get('/login');

        // 2. POST valid credentials
        $response = $this->client->post('/login', [
            'LoginForm[username]' => 'admin',
            'LoginForm[password]' => 'admin123',
            'LoginForm[rememberMe]' => '0',
        ]);

        // Should redirect after successful login
        $this->assertEquals(302, $response['statusCode']);

        // 3. Follow redirect or check admin protected area
        $adminResponse = $this->client->get('/admin/casinos');
        $this->assertEquals(200, $adminResponse['statusCode']);
        $this->assertStringContainsString('Logout (admin)', $adminResponse['body']);

        // 4. Test Logout
        $logoutResponse = $this->client->post('/logout');
        $this->assertEquals(302, $logoutResponse['statusCode']);

        // 5. Subsequent access to admin should redirect to login
        $subsequentAdmin = $this->client->get('/admin/casinos');
        $this->assertEquals(302, $subsequentAdmin['statusCode']);
        $this->assertStringContainsString('login', $subsequentAdmin['headers']['location'] ?? '');
    }
}
