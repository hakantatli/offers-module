<?php

namespace app\tests\unit\models;

use PHPUnit\Framework\TestCase;
use app\models\LoginForm;
use Yii;

class LoginFormTest extends TestCase
{
    public function testBlankCredentials()
    {
        $model = new LoginForm();
        $this->assertFalse($model->validate());
        $this->assertArrayHasKey('username', $model->errors);
        $this->assertArrayHasKey('password', $model->errors);
    }

    public function testWrongPassword()
    {
        $model = new LoginForm([
            'username' => 'admin',
            'password' => 'wrong_password',
        ]);
        $this->assertFalse($model->login());
        $this->assertTrue(Yii::$app->user->isGuest);
        $this->assertArrayHasKey('password', $model->errors);
    }

    public function testCorrectLogin()
    {
        $model = new LoginForm([
            'username' => 'admin',
            'password' => 'admin123',
        ]);
        $this->assertTrue($model->login());
        $this->assertFalse(Yii::$app->user->isGuest);
        $this->assertEquals('admin', Yii::$app->user->identity->username);

        // Logout after test
        Yii::$app->user->logout();
        $this->assertTrue(Yii::$app->user->isGuest);
    }
}
