<?php
namespace framework;

/**
 * Class BaseError
 * @package framework
 *
 * Error handling and displaying in screen
 */
class BaseError {

    /**
     * Throw error ID and custom message
     * @param $errorCode
     * @param $errorMessage
     */
    public static function throwMessage($errorCode, $errorMessage) {
        $layoutPathFile = 'views/error/' . $errorCode. '.php';
        if (is_file(realpath(__DIR__ . '/../' . $layoutPathFile))) {
            http_response_code($errorCode);
            include_once('../' . $layoutPathFile);
            die();
        }
    }
}