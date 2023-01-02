<?php

namespace Mdanter\Ecc\Exception;

use Mdanter\Ecc\Primitives\GeneratorPoint;
use Mdanter\Ecc\Primitives\PointInterface;

class PublicKeyException extends \RuntimeException
{
    /**
     * @var GeneratorPoint
     */
    private $G;

    /**
     * @var PointInterface
     */
    private $point;

    public function __construct(GeneratorPoint $G, PointInterface $point, $message = "",   $code = 0, $previous = null)
    {
        $this->G = $G;
        $this->point = $point;
        parent::__construct($message, $code, $previous);
    }

    public function getGenerator()
    {
        return $this->G;
    }

    public function getPoint()
    {
        return $this->point;
    }
}
