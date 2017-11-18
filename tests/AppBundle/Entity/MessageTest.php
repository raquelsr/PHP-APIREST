<?php
/**
 * Created by PhpStorm.
 * User: Raquel
 * Date: 18/11/17
 * Time: 15:59
 */

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Message;
use PHPUnit\Framework\TestCase;

/**
 * Class MessageTest
 *
 * @package Tests\AppBundle\Entity
 */
class MessageTest extends TestCase
{
    /**
     * @var Message
     */
    protected $message;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    protected function setUp()
    {
        $this->message = new Message(404,'Not Found');
    }


    /**
     * Implement testConstructor
     *
     * @covers \AppBundle\Entity\Message::__construct()
     *
     * @return void
     */
    public function testConstructor()
    {
        self::assertEquals(404, $this->message->getCode());
        self::assertEquals('Not Found', $this->message->getMessage());
    }

    /**
     * Implement testSetCode().
     *
     * @covers \AppBundle\Entity\Message::setCode()
     * @covers \AppBundle\Entity\Message::getCode()
     *
     * @return void
     */
    public function testSetCode()
    {
        $this->message->setCode(200);
        self:self::assertEquals(200, $this->message->getCode());
    }

    /**
     * Implement testSetMessage().
     *
     * @covers \AppBundle\Entity\Message::setMessage()
     * @covers \AppBundle\Entity\Message::getMessage()
     *
     * @return void
     */
    public function testSetMessage()
    {
        $this->message->setMessage('Mensaje cambiado');
        self:self::assertEquals('Mensaje cambiado', $this->message->getMessage());
    }

    /**
     * Implement testSerialize().
     *
     * @covers \AppBundle\Entity\Message::jsonSerialize()
     *
     * @return void
     */
    public function testSerialize()
    {
        $cadena = json_encode($this->message->jsonSerialize());
        self::assertJson($cadena);
    }

}