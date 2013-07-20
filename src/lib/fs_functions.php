<?php

/**
 * Determines if this script is running in a Windows OS.
 *
 * @var boolean
 */
define('FILE_SYSTEM_IS_WINDOWS', (false !== strpos(strtolower(PHP_OS), 'win')));

/**
 * Returns the canonical path for the given path.
 *
 * @param string $path The path to canonicalize.
 *
 * @return string The canonicalized path.
 */
function canonical_path($path)
{
    $canonical = array();
    $path = preg_split('/[\\\\\/]+/', $path);

    foreach ($path as $segment) {
        if ('.' == $segment) {
            continue;
        }

        if ('..' == $segment) {
            array_pop($canonical);
        } else {
            $canonical[] = $segment;
        }
    }

    return join(DIRECTORY_SEPARATOR, $canonical);
}

/**
 * Checks if the path is an absolute path.
 *
 * @param string $path The path to check.
 *
 * @return boolean TRUE if absolute, FALSE if not.
 */
function is_absolute_path($path)
{
    if (preg_match('/^([\\\\\/]|[a-zA-Z]\:[\\\\\/])/', $path)
        || (false !== filter_var($path, FILTER_VALIDATE_URL))) {
        return true;
    }

    return false;
}

/**
 * Checks if the path is marked as hidden.
 *
 * @param string $path The path to check.
 *
 * @return boolean TRUE if the path is hidden, FALSE if not. If the file
 *                 does not exist or its flags could not be retrieved, a
 *                 warning is triggered and NULL is returned.
 */
function is_hidden_path($path)
{
    if (false === file_exists($path)) {
        trigger_error(sprintf(
            'The path "%s" does not exist.',
            $path
        ), E_USER_WARNING);

        // @codeCoverageIgnoreStart
        return;
    }
    // @codeCoverageIgnoreEnd

    if (0 === strpos(basename($path), '.')) {
        return true;
    }

    if (FILE_SYSTEM_IS_WINDOWS) {
        exec('attrib ' . escapeshellarg($path), $result, $status);

        if (0 === $status) {
            $result = str_split(
                join(
                    '',
                    array_slice(
                        preg_split('/\s+/', trim($result[0])),
                        0,
                        -1
                    )
                )
            );

            return in_array('H', $result) ?: in_array('S', $result);
        } else {
            trigger_error(sprintf(
                'The attributes for path "%s" could not be retrieved.',
                $path
            ), E_USER_WARNING);

            return;
        }
    }

    return false;
}
