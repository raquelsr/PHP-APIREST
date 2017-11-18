<?php
/**
 * Created by PhpStorm.
 * User: Raquel
 * Date: 18/11/17
 * Time: 16:09
 */

namespace Tests\AppBundle\Entity;



use AppBundle\Entity\Result;
use AppBundle\Entity\User;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * Class ResultTest
 *
 * @package Tests\AppBundle\Entity
 */
class ResultTest extends TestCase
{
    /**
     * @var Result
     */
    protected $result;

    /**
     * @var User
     */
    protected $user;

    protected function setUp()
    {
        $this->user = new User('user1', 'user1@mail.com','*user1',true,true);
        $this->result = new Result(10, $this->user);

    }

    /**
     * Implement testConstructor
     *
     * @covers \AppBundle\Entity\Result::__construct()
     *
     * @return void
     */
    public function testConstructor()
    {
        self::assertEquals(10, $this->result->getResult());
        self::assertEquals($this->user, $this->result->getUser());
        self::assertEquals(new \DateTime('now'), $this->result->getTime());
    }

    /**
     * Implement testGetId().
     *
     * @covers \AppBundle\Entity\Result::getId
     *
     * @return void
     */
    public function testGetId()
    {
        self::assertEmpty($this->result->getId());
    }

    /**
     * Implement testSetResult().
     *
     * @covers \AppBundle\Entity\Result::setResult()
     * @covers \AppBundle\Entity\Result::getResult()
     *
     * @return void
     */
    public function testSetResult()
    {
        $this->result->setResult(7);
        self::assertEquals(7, $this->result->getResult());
    }

    /**
     * Implement testSetUser().
     *
     * @covers \AppBundle\Entity\Result::setUser()
     * @covers \AppBundle\Entity\Result::getUser()
     *
     * @return void
     */
    public function testSetUser()
    {
        $user2 = new User('user2', 'user2@mail.com','*user2*', true,true);
        $this->result->setUser($user2);
        self::assertEquals($user2, $this->result->getUser());
    }


    /**
     * Implement testSetTime().
     *
     * @covers \AppBundle\Entity\Result::setTime()
     * @covers \AppBundle\Entity\Result::getTime()
     *
     * @return void
     */
    public function testSetTime()
    {
        $time = new \DateTime('tomorrow');
        $this->result->setTime($time);
        self::assertEquals($time, $this->result->getTime());
    }

    /**
     * Implement testSerialize().
     *
     * @covers \AppBundle\Entity\Result::jsonSerialize()
     *
     * @return void
     */
    public function testSerialize()
    {
        $this->result->setResult(9);
        $cadena = json_encode($this->result->jsonSerialize());
        self::assertJson($cadena);
    }

}