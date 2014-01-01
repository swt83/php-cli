# CLI

A PHP library for sending output to the command line interface.

## Install

Normal install via Composer.

## Methods

* ``CLI::write($string);`` Print output.
* ``CLI::spin($string);`` Print output on the same line.
* ``CLI::progress($count, $total);`` Print percentage complete on the same line.
* ``CLI::error($string);`` Print a red error.
* ``CLI::beep($loops);`` Emit a beep a given number of times.
* ``CLI::countdown($seconds);`` Print a countdown, on the same line.
* ``CLI::newline($num);`` Print blank lines.

## "Spinning" and "Progress"

When using ``CLI::spin('Importing file XYZ...');`` or ``CLI::progress($count, $total)``, you will be sending output to the same line of the terminal.  Just remember to add a final ``CLI::newline();`` or ``CLI::progress_complete();`` after your loop to give future output a clean line to work with.

## Limitations

- My understanding is this class won't work at all in Windows.