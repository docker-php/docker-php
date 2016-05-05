<?php

namespace Docker\API\Model;

class ProgressDetail
{
    /**
     * @var int
     */
    protected $code;
    /**
     * @var int
     */
    protected $message;
    /**
     * @var int
     */
    protected $current;
    /**
     * @var int
     */
    protected $total;

    /**
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param int $code
     *
     * @return self
     */
    public function setCode($code = null)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return int
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param int $message
     *
     * @return self
     */
    public function setMessage($message = null)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return int
     */
    public function getCurrent()
    {
        return $this->current;
    }

    /**
     * @param int $current
     *
     * @return self
     */
    public function setCurrent($current = null)
    {
        $this->current = $current;

        return $this;
    }

    /**
     * @return int
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @param int $total
     *
     * @return self
     */
    public function setTotal($total = null)
    {
        $this->total = $total;

        return $this;
    }
}
