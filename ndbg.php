<?php

/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * @package     php-ndbg
 * @copyright   2021 Nikita Podvirnyy (Observer KRypt0n_)
 * @license     GNU GPL-3.0 <https://www.gnu.org/licenses/gpl-3.0.html>
 * @author      Nikita Podvirnyy (Observer KRypt0n_)
 * 
 * Contacts:
 *
 * Email: <suimin.tu.mu.ga.mi@gmail.com>
 * GitHub: https://github.com/KRypt0nn
 * VK:     https://vk.com/technomindlp
 * 
 */

namespace Ndbg;

class Debugger
{
    /**
     * Apply debug code
     * 
     * @param string $code - original PHP code you want to debug
     * @param string $debug_file - path to file your debuggable code will include to get debug callback
     * 
     * @return string - return debuggable code
     */
    public static function apply ($code, $debug_file)
    {
        $applied = '';
        $tokens = token_get_all ($code);
        $unique_name = self::getUniqueName ();

        $function_entry = 0;

        for ($i = 0, $l = sizeof ($tokens); $i < $l; ++$i)
        {
            if ($tokens[$i][0] == T_NAMESPACE)
            {
                for (++$i; $i < $l; ++$i)
                    if ($tokens[$i] == ';')
                    {
                        $function_entry = $i;

                        break;
                    }

                break;
            }
        }

        for ($i = 0; $i < $l; ++$i)
        {
            $token = $tokens[$i];

            if (!is_array ($token))
                $applied .= $token;

            # Skipping use statement in functions definitions
            elseif ($token[0] == T_USE)
            {
                $applied .= $token[1];

                while ($tokens[$i++] != '(')
                    $applied .= !is_array ($tokens[$i]) ? $tokens[$i] : $tokens[$i][1];

                $brackets = 1;

                while ($brackets > 0)
                {
                    if ($tokens[$i] == ')')
                        --$brackets;

                    elseif ($tokens[$i] == '(')
                        ++$brackets;

                    $applied .= !is_array ($tokens[$i]) ? $tokens[$i] : $tokens[$i][1];

                    ++$i;
                }
            }

            elseif ($token[0] == T_VARIABLE)
                $applied .= '${$GLOBALS[\''. $unique_name .'\'][0](\''. substr ($token[1], 1) .'\',isset($GLOBALS[\''. $unique_name .'\'][1][$_'. $unique_name .'=__FUNCTION__.\'_\'.__CLASS__.\'_\'.__METHOD__])&&isset(${$GLOBALS[\''. $unique_name .'\'][1][$_'. $unique_name .']})?${$GLOBALS[\''. $unique_name .'\'][1][$_'. $unique_name .']}:\''. $unique_name .'\',array(__LINE__,__FILE__,__FUNCTION__,__CLASS__,__METHOD__,__NAMESPACE__))}';

            else $applied .= $token[1];

            if ($i == $function_entry)
                $applied .= "\r\n\r\n". '$_'. $unique_name .'=require \''. $debug_file .'\';$GLOBALS[\''. $unique_name .'\']=array(function($name,$prev_value,$consts){$GLOBALS[\''. $unique_name .'\'][2](isset($GLOBALS[\''. $unique_name .'\'][1][$n=$consts[2].\'_\'.$consts[3].\'_\'.$consts[4]])?$GLOBALS[\''. $unique_name .'\'][1][$n]:\''. $unique_name .'\',$prev_value,array_merge(array(\'globals\'=>$GLOBALS),array_combine(array(\'line\',\'file\',\'function\',\'class\',\'method\',\'namespace\'),array_map(function($i){return $i!=\'\'?$i:null;},$consts))));$GLOBALS[\''. $unique_name .'\'][1][$consts[2].\'_\'.$consts[3].\'_\'.$consts[4]]=$name;return $name;},array(),$_'. $unique_name .'(\''. $unique_name .'\',array_merge(array(\'globals\'=>$GLOBALS),array_combine(array(\'line\',\'file\',\'function\',\'class\',\'method\',\'namespace\'),array_map(function($i){return $i!=\'\'?$i:null;},array(__LINE__,__FILE__,__FUNCTION__,__CLASS__,__METHOD__,__NAMESPACE__))))));$_'. $unique_name .'=\''. $unique_name .'\';';
        }

        return $applied;
    }

    protected static function getUniqueName ($len = 8)
    {
        $alphabet = array_merge (range ('a', 'z'), range ('A', 'Z'));
        $name = '';

        for ($i = 0; $i < $len; ++$i)
            $name .= $alphabet[rand (0, 51)];

        return $name;
    }
}
