<?php

// https://github.com/alexcorvi/heic2any/

function isExecEnabled() {
    // Prüfen, ob die Funktion überhaupt existiert
    if (!function_exists('exec')) {
        return false;
    }

    // Prüfen, ob exec in der php.ini über disable_functions blockiert wird
    $disabled = ini_get('disable_functions');
    if ($disabled) {
        $disabled_array = explode(',', $disabled);
        $disabled_array = array_map('trim', $disabled_array);
        if (in_array('exec', $disabled_array)) {
            return false;
        }
    }

    // Falls keine Sperre vorliegt, ist es möglich
    return true;
}

if (isExecEnabled()) {
    echo "exec() ist verfügbar und kann genutzt werden.";
} else {
    echo "exec() ist deaktiviert oder nicht verfügbar.";
}

// ++++++++++++++++ ext.php +++++++++++++++++++++
/**
 *
 * Image Upload extension for phpBB.
 *
 * @copyright (c) 2026 imcger
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace imcger\imgupload;

class ext extends \phpbb\extension\base
{
    /**
     * Prüft, ob die Erweiterung aktiviert werden kann.
     *
     * Die eingebundene Library "maestroerror/php-heic-to-jpg" führt
     * intern eine Go-Binary aus. Dafür müssen exec()/proc_open()
     * verfügbar sein und die Mindest-PHP-Version muss stimmen.
     *
     * @return bool
     */
    public function is_enableable()
    {
        // Von der Library vorausgesetzte PHP-Version
        if (version_compare(PHP_VERSION, '7.4.0', '<'))
        {
            return false;
        }

        // exec()/proc_open() dürfen nicht per php.ini deaktiviert sein,
        // sonst kann die Konvertierungs-Binary nicht ausgeführt werden
        $disabled = array_map('trim', explode(',', (string) ini_get('disable_functions')));

        if (in_array('proc_open', $disabled, true) || in_array('exec', $disabled, true) || in_array('shell_exec', $disabled, true))
        {
            return false;
        }

        // Composer-Autoloader der Erweiterung muss vorhanden sein
        if (!is_readable(__DIR__ . '/vendor/autoload.php'))
        {
            return false;
        }

        return true;
    }
}
