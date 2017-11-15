<?php // apiNov2017 - Message.php

namespace AppBundle\Entity;

/**
 * Class Message
 *
 * @package AppBundle\Entity
 */
class Message implements \JsonSerializable
{
    /**
     * Code
     *
     * @var int
     */
    private $_code;

    /**
     * Message
     *
     * @var string
     */
    private $_message;

    /**
     * Message constructor.
     *
     * @param int    $code    code
     * @param string $message message
     */
    public function __construct(int $code, string $message)
    {
        $this->_code = $code;
        $this->_message = $message;
    }

    /**
     * Get code
     *
     * @return int
     */
    public function getCode(): int
    {
        return $this->_code;
    }

    /**
     * Set code
     *
     * @param int $_code code
     *
     * @return void
     */
    public function setCode(int $_code)
    {
        $this->_code = $_code;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage(): string
    {
        return $this->_message;
    }

    /**
     * Set message
     *
     * @param string $_message message
     *
     * @return void
     */
    public function setMessage(string $_message)
    {
        $this->_message = $_message;
    }

    /**
     * Specify data which should be serialized to JSON
     *
     * @link   http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since  5.4.0
     */
    public function jsonSerialize()
    {
        return [
            'code' => $this->getCode(),
            'message' => $this->getMessage()
        ];
    }
}
