<?php
namespace framework;

/**
 * Class BaseEncryption
 * @package framework
 *
 * The following class is used to encrypt / decrypt values using the Rijndael algorithm and
 * the mcrypt function.
 *
 * Mcrypt packages must be installed prior to be used in PHP.
 * On Ubuntu Linux
 * Installation: $ sudo apt-get install php5-mcrypt
 * Activation: $ php5enmod mcrypt
 *
 * Restart Apache 2 after activate component.
 *
 * Installation notes: http://php.net/manual/en/mcrypt.installation.php
 *
 * ************************
 *
 * Use example:
 * $encrypt = new \framework\BaseEncryption();
 * $encryptionResult = $encrypt->encrypt('valueToEncrypt');
 * echo $encryptionResult['encryptedData']; //The encrypted value
 * echo $encryptionResult['initializationVector']; //The initialization vector
 *
 * $decryptedValue = $encrypt->decrypt($encryptionResult['encryptedData'], $encryptionResult['initializationVector']);
 * echo $decryptedValue; //Returns 'valueToEncrypt'
 *
 */
class BaseEncryption {

    //This constant defines the cipher mode, in this case is Rijndael (Raindoll) 256
    const MY_MCRYPT_CIPHER = MCRYPT_RIJNDAEL_256;

    //Modality of encryption: CBC
    const MY_MCRYPT_MODE = MCRYPT_MODE_CBC;

    /**
     * The following key is used as private key to encrypt all the information
     * This must be a random, recommended 32 bytes and it should be secured.  Without this key,
     * it is not possible to decrypt values.
     */
    const MY_MCRYPT_KEY_STRING = "1234567890-abcDEFGHUzyxwvutsrqpo";

    /**
     * Encrypt value using the private key
     * @param $rawValue Value to be encrypted
     * @param bool $base64encoded If it has to be base64 encoded or not.  TRUE by default
     * @return array [Encrypted value, initialization vector]
     */
    public function encrypt($rawValue, $base64encoded = true) {
        /**
         * Step 1: Set the initialization vector.  This value has to be random everytime it is generated
         * an encrypted value, so it is possible to assure the security.
         * This value has to be stored together with the encrypted value, otherwise it won't be possible to be
         * decrypted.  This value can be "public" without risking the security.
         */
        $ivSize = mcrypt_get_iv_size(self::MY_MCRYPT_CIPHER, self::MY_MCRYPT_MODE);
        $initializationVector = mcrypt_create_iv($ivSize, MCRYPT_RAND);

        //Step 2: Encrypt the data with all the settings
        $encryptedData = mcrypt_encrypt(self::MY_MCRYPT_CIPHER, self::MY_MCRYPT_KEY_STRING, $rawValue, self::MY_MCRYPT_MODE, $initializationVector);

        //Step 3: Encode data to Base64 if needed.  TRUE by default
        if ($base64encoded) {
            $encryptedData = base64_encode($encryptedData);
            $initializationVector = base64_encode($initializationVector);
        }

        //Step 4: Return the encrypted data and the initialization vector
        return [
            'encryptedData' => $encryptedData,
            'initializationVector' => $initializationVector
        ];
    }

    /**
     * Decrypt a value using the encrypted and initialization vector values
     * @param $encryptedValue Encrypted value to be decrypted
     * @param $initializationVector Stored initialization vector for that encrypted value
     * @param bool $base64encoded If values are Base64 encoded.  TRUE by default
     * @return mixed Decrypted value
     */
    public function decrypt($encryptedValue, $initializationVector, $base64encoded = true) {
        //Step 1: If data was stored using Base64 encoding, decode values
        if ($base64encoded) {
            $encryptedValue = base64_decode($encryptedValue);
            $initializationVector = base64_decode($initializationVector);
        }

        //Step 2: Decrypt the data with the private key and initialization vector values
        $decryptedData = mcrypt_decrypt(self::MY_MCRYPT_CIPHER, self::MY_MCRYPT_KEY_STRING, $encryptedValue, self::MY_MCRYPT_MODE, $initializationVector);

        //Step 3: Return the decrypted data. The rtrim is needed to remove padding added during encryption
        return rtrim($decryptedData);
    }

}