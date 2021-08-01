<?php

require __DIR__ .'\\..\\ndbg.php';

use Ndbg\Debugger;

file_put_contents (__DIR__ .'\\test_file_applied.php', Debugger::apply (file_get_contents (__DIR__ .'\\test_file.php'), __DIR__ .'\\debug_file.php'));
