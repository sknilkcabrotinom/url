<?php

namespace League\Url\Test;

use League\Url\Pass;
use League\Url\User;
use League\Url\UserInfo;
use PHPUnit_Framework_TestCase;

/**
 * @group userinfo
 */
class UserInfoTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param $login
     * @param $pass
     * @param $expected
     * @dataProvider isEmptyProvider
     */
    public function testIsEmpty($login, $pass, $expected)
    {
        $this->assertSame($expected, (new UserInfo($login, $pass))->isEmpty());
    }

    public function isEmptyProvider()
    {
        return [
            ['login', 'pass', false],
            ['login', null, false],
            ['', '', true],
            [null, null, true],
            [null, 'pass', true],
            ['', 'pass', true],
        ];
    }

    public function testGetterMethod()
    {
        $userinfo = new UserInfo();
        $this->assertInstanceof('League\Url\Component', $userinfo->user);
        $this->assertInstanceof('League\Url\Component', $userinfo->pass);
    }

    /**
     * @param $login
     * @param $pass
     * @param $expected
     * @dataProvider toArrayProvider
     */
    public function testToArray($login, $pass, $expected, $expected_user, $expected_pass)
    {
        $userinfo = new UserInfo($login, $pass);
        $this->assertSame($expected, $userinfo->toArray());
        $this->assertSame($expected_user, $userinfo->getUser());
        $this->assertSame($expected_pass, $userinfo->getPass());
    }

    public function toArrayProvider()
    {
        return [
            ['login', 'pass', ['user' => 'login', 'pass' => 'pass'], 'login', 'pass'],
            ['login', null,   ['user' => 'login', 'pass' => null], 'login', ''],
            ['login', '',     ['user' => 'login', 'pass' => null], 'login', ''],
            ['', '',          ['user' => null   , 'pass' => null], '', ''],
            [null, null,      ['user' => null   , 'pass' => null], '', ''],
            ['', 'pass',      ['user' => null   , 'pass' => null], '', ''],
            [null, 'pass',    ['user' => null   , 'pass' => null], '', ''],
        ];
    }
}
