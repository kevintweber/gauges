<?php

namespace kevintweber\Gauges;

class Response
{
    /** @var string */
    protected $json;

    /** @var int */
    protected $status;

    /**
     * Constructor
     *
     * @param int    $status The http status code.
     * @param string $json   The JSON response.
     */
    public function __construct($status, $json)
    {
        $this->json = $json;
        $this->status = $status;
    }

    public function getArray()
    {
        return json_decode($this->json, true);
    }

    public function getRawJSON()
    {
        return $this->json;
    }

    public function getStdObj()
    {
        return json_decode($this->json);
    }

    public function getStatus()
    {
        return $this->status;
    }
}