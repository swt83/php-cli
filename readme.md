# CLI

A PHP library for sending output to the Command Line Interface.

## Install

Some methods are designed for use in Laravel.

### Provider

Register your service provider in ``app/config/app.php``:

```php
'Travis\CLI\Provider'
```

You may also wish to add an alias to remove the namespace:

```php
'CLI' => 'Travis\CLI'
```

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