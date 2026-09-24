<?php

namespace app\tests\unit\models;

use PHPUnit\Framework\TestCase;
use app\models\User;

class UserTest extends TestCase
{
    public function testFindUserById()
    {
        $user = User::findIdentity('100');
        $this->assertNotNull($user);
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('admin', $user->username);
        $this->assertEquals('100', $user->getId());

        $this->assertNull(User::findIdentity('99999'));
    }

    public function testFindUserByUsername()
    {
        $user = User::findByUsername('admin');
        $this->assertNotNull($user);
        $this->assertEquals('100', $user->id);

        // Case-insensitive check
        $userUpper = User::findByUsername('ADMIN');
        $this->assertNotNull($userUpper);

        $this->assertNull(User::findByUsername('nonexistent_user'));
    }

    public function testValidatePassword()
    {
        $user = User::findByUsername('admin');
        $this->assertNotNull($user);
        $this->assertTrue($user->validatePassword('admin123'));
        $this->assertFalse($user->validatePassword('wrong_password'));
    }

    public function testValidateAuthKey()
    {
        $user = User::findByUsername('admin');
        $this->assertNotNull($user);
        $this->assertTrue($user->validateAuthKey('revpanda_admin_auth_key_100'));
        $this->assertFalse($user->validateAuthKey('invalid_key'));
    }
}
