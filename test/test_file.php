<?php

namespace aboba;

$a = 10;
$b = 0;

for ($i = 0; $i < $a; ++$i)
    $b += $i;

(function () use ($b)
{
    echo $b;
})();
