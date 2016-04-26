<?php

namespace libs;

/**
 * Class JSONProcess
 * @package libs
 *
 * Functions to process JSON requests or data
 */
class JSONProcess {

    /**
     * Output data as JSON, changing the header content-type
     * @param $data Data to be output
     */
    public static function returnJsonOutput($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
    }
}