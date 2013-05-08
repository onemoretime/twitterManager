<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Frameset//EN"
         "http://www.w3.org/TR/REC-html40/frameset.dtd">

<?php
$documentRoot = null;

if (isset($_SERVER['DOCUMENT_ROOT'])) {
    $documentRoot = $_SERVER['DOCUMENT_ROOT'];
   
    if (strstr($documentRoot, '/') || strstr($documentRoot, '\\')) {
        if (strstr($documentRoot, '/')) {
            $documentRoot = str_replace('/', DIRECTORY_SEPARATOR, $documentRoot);
        }
        elseif (strstr($documentRoot, '\\')) {
            $documentRoot = str_replace('\\', DIRECTORY_SEPARATOR, $documentRoot);
        }
    }
   
    if (preg_match('/[^\\/]{1}\\[^\\/]{1}/', $documentRoot)) {
        $documentRoot = preg_replace('/([^\\/]{1})\\([^\\/]{1})/', '\\1DIR_SEP\\2', $documentRoot);
        $documentRoot = str_replace('DIR_SEP', '\\\\', $documentRoot);
    }
}
else {
    /**
     * I usually store this file in the Includes folder at the root of my
     * virtual host. This can be changed to wherever you store this file.
     *
     * Example:
     * If you store this file in the Application/Settings/DocRoot folder at the
     * base of your site, you would change this array to include each of those
     * folders.
     *
     * <code>
     * $directories = array(
     *     'Application',
     *     'Settings',
     *     'DocRoot'
     * );
     * </code>
     */
    $directories = array(
        'Includes'
    );
   
    if (defined('__DIR__')) {
        $currentDirectory = __DIR__;
    }
    else {
        $currentDirectory = dirname(__FILE__);
    }
   
    $currentDirectory = rtrim($currentDirectory, DIRECTORY_SEPARATOR);
    $currentDirectory = $currentDirectory . DIRECTORY_SEPARATOR;
   
    foreach ($directories as $directory) {
        $currentDirectory = str_replace(
            DIRECTORY_SEPARATOR . $directory . DIRECTORY_SEPARATOR,
            DIRECTORY_SEPARATOR,
            $currentDirectory
        );
    }
   
    $currentDirectory = rtrim($currentDirectory, DIRECTORY_SEPARATOR);
}

define('SERVER_DOC_ROOT', $documentRoot);
?>