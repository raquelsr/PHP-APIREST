<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\User;
use PHPUnit\Framework\TestCase;

/**
 * Class UsuarioTest
 *
 * @package Tests\AppBundle\Entity
 */
class UsuarioTest extends TestCase
{

    /**
     * @var User
     */
    protected $usuario;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    protected function setUp()
    {
        $this->usuario = new User();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    protected function tearDown()
    {
    }

    /**
     * Implement testConstructor
     *
     * @covers \AppBundle\Entity\User::__construct()
     *
     * @return void
     */
    public function testConstructor()
    {
        self::assertEquals(0, $this->usuario->getId());
        self::assertEmpty($this->usuario->getUsername());
        self::assertEmpty($this->usuario->getEmail());
        self::assertTrue($this->usuario->isEnabled());
        self::assertFalse($this->usuario->isAdmin());
    }

    /**
     * Implement testGetId().
     * 
     * @covers \AppBundle\Entity\User::getId
     *
     * @return void
     */
    public function testGetId()
    {
        self::assertEmpty($this->usuario->getId());
    }

    /**
     * Implement testSetUsername().
     * 
     * @covers \AppBundle\Entity\User::setUsername
     * @covers \AppBundle\Entity\User::getUsername
     *
     * @return void
     */
    public function testSetUsername()
    {
        $this->usuario->setUsername('testUsuario');
        self::assertEquals(
            'testUsuario',
            $this->usuario->getUsername()
        );
    }

    /**
     * Implement testSetPassword().
     * 
     * @covers \AppBundle\Entity\User::setPassword
     * @covers \AppBundle\Entity\User::getPassword
     * @covers \AppBundle\Entity\User::validatePassword
     *
     * @return void
     */
    public function testSetPassword()
    {
        $this->usuario->setPassword('testUsuario');
        self::assertTrue($this->usuario->validatePassword('testUsuario'));
    }

    /**
     * Implement testIsAdmin().
     *
     * @covers \AppBundle\Entity\User::setAdmin
     * @covers \AppBundle\Entity\User::isAdmin
     *
     * @return void
     */
    public function testIsAdmin()
    {
        $this->usuario->setAdmin(true);
        self::assertTrue($this->usuario->isAdmin());
    }

    /**
     * Implement testIsActive().
     * 
     * @covers \AppBundle\Entity\User::setEnabled
     * @covers \AppBundle\Entity\User::isEnabled
     *
     * @return void
     */
    public function testIsEnabled()
    {
        $this->usuario->setEnabled(true);
        self::assertTrue($this->usuario->isEnabled());
        $this->usuario->setEnabled(false);
        self::assertFalse($this->usuario->isEnabled());
    }

    /**
     * Implement testSetEmail().
     * 
     * @covers \AppBundle\Entity\User::setEmail
     * @covers \AppBundle\Entity\User::getEmail
     *
     * @return void
     */
    public function testSetEmail()
    {
        $this->usuario->setEmail('testUsuario@example.com');
        self::assertEquals(
            'testUsuario@example.com',
            $this->usuario->getEmail()
        );
    }

    /**
     * Implement testSerialize().
     * 
     * @covers \AppBundle\Entity\User::jsonSerialize()
     *
     * @return void
     */
    public function testSerialize()
    {
        $this->usuario->setUsername('testUsuario');
        $cadena = json_encode($this->usuario->jsonSerialize());
        self::assertJson($cadena);
    }

    /**
     * Implement test__toString().
     * 
     * @covers \AppBundle\Entity\User::__toString
     *
     * @return void
     */
    public function test__toString()
    {
        $this->usuario->setUsername('testUsuario');
        self::assertEquals(
            'testUsuario',
            $this->usuario->__toString()
        );
    }
}
