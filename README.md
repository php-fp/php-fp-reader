# The Reader Monad for PHP. [![Build Status](https://travis-ci.org/php-fp/php-fp-reader.svg?branch=master)](https://travis-ci.org/php-fp/php-fp-reader)

## Intro

Sometimes, when you're composing several functions into a pipeline, there will be some value that is required by functions throughout - perhaps it's a `Request` object while creating a response in a web app. Either way, it can become messy to have to pass this in as a parameter to the functions that need it, and nearly impossible if we don't yet know its value! This is where Reader comes in.

Reader allows you to build computations that depend on a value -- known as the _environment_ -- without specifying it. Then, when you wish to run the computation, you simply call `run($environment)`, and that value is available throughout the computation. You can think of this, if you really must, as type-safe dependency injection. The web server analogy is a good one: you can write your entire request lifecycle as a Reader computation, and then simply run it with each new request as a new environment! This might look something like this:

```php
<?php

function update($key, $value)
{
    return function ($xs) use ($key, $value)
    {
        return array_merge($xs, [$key => $value]);
    };
}

Reader::of([])
    ->map(update('status', 200)) // Add a response status.
    ->chain(
        function ($res) // Currently ['status' => 200]
        {
            return Reader::ask()->map(
                function ($req) use ($res)
                {
                    return update(
                        'message',
                        'This is ' + $req['path'],
                    ) ($response);
                }
            );
        }
    )

    // ['status' => 200, 'message' => 'This is /hello']
    ->run(['path' => '/hello']);
```

In this example, we can see that mapping over `Reader::ask()` gives us access to the environment that hasn't yet been specified, and so we can add it to your response object. Under the hood, this works similarly to IO: computations are queued, and `run` is what eventually triggers the process and evaluates them. So, use `map` when you only care about the Reader's value, and use `chain` when you also care about the Reader's environment. You could end up with some gorgeous request lifecycle handlers:

```php
<?php

return Reader::of([])
    ->chain($authenticate) // Requires the Request.
    ->map($addStatus) // Only cares about the response.
    ->map($addToken) // Doesn't care about the Request.
    ->chain($setContentType); // Checks requested type in the Request.
```

Pretty, huh? Neat pipelines that exactly describe the flow of data through your project.

## API

In the following type signatures, constructors and static functions are written as one would see in pure languages such as Haskell. The others contain a pipe, where the type before the pipe represents the type of the current Reader instance, and the type after the pipe represents the function.

### `of :: a -> Reader b a`

Applicative constructor for a Reader instance. There are two types involved with a Reader, but we only need to declare one of them at this point - the other can be inferred later with chain functions or `run`.

```php
<?php

use PhpFp\Reader\Reader;

assert(Reader::of(2)->run(null) == 2);
```

### `ask :: -> Reader b b`

The main bit of wizardry for the Reader monad: environment access. This is actually just a Reader-wrapped identity function, so it's actually not too difficult to work out how this wizardry works (the same rough pattern is used for `Writer::tell()`).

```php
<?php

use PhpFp\Reader\Reader;

$reader = Reader::of(2)->chain(
    function ($x)
    {
        return Reader::ask()->map(
            function ($y) use ($x)
            {
                return $x + $y;
            }
        );
    }
);

assert($reader->run(4) == 6);
```

This only has to be a function because of PHP restrictions: we can't add a class instance as a static property on a class.

### `chain :: Reader a c | (a -> Reader b c) -> Reader b c`

As with all monads, `chain` is where the wizardry is. The examples in `ask` and the introduction demonstrate the most common use case of this, but this can, of course, be used without convenience values:

```php
<?php

use PhpFp\Reader\Reader;

$reader = Reader::of(2)->chain(
    function ($x)
    {
        return Reader::of($x + 2);
    }
);

assert($reader->run(null) == 4);
```

### `map :: Reader a c | (a -> b)`

The standard functor map: all this does is transform the value within the Reader. Note (again) that Reader functions similarly to IO in that the mapping function won't actually be called until `run($env)` is invoked, but that shouldn't make a difference if you're writing purely functional code!

```php
<?php

use PhpFp\Reader\Reader;

$reader = Reader::of(2)->map(
    function ($x)
    {
        return $x * 22;
    }
);

assert($reader->run(null) == 44);
```

### `ap :: Reader (a -> b) c | Reader a c -> Reader b c`

Standard applicative function application. If you have a function wrapped in a Reader, and a parameter wrapped in a Reader, this is the function for you (Remember that your mapping function can always return a function!):

```php
<?php

use PhpFp\Reader\Reader;

$reader = Reader::of(
    function ($x)
    {
        return $x + 5;
    }
)->ap(Reader::of(2));

assert($reader->run(null) == 7);
```

### `run :: Reader a b | b -> a`

The method for forking the Reader monad. The type signature of this is interesting: essentially, a Reader is defined by a type, `a`, that depends on a type, `b`, which is also a good description of a function `b -> a`. Reader is, in many ways, just elaborate and flexible function composition, which is why it gets called the 'arrow monad' (feel free to Google this!):

```
<?php

use PhpFp\Reader\Reader;

assert(Reader::of('hello')->run(null) == 'hello');
```

## Contributing

The Reader monad probably won't require any great changes to the code base, but feel free to submit PRs if you have any ideas (I'm guessing they're probably implementations of more typeclasses, if anything...)

As with the other monads, you are always welcome to submit PRs to make the documentation clearer, as well as issues if some part of the documentation has confused you. This is a learning resource as much as a package!
