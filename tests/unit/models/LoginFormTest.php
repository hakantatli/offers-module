<?php

namespace app\tests\unit\models;

use PHPUnit\Framework\TestCase;
use app\models\LoginForm;
use Yii;

class LoginFormTest extends TestCase
{
    public function testBlankCredentials(): void
    {
        $model = new LoginForm();
        $this->assertFalse($model->validate());
        $this->assertArrayHasKey('username', $model->errors);
        $this->assertArrayHasKey('password', $model->errors);
    }

    public function testWrongPassword(): void
    {
        $model = new LoginForm([
            'username' => 'admin',
            'password' => 'wrong_password',
        ]);
        $this->assertFalse($model->login());
        $this->assertTrue(Yii::$app->user->isGuest);
        $this->assertArrayHasKey('password', $model->errors);
    }

    public function testCorrectLogin(): void
    {
        $model = new LoginForm([
            'username' => 'admin',
            'password' => 'admin123',
        ]);
        $this->assertTrue($model->login());
        $this->assertFalse(Yii::$app->user->isGuest);
        $identity = Yii::$app->user->identity;
        $this->assertInstanceOf(\app\models\User::class, $identity);
        $this->assertEquals('admin', $identity->username);

        // Logout after test
        Yii::$app->user->logout();
        $this->assertTrue(Yii::$app->user->isGuest);
    }
}
