<?php

namespace libs;


class CreditCardValidations {

    /**
     * Validate a credit card number.
     * In principle, the Luhn algorithm must be used to verify if a credit card number
     * is valid.  In this example is only used the validation for length 15 or 16
     * @param $creditCard Credit card number to check
     * @return bool Whether the credit card is valid or not
     */
    public static function validateCreditCardNumber($creditCard) {
        $creditCard = trim($creditCard);
        return preg_match('/^[0-9]{15,16}$/', $creditCard);
    }

    /**
     * Validate a credit card number CVV.
     * In this function is only used the validation for length 3 or 4
     * @param $creditCard Credit card number to check
     * @return bool Whether the credit card is valid or not
     */
    public static function validateCreditCardCVV($creditCardCVV) {
        $creditCardCVV = trim($creditCardCVV);
        return preg_match('/^[0-9]{3,4}$/', $creditCardCVV);
    }

    /**
     * Validate if a month is valid
     * @param $monthNumber Month number
     * @return bool Whether is valid or not
     */
    public static function validateMonthNumber($monthNumber) {
        if(is_numeric($monthNumber)) {
            $monthNumber = intval($monthNumber);
            return ($monthNumber >=1 && $monthNumber <= 12);
        }
        return false;
    }

    /**
     * Validates a credit card expiration year number is in range YEAR - (YEAR+7)
     * @param $yearNumber Year number value
     * @return bool Whether the credit card number is valid
     */
    public static function validateCreditCardYear($yearNumber) {
        $currentYearNumber = date('Y');
        if(is_numeric($yearNumber)) {
            $yearNumber = intval($yearNumber);
            return ($yearNumber >=$currentYearNumber && $yearNumber <= ($currentYearNumber+7));
        }
        return false;

    }
}