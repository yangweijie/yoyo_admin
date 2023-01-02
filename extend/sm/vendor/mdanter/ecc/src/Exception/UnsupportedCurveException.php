<?php


namespace Mdanter\Ecc\Exception;

use Throwable;

class UnsupportedCurveException extends \RuntimeException
{
    /**
     * @var null|string
     */
    private $oid;

    /**
     * @var null|string
     */
    private $curveName;

    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function setCurveName(string $curveName)
    {
        $this->curveName = $curveName;
        return $this;
    }

    public function setOid(string $oid)
    {
        $this->oid = $oid;
        return $this;
    }

    public function hasCurveName()
    {
        return is_string($this->curveName);
    }

    public function hasOid()
    {
        return is_string($this->oid);
    }

    public function getCurveName()
    {
        return $this->curveName;
    }

    public function getOid()
    {
        return $this->oid;
    }
}
