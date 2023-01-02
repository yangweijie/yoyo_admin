<?php

namespace Mdanter\Ecc\Math;

use Mdanter\Ecc\Primitives\GeneratorPoint;

/**
 * Debug helper class to trace all calls to math functions along with the provided params and result.
 */
class DebugDecorator implements GmpMathInterface
{
    /**
     * @var GmpMathInterface
     */
    private $adapter;

    /**
     * @var callable
     */
    private $writer;

    /**
     * @param GmpMathInterface     $adapter
     * @param callable|null        $callback
     */
    public function __construct(GmpMathInterface $adapter, callable $callback = null)
    {
        $this->adapter = $adapter;
        $this->writer = $callback ?: function ($message) {
            echo $message;
        };
    }

    /**
     *
     * @param string $message
     */
    private function write($message)
    {
        call_user_func($this->writer, $message);
    }

    /**
     *
     * @param  string $func
     * @param  array  $args
     * @return mixed
     */
    private function call($func, $args)
    {
        $strArgs = array_map(
            function ($arg) {
                if ($arg instanceof \GMP) {
                    return var_export($this->adapter->toString($arg), true);
                } else {
                    return var_export($arg, true);
                }
            },
            $args
        );

        if (strpos($func, '::')) {
            list(, $func) = explode('::', $func);
        }

        $this->write($func.'('.implode(', ', $strArgs).')');

        $res = call_user_func_array([ $this->adapter, $func ], $args);

        if ($res instanceof \GMP) {
            $this->write(' => ' . var_export($this->adapter->toString($res), true) . PHP_EOL);
        } else {
            $this->write(' => ' . var_export($res, true) . PHP_EOL);
        }

        return $res;
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\Math\GmpMathInterface::cmp()
     */
    public function cmp( $first,  $other)
    {
        $func = __METHOD__;
        $args = func_get_args();

        return call_user_func(
            array(
            $this,
            'call',
            ),
            $func,
            $args
        );
    }


    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\Math\GmpMathInterface::cmp()
     */
    public function equals(  $first, $other)
    {
        $func = __METHOD__;
        $args = func_get_args();

        return call_user_func(
            array(
                $this,
                'call',
            ),
            $func,
            $args
        );
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\Math\GmpMathInterface::mod()
     */
    public function mod($number, $modulus)
    {
        $func = __METHOD__;
        $args = func_get_args();

        return call_user_func(
            array(
            $this,
            'call',
            ),
            $func,
            $args
        );
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\Math\GmpMathInterface::add()
     */
    public function add($augend, $addend)
    {
        $func = __METHOD__;
        $args = func_get_args();

        return call_user_func(
            array(
            $this,
            'call',
            ),
            $func,
            $args
        );
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\Math\GmpMathInterface::sub()
     */
    public function sub($minuend, $subtrahend)
    {
        $func = __METHOD__;
        $args = func_get_args();

        return call_user_func(
            array(
            $this,
            'call',
            ),
            $func,
            $args
        );
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\Math\GmpMathInterface::mul()
     */
    public function mul($multiplier, $multiplicand)
    {
        $func = __METHOD__;
        $args = func_get_args();

        return call_user_func(
            array(
            $this,
            'call',
            ),
            $func,
            $args
        );
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\Math\GmpMathInterface::div()
     */
    public function div($dividend, $divisor)
    {
        $func = __METHOD__;
        $args = func_get_args();

        return call_user_func(
            array(
            $this,
            'call',
            ),
            $func,
            $args
        );
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\Math\GmpMathInterface::pow()
     */
    public function pow($base, int $exponent)
    {
        $func = __METHOD__;
        $args = func_get_args();

        return call_user_func(
            array(
            $this,
            'call',
            ),
            $func,
            $args
        );
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\Math\GmpMathInterface::bitwiseAnd()
     */
    public function bitwiseAnd($first, $other)
    {
        $func = __METHOD__;
        $args = func_get_args();

        return call_user_func(
            array(
            $this,
            'call',
            ),
            $func,
            $args
        );
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\MathAdapter::toString()
     */
    public function toString($value)
    {
        return $this->adapter->toString($value);
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\Math\GmpMathInterface::hexDec()
     */
    public function hexDec(string $hexString)
    {
        $func = __METHOD__;
        $args = func_get_args();

        return call_user_func(
            array(
            $this,
            'call',
            ),
            $func,
            $args
        );
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\Math\GmpMathInterface::decHex()
     */
    public function decHex(string $decString)
    {
        $func = __METHOD__;
        $args = func_get_args();

        return call_user_func(
            array(
            $this,
            'call',
            ),
            $func,
            $args
        );
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\Math\GmpMathInterface::powmod()
     */
    public function powmod($base, $exponent, $modulus)
    {
        $func = __METHOD__;
        $args = func_get_args();

        return call_user_func(
            array(
            $this,
            'call',
            ),
            $func,
            $args
        );
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\Math\GmpMathInterface::isPrime()
     */
    public function isPrime($n)
    {
        $func = __METHOD__;
        $args = func_get_args();

        return call_user_func(
            array(
            $this,
            'call',
            ),
            $func,
            $args
        );
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\Math\GmpMathInterface::nextPrime()
     */
    public function nextPrime($currentPrime)
    {
        $func = __METHOD__;
        $args = func_get_args();

        return call_user_func(
            array(
            $this,
            'call',
            ),
            $func,
            $args
        );
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\Math\GmpMathInterface::inverseMod()
     */
    public function inverseMod($a, $m)
    {
        $func = __METHOD__;
        $args = func_get_args();

        return call_user_func(
            array(
            $this,
            'call',
            ),
            $func,
            $args
        );
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\Math\GmpMathInterface::jacobi()
     */
    public function jacobi($a, $p)
    {
        $func = __METHOD__;
        $args = func_get_args();

        return call_user_func(
            array(
            $this,
            'call',
            ),
            $func,
            $args
        );
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\Math\GmpMathInterface::intToString()
     */
    public function intToFixedSizeString($x, int $byteSize)
    {
        $func = __METHOD__;
        $args = func_get_args();

        return call_user_func(
            array(
                $this,
                'call',
            ),
            $func,
            $args
        );
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\Math\GmpMathInterface::intToString()
     */
    public function intToString($x)
    {
        $func = __METHOD__;
        $args = func_get_args();

        return call_user_func(
            array(
            $this,
            'call',
            ),
            $func,
            $args
        );
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\Math\GmpMathInterface::stringToInt()
     */
    public function stringToInt(string $s)
    {
        $func = __METHOD__;
        $args = func_get_args();

        return call_user_func(
            array(
            $this,
            'call',
            ),
            $func,
            $args
        );
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\Math\GmpMathInterface::digestInteger()
     */
    public function digestInteger($m)
    {
        $func = __METHOD__;
        $args = func_get_args();

        return call_user_func(
            array(
            $this,
            'call',
            ),
            $func,
            $args
        );
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\Math\GmpMathInterface::gcd2()
     */
    public function gcd2($a, $m)
    {
        $func = __METHOD__;
        $args = func_get_args();

        return call_user_func(
            array(
            $this,
            'call',
            ),
            $func,
            $args
        );
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\Math\GmpMathInterface::rightShift()
     */
    public function rightShift($number, int $positions)
    {
        $func = __METHOD__;
        $args = func_get_args();

        return call_user_func(
            array(
            $this,
            'call',
            ),
            $func,
            $args
        );
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\Math\GmpMathInterface::leftShift()
     */
    public function leftShift($number, int $positions)
    {
        $func = __METHOD__;
        $args = func_get_args();

        return call_user_func(
            array(
            $this,
            'call',
            ),
            $func,
            $args
        );
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\Math\GmpMathInterface::bitwiseXor()
     */
    public function bitwiseXor($first, $other)
    {
        $func = __METHOD__;
        $args = func_get_args();

        return call_user_func(
            array(
            $this,
            'call'
            ),
            $func,
            $args
        );
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\Math\GmpMathInterface::baseConvert()
     */
    public function baseConvert(string $value, int $fromBase, int $toBase)
    {
        $func = __METHOD__;
        $args = func_get_args();

        return call_user_func(
            array(
            $this,
            'call'
            ),
            $func,
            $args
        );
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\Math\GmpMathInterface::getEcMath()
     */
    public function getEcMath(GeneratorPoint $generatorPoint, $input)
    {
        $func = __METHOD__;
        $args = func_get_args();

        return call_user_func(
            array(
            $this,
            'call'
            ),
            $func,
            $args
        );
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\Math\GmpMathInterface::getModularArithmetic()
     */
    public function getModularArithmetic($modulus)
    {
        $func = __METHOD__;
        $args = func_get_args();

        return call_user_func(
            array(
                $this,
                'call'
            ),
            $func,
            $args
        );
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\Math\GmpMathInterface::getNumberTheory()
     */
    public function getNumberTheory()
    {
        $func = __METHOD__;
        $args = func_get_args();

        return call_user_func(
            array(
            $this,
            'call'
            ),
            $func,
            $args
        );
    }
}
