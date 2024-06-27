<?php

namespace Travis;

class CLI
{
    /**
     * Color codes for text.
     *
     * @var     array
     */
    protected static $colors = array(
        'black'         => '0;30',
        'dark_gray'     => '1;30',
        'blue'          => '0;34',
        'dark_blue'     => '1;34',
        'light_blue'    => '1;34',
        'green'         => '0;32',
        'light_green'   => '1;32',
        'cyan'          => '0;36',
        'light_cyan'    => '1;36',
        'red'           => '0;31',
        'light_red'     => '1;31',
        'purple'        => '0;35',
        'light_purple'  => '1;35',
        'light_yellow'  => '0;33',
        'yellow'        => '1;33',
        'light_gray'    => '0;37',
        'white'         => '1;37',
    );

    /**
     * Print output to terminal.
     *
     * @param   string  $string
     * @param   bool    $sameline
     * @return  void
     */
    public static function write($string, $sameline = false)
    {
        // if sameline and NOT windows...
        if ($sameline and !static::is_windows())
        {
            // overwrite current line
            $string = "\r"."\033"."[K".$string;

            // WARNING:
            // When writing on sameline ("spinning"),
            // don't forget to add a final CLI::newline()
            // after you're done to get a new line for
            // future outputs.
        }
        else
        {
            // add newline to end
            $string .= "\n";
        }

        // if is command line...
        if (PHP_SAPI === 'cli')
        {
            // output to shell
            fwrite(STDOUT, $string);
        }

        // unset
        unset($string, $sameline);
    }

    /**
     * Print output to terminal, same line.
     *
     * @param   string  $string
     * @return  void
     */
    public static function spin($string)
    {
        // alias
        static::write($string, true);
    }

    /**
     * Print percentage progress to terminal, same line.
     *
     * @param   string  $string
     * @return  void
     */
    public static function progress($num, $total)
    {
        // calculate
        $percentage = round((100 * $num / $total), 2);
        $string = '[ ';
        $ticks = 49;
        $progress = round($ticks * $percentage / 100);
        for ($i = 0; $i <= $progress; $i++)
        {
            $string .= '=';
        }
        for ($i = 0; $i <= $ticks - $progress; $i++)
        {
            $string .= ' ';
        }
        $string .= '] '.number_format($percentage, 2).'%';

        // write
        static::write($string, true);

        // unset
        unset($num, $total, $percentage, $string, $ticks, $progress, $i);
    }

    /**
     * Close the progress spinner.
     *
     * @return  void
     */
    public static function progress_complete()
    {
        // write
        return static::progress(1, 1).static::newline();
    }

    /**
     * Print info to terminal.
     *
     * @param   string  $string
     * @return  void
     */
    public static function info($string)
    {
        // make string red
        $string = static::colorize($string, 'green');

        // write
        static::write($string);

        // unset
        unset($string);
    }

    /**
     * Print error to terminal.
     *
     * @param   string  $string
     * @return  void
     */
    public static function error($string)
    {
        // make string red
        $string = static::colorize($string, 'red');

        // write
        static::write($string);

        // unset
        unset($string);
    }

    /**
     * Print fata error to terminal.
     *
     * @param   string  $string
     * @return  void
     */
    public static function fatal($string)
    {
        // write
        static::error($string);

        // throw error
        trigger_error($string);

        // die
        die();
    }

    /**
     * Emit beep from terminal.
     *
     * @param   int     $loops
     * @return  void
     */
    public static function beep($loops = 1)
    {
        // repeating beep
        echo str_repeat("\x07", $loops);
    }

    /**
     * Print sameline countdown to terminal.
     *
     * @param   int     $seconds
     * @return  void
     */
    public static function countdown($seconds)
    {
        // if seconds is a number...
        if (is_numeric($seconds))
        {
            // for each second...
            for ($i = $seconds; $i > 0; $i--)
            {
                // write notice
                static::spin('Waiting '.$i.' seconds...');

                // sleep a second
                sleep(1);
            }

            // escape spinner
            static::newline();
        }
    }

    /**
     * Print blank lines to terminal.
     *
     * @param   int     $num
     * @return  void
     */
    public static function newline($num = 1)
    {
        // foreach each newline
        for ($i = 0; $i < $num; $i++)
        {
            // write blank
            static::write('');
        }

        // unset
        unset($num, $i);
    }

    /**
     * Return a string formatted into number of cells.
     *
     * @param   string  $str
     * @param   int     $len
     * @param   boolean $dots
     * @return  void
     */
    public static function block($str, $len, $align = 'right', $dots = false)
    {
        //"\033[1;31mHello\033[0m \033[1;32mWorld\033[0m! ✓";
        // Remove color codes like "\033[1;31m"
        $string_without_color = preg_replace('/\033\[\d+(?:;\d+)*m/', '', $str);

        // Remove reset code "\033[0m"
        $string_without_color = preg_replace('/\033\[0m/', '', $string_without_color);

        $size = mb_strlen($string_without_color, 'UTF-8');
        if ($len < $size)
        {
            return substr($string_without_color, 0, $len);
        }

        $remainder = $len - $size;

        $new = '';
        for ($i = 1; $i <= $remainder; $i++)
        {
            $new .= $dots ? '.' : ' ';
        }

        if ($align === 'right')
        {
            $new .= $dots ? '. '.$str : $str;
        }
        else
        {
            $old = $new;
            $new = $dots ? $str.' .'.$old : $str.$old;
        }

        // return
        return $new;
    }

    /**
     * Return string w/ color added.
     *
     * @param   string  $string
     * @return  string
     */
    public static function colorize($string, $color)
    {
        // catch windows...
        if (static::is_windows())
        {
            // bail
            return $string;
        }

        // catch errors...
        if (!array_key_exists($color, static::$colors))
        {
            trigger_error('Unable to find that color.');
        }

        // return
        return "\033[".static::$colors[$color]."m".$string."\033[0m";
    }

    /**
     * Get input from the user.
     *
     * @param   string  $question
     * @param   int     $block
     * @return  string
     */
    public static function input($question, $block = null)
    {
        // if block...
        if ($block) $question = static::block($question, $block, 'left', true);

        // print question
        static::spin($question.': ', 'white');

        // get input
        $input = fgets(STDIN);

        // return
        return trim($input);
    }

    /**
     * Get confirmation from the user.
     *
     * @param   string  $question
     * @param   int     $block
     * @return  boolean
     */
    public static function confirm($question, $block = null)
    {
        // get input
        $input = static::input($question.' [Y/N]', $block);

        // return
        return in_array($input, array('Y', 'Yes', 'y', 'yes', '1'));
    }

    /**
     * Return if shell is on Windows.
     *
     * @return  boolean
     */
    protected static function is_windows()
    {
        return 'win' === strtolower(substr(php_uname("s"), 0, 3));
    }
}