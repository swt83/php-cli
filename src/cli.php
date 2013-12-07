<?php

namespace Travis;

class CLI
{
    /**
     * Check for valid arguments compared to input rules.
     *
     * @param   array   $args
     * @param   array   $rules
     * @return  void
     */
    public static function capture($args, $rules)
    {
        // catch help...
        if (isset($args[0])) {
            if ($args[0] == 'help') {
                static::error('Rules:');
                static::write(print_r($rules, true));
                die();
            }
        }

        // make clean array...
        $clean = array();
        $params = array_keys($rules);
        foreach ($params as $key => $value) {
            $clean[$value] = isset($args[$key]) ? $args[$key] : null;
        }
        $args = $clean;

        // amend rules (remove blank rules)...
        $original_rules = $rules;
        foreach ($rules as $key => $value) {
            if ($value == '' or $value == null) {
                unset($rules[$key]);
            }
        }

        // validate input
        $validation = Validator::make($args, $rules);
        if ($validation->fails()) {
            // cleanup messages...
            $errors = array();
            foreach ($validation->errors->messages as $msg) {
                $errors[] = $msg[0];
            }

            // print notice
            static::error('Rules:');
            static::write(print_r($original_rules, true));
            static::error('Errors:');
            static::write(print_r($errors, true));
            static::fatal('Script could not continue.');
        }

        // fix null values...
        foreach ($args as $key => $value) {
            $args[$key] = $value == 'null' ? null : $value;
        }

        // return
        return $args;
    }

    /**
     * Print output to terminal.
     *
     * @param   string  $string
     * @param   bool    $sameline
     * @return  void
     */
    public static function write($string, $sameline = false)
    {
        if ($sameline == true) {
            // overwrite current line
            $string = "\r"."\033"."[K".$string;

            // WARNING:
            // When writing on sameline ("spinning"),
            // don't forget to add a final CLI::newline()
            // after you're done to get a final carriage
            // return for future outputs.
        } else {
            // add newline to end
            $string .= "\n";
        }

        // output to terminal
        if (Request::cli()) fwrite(STDOUT, $string);
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
        $string = '| ';
        $ticks = 49;
        $progress = round($ticks * $percentage / 100);
        for ($i = 0; $i <= $progress; $i++) {
            $string .= '#';
        }
        for ($i = 0; $i <= $ticks - $progress; $i++) {
            $string .= ' ';
        }
        $string .= '| '.number_format($percentage, 2).'%';

        // alias
        static::write($string, true);
    }

    /**
     * Close the progress spinner.
     *
     * @return  void
     */
    public static function progress_complete()
    {
        return static::progress(1, 1).static::newline();
    }

    /**
     * Print error to terminal.
     *
     * @param   string  $string
     * @return  void
     */
    public static function error($string)
    {
        // error output w/ red color
        if (Request::cli()) fwrite(STDERR, "\033[1;31m".$string."\033[0m"."\n");
    }

    /**
     * Print fata error to terminal.
     *
     * @param   string  $string
     * @return  void
     */
    public static function fatal($string)
    {
        static::error($string);
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
        if (Request::cli()) echo str_repeat("\x07", $loops);
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
        if (is_numeric($seconds)) {
            // for each second...
            for ($i = $seconds; $i > 0; $i--) {
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
        for ($i = 0; $i < $num; $i++) {
            // output blank
            static::write('');
        }
    }

    /**
     * Return a string formatted into number of cells.
     *
     * @param   string  $str
     * @param   int     $len
     * @return  void
     */
    public static function block($str, $len)
    {
        $size = strlen($str);
        if ($len < $size) {
            return substr($str, 0, $len);
        }

        $remainder = $len - $size;

        $new = '';
        for ($i = 1; $i <= $remainder; $i++) {
            $new .= ' ';
        }
        $new .= $str;

        return $new;
    }
}