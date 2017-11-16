<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Result
 *
 * @ORM\Table(
 *     name="results",
 *     indexes = {
 *          @ORM\Index(name="FK_USER_ID_idx", columns={ "user_id" })
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
     *          name                 = "user_id",
     *          referencedColumnName = "id",
     *          onDelete             = "cascade",
     *          nullable             = false
     *     )
     * })
     */
    private $user_id;

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
    public function __construct(int $result, User $user_id)
    {
        $this->result = $result;
        $this->user_id = $user_id;
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
     * @param User $user_id
     *
     * @return Result
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUserId()
    {
        return $this->user_id;
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
            'user'          => $this->getUserId(),
            'time'          => $this->getTime()->format('d-m-Y H:i:s')
            ]
        ];
    }
}

