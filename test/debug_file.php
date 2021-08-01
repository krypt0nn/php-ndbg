<?php

return function ($temp_name, $params)
{
    echo 'Ran file '. $params['file'] . PHP_EOL;
    
    return function ($variable, $value, $params) use ($temp_name)
    {
        if ($value == $temp_name)
            return;
        
        echo "line {$params['line']} | $$variable = $value\n";
    };
};
