<?php

namespace app\tests\functional;

use PHPUnit\Framework\TestCase;
use app\models\Casino;

class AdminCasinoEndpointTest extends TestCase
{
    private TestHttpClient $guestClient;
    private TestHttpClient $adminClient;

    protected function setUp(): void
    {
        parent::setUp();
        $this->guestClient = new TestHttpClient();
        $this->adminClient = new TestHttpClient();

        // Log in admin client
        $this->adminClient->get('/login');
        $this->adminClient->post('/login', [
            'LoginForm[username]' => 'admin',
            'LoginForm[password]' => 'admin123',
        ]);
    }

    protected function tearDown(): void
    {
        Casino::deleteAll(['like', 'name', 'Functional Test Casino']);
        parent::tearDown();
    }

    public function testCasinoIndexGuestRedirects(): void
    {
        $response = $this->guestClient->get('/admin/casinos');
        $this->assertEquals(302, $response['statusCode']);
        $this->assertStringContainsString('login', $response['headers']['location'] ?? '');
    }

    public function testCasinoIndexAdminAccess(): void
    {
        $response = $this->adminClient->get('/admin/casinos');
        $this->assertEquals(200, $response['statusCode']);
        $this->assertStringContainsString('Manage Casinos', $response['body']);
        $this->assertStringContainsString('+ New Casino', $response['body']);
    }

    public function testCasinoCreateForm(): void
    {
        // Guest is blocked
        $guestRes = $this->guestClient->get('/admin/casinos/create');
        $this->assertEquals(302, $guestRes['statusCode']);

        // Admin gets form
        $adminRes = $this->adminClient->get('/admin/casinos/create');
        $this->assertEquals(200, $adminRes['statusCode']);
        $this->assertStringContainsString('Create Casino', $adminRes['body']);
        $this->assertStringContainsString('name="Casino[name]"', $adminRes['body']);
    }

    public function testCasinoCreateValidationFailure(): void
    {
        // Extract CSRF token
        $this->adminClient->get('/admin/casinos/create');

        // Invalid rating (> 5.0)
        $response = $this->adminClient->post('/admin/casinos/create', [
            'Casino[name]' => 'Functional Test Casino Invalid',
            'Casino[rating]' => '8.5',
            'Casino[is_active]' => '1',
        ]);

        $this->assertEquals(200, $response['statusCode']);
        $this->assertStringContainsString('Rating (0.0 - 5.0) must be no greater than 5.', $response['body']);
    }

    public function testCasinoCreateSuccess(): void
    {
        $this->adminClient->get('/admin/casinos/create');

        $response = $this->adminClient->post('/admin/casinos/create', [
            'Casino[name]' => 'Functional Test Casino Golden',
            'Casino[rating]' => '4.75',
            'Casino[is_active]' => '1',
        ]);

        $this->assertEquals(302, $response['statusCode']);

        $casino = Casino::findOne(['name' => 'Functional Test Casino Golden']);
        $this->assertNotNull($casino);
        $this->assertEquals('functional-test-casino-golden', $casino->slug);
    }

    public function testCasinoView(): void
    {
        $casino = Casino::find()->one();
        $this->assertNotNull($casino);

        $response = $this->adminClient->get('/admin/casinos/' . $casino->id);
        $this->assertEquals(200, $response['statusCode']);
        $this->assertStringContainsString($casino->name, $response['body']);

        // Non-existent ID returns 404
        $notFoundRes = $this->adminClient->get('/admin/casinos/999999');
        $this->assertEquals(404, $notFoundRes['statusCode']);
    }

    public function testCasinoUpdate(): void
    {
        $casino = new Casino([
            'name' => 'Functional Test Casino To Update',
            'rating' => 4.0,
            'is_active' => 1,
        ]);
        $casino->save();

        // 1. GET update form
        $formRes = $this->adminClient->get('/admin/casinos/' . $casino->id . '/update');
        $this->assertEquals(200, $formRes['statusCode']);
        $this->assertStringContainsString('Update Casino: ' . $casino->name, $formRes['body']);

        // 2. POST update
        $updateRes = $this->adminClient->post('/admin/casinos/' . $casino->id . '/update', [
            'Casino[name]' => 'Functional Test Casino Updated Title',
            'Casino[rating]' => '4.95',
            'Casino[is_active]' => '1',
        ]);
        $this->assertEquals(302, $updateRes['statusCode']);

        $casino->refresh();
        $this->assertEquals('Functional Test Casino Updated Title', $casino->name);
        $this->assertEquals(4.95, (float)$casino->rating);
    }

    public function testCasinoDeleteVerbFilterAndExecution(): void
    {
        $casino = new Casino([
            'name' => 'Functional Test Casino To Delete',
            'rating' => 3.5,
            'is_active' => 1,
        ]);
        $casino->save();
        $casinoId = $casino->id;

        // GET request must be rejected by VerbFilter (405 Method Not Allowed)
        $getRes = $this->adminClient->get('/admin/casinos/' . $casinoId . '/delete');
        $this->assertEquals(405, $getRes['statusCode']);

        // POST request must delete the record and redirect (302)
        // Retrieve fresh CSRF token first
        $this->adminClient->get('/admin/casinos');
        $postRes = $this->adminClient->post('/admin/casinos/' . $casinoId . '/delete');
        $this->assertEquals(302, $postRes['statusCode']);

        $this->assertNull(Casino::findOne($casinoId));
    }
}
