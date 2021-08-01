<h1 align="center">🚀 php-ndbg</h1>

**PHP Native Debug** is a library that gives you ability to generate native debugging code from original one

## Installation

```
composer require krypt0nn/php-ndbg
```

## Principle of work

The main idea is to add callbacks everywhere in code where the variables changes its values. For example

Original one

```php
<?php

$var = 10;
```

Debuggable one

```php
<?php

$var = 10;
var_changed ('var', 10);
```

And define function `var_changed` somewhere above, like

```php
<?php

function var_changed ($name, $value)
{
    echo "$name = $value\n";
}
```

So we'll see if some variables in code will change

## Usage

An usage example you can find in `test` directory. There we have `test_file.php` that is the file we want to debug. File `test.php` will call `Ndbg\Debugger::apply` method for this file and save it as `test_file_applied.php` with `debug_file.php` as the file with a debugging callback

This library has only one method: `Ndbg\Debugger::apply(string $code, string $debug_file): string`. `$code` is the code you want to debug, `$debug_file` is debug file, of course, and this function will return debuggable code

Debug file is a file with this structure:

```php
<?php

return function ($temp_name, $params)
{
    // this callback will be called when
    // debuggable script will start

    return function ($variable, $value, $params) use ($temp_name)
    {
        // this callback will be called every time
        // when some variable will be changed
    };
};
```

`$temp_name` is a parameter that is containing, as you can guess, temporary name which is using as an "empty" variables value and `$GLOBALS` index for debugger data storing

`$variable` and `$value` is parameters that tell you the name of the variable and its new value

And `$params` contains array of some information about place where this callback was called

```php
[
    'globals'   => $GLOBALS,
    'line'      => __LINE__,
    'file'      => __FILE__,
    'function ' => __FUNCTION__,
    'class'     => __CLASS__,
    'method'    => __METHOD__,
    'namespace' => __NAMESPACE__
]
```

And if some constant will contain empty string as a value (like callback was called from a function without class and there `__CLASS__` will be `''` (an empty string)) - it will be replaced as null value

So an example debug file will looks like

```php
<?php

return function ($temp_name, $params)
{
    echo 'Ran file '. $params['file'] . PHP_EOL;

    return function ($variable, $value, $params) use ($temp_name)
    {
        if ($params['function'] !== null)
            echo 'Ran function '. $params['function'] .
                 ' at line '. $params['line'] . PHP_EOL;
    };
};
```

<br>

Author: [Nikita Podvirnyy](https://vk.com/technomindlp)