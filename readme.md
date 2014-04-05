# CLI

A PHP library for working with the command line interface.

## Install

Normal install via Composer.

## Usage

```php
use Travis\CLI;

// outputs
CLI::write($string); // print output to shell
CLI::spin($string); // print output on the same line
CLI::newline($num); // print blank lines
CLI::progress($count, $total); // print percentage complete, on the same line
CLI::progress_complete(); // print final percentage and line return
CLI::info($string); // print a green message
CLI::error($string); // print a red message
CLI::fatal($string); // print a red message and kill the script
CLI::beep($loops); // emit a beep a number of times
CLI::countdown($seconds); // print a countdown, on the same line

// formatting
$string = CLI::colorize($string, $color); // format a string to have a color
CLI::write($string);
$string = CLI::block($string, 20); // format content to be right aligned in block
$string = CLI::block($string, 20, 'left'); // left aligned in block
$string = CLI::block($string, 20, 'left', true); // left aligned in block w/ dots
CLI::write($string);

// inputs
$var = CLI::input($question); // get input from the user
$vars = CLI::inputs($array_of_questions); // get array of inputs from the user
$vars = CLI::inputs($array_of_questions, true); // get array of inputs w/ block alignments
$var = CLI::confirm($question); // get boolean response
```