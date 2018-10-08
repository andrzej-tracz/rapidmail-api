<?php

namespace App\Infrastructure\Template\PhantomJS;

use Anam\PhantomMagick\Str;

class Converter extends \Anam\PhantomMagick\Converter
{
    /**
     * Initialize the Converter.
     *
     * @param string $source source of the data file
     */
    public function __construct($source = null)
    {
        parent::__construct($source);

        $this->initialize();
    }

    /**
     * Initialize the converter settings.
     */
    private function initialize()
    {
        self::$scripts['converter'] = dirname(__FILE__).'/app.js';
    }

    /**
     * Check phantomjs is installed or not.
     *
     * @param string $binary Binary location
     *
     * @return bool
     */
    public function verifyBinary($binary)
    {
        $uname = strtolower(php_uname());

        if (Str::contains($uname, 'darwin')) {
            if (!shell_exec(escapeshellcmd("command -v {$binary} >/dev/null 2>&1"))) {
                return false;
            }
        } elseif (Str::contains($uname, 'win')) {
            if (!shell_exec(escapeshellcmd("{$binary}"))) {
                return false;
            }
        } elseif (Str::contains($uname, 'linux') || Str::contains($uname, 'root')) {
            if (!shell_exec(escapeshellcmd("which {$binary}"))) {
                return false;
            }
        } else {
            throw new \RuntimeException('Unknown operating system.');
        }

        return true;
    }
}
