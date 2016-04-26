<?php

namespace libs;

class Validations {

    /**
     * Validate a date with format yyyy-mm-dd
     * @param $dateItem Date to be validated
     * @return bool Whether the validation passes or not
     */
    public static function validateDate($dateItem) {
        $validDate = true;
        if ($dateItem != '') {
            $dateValues = explode('-', trim($dateItem));
            $validDate = (count($dateValues) == 3) ?
                checkdate($dateValues[1], $dateValues[2], $dateValues[0]) :
                false;
        }
        return $validDate;
    }
}