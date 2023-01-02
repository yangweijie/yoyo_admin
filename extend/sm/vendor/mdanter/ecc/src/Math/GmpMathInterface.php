<?php

namespace Mdanter\Ecc\Math;

interface GmpMathInterface
{
    /**
     * Compares two numbers
     *
     * @param \GMP $first
     * @param \GMP $other
     * @return int        less than 0 if first is less than second, 0 if equal, greater than 0 if greater than.
     */
    public function cmp($first, $other);

    /**
     * @param \GMP $first
     * @param \GMP $other
     * @return bool
     */
    public function equals($first, $other);

    /**
     * Returns the remainder of a division
     *
     * @param \GMP $number
     * @param \GMP $modulus
     * @return \GMP
     */
    public function mod($number, $modulus);

    /**
     * Adds two numbers
     *
     * @param \GMP $augend
     * @param \GMP $addend
     * @return \GMP
     */
    public function add($augend, $addend);

    /**
     * Substract one number from another
     *
     * @param \GMP $minuend
     * @param \GMP $subtrahend
     * @return \GMP
     */
    public function sub($minuend, $subtrahend);

    /**
     * Multiplies a number by another.
     *
     * @param \GMP $multiplier
     * @param \GMP $multiplicand
     * @return \GMP
     */
    public function mul($multiplier, $multiplicand);

    /**
     * Divides a number by another.
     *
     * @param   $dividend
     * @param   $divisor
     * @return
     */
    public function div($dividend, $divisor);

    /**
     * Raises a number to a power.
     *
     * @param   $base     The number to raise.
     * @param int $exponent The power to raise the number to.
     * @return
     */
    public function pow($base, int $exponent);

    /**
     * Performs a logical AND between two values.
     *
     * @param   $first
     * @param   $other
     * @return
     */
    public function bitwiseAnd($first, $other);

    /**
     * Performs a logical XOR between two values.
     *
     * @param   $first
     * @param   $other
     * @return
     */
    public function bitwiseXor($first, $other);

    /**
     * Shifts bits to the right
     * @param         $number    Number to shift
     * @param int $positions Number of positions to shift
     * @return
     */
    public function rightShift($number, int $positions);

    /**
     * Shifts bits to the left
     * @param        $number    Number to shift
     * @param int $positions Number of positions to shift
     * @return
     */
    public function leftShift($number, int $positions);

    /**
     * Returns the string representation of a returned value.
     *
     * @param  $value
     * @return string
     */
    public function toString($value);

    /**
     * Converts an hexadecimal string to decimal.
     *
     * @param string $hexString
     * @return int|string
     */
    public function hexDec(string $hexString);

    /**
     * Converts a decimal string to hexadecimal.
     *
     * @param int|string $decString
     * @return string
     */
    public function decHex(string $decString);

    /**
     * Calculates the modular exponent of a number.
     *
     * @param  $base
     * @param  $exponent
     * @param  $modulus
     * @return
     */
    public function powmod($base, $exponent, $modulus);

    /**
     * Checks whether a number is a prime.
     *
     * @param   $n
     * @return boolean
     */
    public function isPrime($n);

    /**
     * Gets the next known prime that is greater than a given prime.
     *
     * @param   $currentPrime
     * @return
     */
    public function nextPrime($currentPrime);

    /**
     * @param  $a
     * @param  $m
     * @return
     */
    public function inverseMod($a, $m);

    /**
     * @param  $a
     * @param  $p
     * @return int
     */
    public function jacobi($a, $p);

    /**
     * @param   $x
     * @return string
     */
    public function intToString($x);

    /**
     * @param  $x
     * @param int $byteSize
     * @return string
     */
    public function intToFixedSizeString($x, int $byteSize);

    /**
     *
     * @param int|string $s
     * @return
     */
    public function stringToInt(string $s);

    /**
     *
     * @param   $m
     * @return
     */
    public function digestInteger($m);

    /**
     * @param   $a
     * @param   $m
     * @return
     */
    public function gcd2($a, $m);

    /**
     * @param string $value
     * @param int $fromBase
     * @param int $toBase
     * @return string
     */
    public function baseConvert(string $value, int $fromBase, int $toBase);

    /**
     * @return NumberTheory
     */
    public function getNumberTheory();

    /**
     * @param  $modulus
     * @return ModularArithmetic
     */
    public function getModularArithmetic($modulus);
}
