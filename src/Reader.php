<?php

namespace PhpFp\Reader;

/**
 * An OO implementation of the Reader monad in PHP.
 */
class Reader
{
    /**
     * This Reader's computation.
     * @var callable
     */
    private $action = null;

    /**
     * Applicative constructor.
     * @param mixed $x The value to wrap.
     * @return Reader The wrapped value.
     */
    public static function of($x) : Reader
    {
        return new Reader(
            function ($_) use ($x)
            {
                return $x;
            }
        );
    }

    /**
     * Identity reader, used to access the environment in chains. See
     * the documentation for a more comprehensive example.
     * @return Reader
     */
    public static function ask() : Reader
    {
        return new Reader(
            function ($x)
            {
                return $x;
            }
        );
    }

    /**
     * Standard constructor for Reader types. The passed function
     * must take one argument - the environment for the Reader.
     * @param callable $f The Reader action.
     */
    public function __construct(callable $f)
    {
        $this->action = $f;
    }

    /**
     * PHP implementation of Haskell Reader's >>=.
     * @param callable $f a -> Reader r b | Reader r a -> Reader r b
     * @return Reader Reader r b
     */
    public function chain(callable $f) : Reader
    {
        return new Reader(
            function ($env) use ($f)
            {
                return $f($this->run($env))->run($env);
            }
        );
    }

    /**
     * Standard functor mapping, derived from chain.
     * @param callable $f Transformation for the inner value.
     * @return Reader The transformed Reader.
     */
    public function map(callable $f) : Reader
    {
        return $this->chain(
            function ($a) use ($f)
            {
                return Reader::of($f($a));
            }
        );
    }

    /**
     * Application, derived from chain.
     * @param Reader $x The wrapped argument.
     * @return Reader The wrapped result.
     */
    public function ap(Reader $x) : Reader
    {
        return $this->chain(
            function ($f) use ($x)
            {
                return $x->map($f);
            }
        );
    }

    /**
     * Execute the Reader computation.
     * @return mixed The computation result.
     */
    public function run($environment)
    {
        return call_user_func(
            $this->action,
            $environment
        );
    }
}
