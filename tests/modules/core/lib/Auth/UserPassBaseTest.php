<?php

namespace SimpleSAML\Test\Module\core\Auth;

class UserPassBaseTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @return void
     */
    public function testAuthenticateECPCallsLoginAndSetsAttributes()
    {
        $state = [
            'saml:Binding' => \SAML2\Constants::BINDING_PAOS,
        ];
        $attributes = ['attrib' => 'val'];

        $username = $_SERVER['PHP_AUTH_USER'] = 'username';
        $password = $_SERVER['PHP_AUTH_PW'] = 'password';

        $stub = $this->getMockBuilder('\SimpleSAML\Module\core\Auth\UserPassBase')
            ->disableOriginalConstructor()
            ->setMethods(['login'])
            ->getMockForAbstractClass();

        $stub->expects($this->once())
            ->method('login')
            ->with($username, $password)
            ->will($this->returnValue($attributes));

        $stub->authenticate($state);

        $this->assertSame($attributes, $state['Attributes']);
    }


    /**
     * @return void
     */
    public function testAuthenticateECPMissingUsername()
    {
        $this->expectException(\SimpleSAML\Error\Error::class, 'WRONGUSERPASS');

        $state = [
            'saml:Binding' => \SAML2\Constants::BINDING_PAOS,
        ];

        unset($_SERVER['PHP_AUTH_USER']);
        $_SERVER['PHP_AUTH_PW'] = 'password';

        $stub = $this->getMockBuilder('\SimpleSAML\Module\core\Auth\UserPassBase')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $stub->authenticate($state);
    }


    /**
     * @return void
     */
    public function testAuthenticateECPMissingPassword()
    {
        $this->expectException(\SimpleSAML\Error\Error::class, 'WRONGUSERPASS');

        $state = [
            'saml:Binding' => \SAML2\Constants::BINDING_PAOS,
        ];

        $_SERVER['PHP_AUTH_USER'] = 'username';
        unset($_SERVER['PHP_AUTH_PW']);

        $stub = $this->getMockBuilder('\SimpleSAML\Module\core\Auth\UserPassBase')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $stub->authenticate($state);
    }


    /**
     * @return void
     */
    public function testAuthenticateECPCallsLoginWithForcedUsername()
    {
        $state = [
            'saml:Binding' => \SAML2\Constants::BINDING_PAOS,
        ];
        $attributes = [];

        $forcedUsername = 'forcedUsername';

        $_SERVER['PHP_AUTH_USER'] = 'username';
        $password = $_SERVER['PHP_AUTH_PW'] = 'password';

        $stub = $this->getMockBuilder('\SimpleSAML\Module\core\Auth\UserPassBase')
            ->disableOriginalConstructor()
            ->setMethods(['login'])
            ->getMockForAbstractClass();

        $stub->expects($this->once())
            ->method('login')
            ->with($forcedUsername, $password)
            ->will($this->returnValue($attributes));

        $stub->setForcedUsername($forcedUsername);

        $stub->authenticate($state);
    }
}
