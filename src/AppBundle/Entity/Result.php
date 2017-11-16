<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Result
 *
 * @ORM\Table(
 *     name="results",
 *     indexes = {
 *          @ORM\Index(name="FK_USER_ID_idx", columns={ "user" })
 *      })
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ResultRepository")
 *
 */
class Result implements \JsonSerializable
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="result", type="integer", nullable=false)
     */
    private $result;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *     @ORM\JoinColumn(
     *          name                 = "user",
     *          referencedColumnName = "id",
     *          onDelete             = "cascade",
     *          nullable             = false
     *     )
     * })
     */
    private $user;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="time", type="datetime")
     */
    private $time;


    /**
     * Result constructor.
     * @param int $result
     * @param User $user_id
     * @param \DateTime $time
     */
    public function __construct(int $result, User $user)
    {
        $this->result = $result;
        $this->user = $user;
        $this->time = new \DateTime('now');
    }


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set result
     *
     * @param integer $result
     *
     * @return Result
     */
    public function setResult($result)
    {
        $this->result = $result;

        return $this;
    }

    /**
     * Get result
     *
     * @return int
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Set user
     *
     * @param User $user
     *
     * @return Result
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set time
     *
     * @param \DateTime $time
     *
     * @return Result
     */
    public function setTime($time)
    {
        $this->time = $time;

        return $this;
    }

    /**
     * Get time
     *
     * @return \DateTime
     */
    public function getTime()
    {
        return $this->time;
    }


    public function jsonSerialize()
    {
        return ['result' => [
            'id'            => $this->getId(),
            'result'        => $this->getResult(),
            'user'          => $this->getUser(),
            'time'          => $this->getTime()->format('d-m-Y H:i:s')
            ]
        ];
    }
}

