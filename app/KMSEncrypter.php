<?php namespace App;

class KMSEncrypter
{

    /**
     * @param string $string
     *
     * @return string
     */
    public function encryptString($string)
    {
        $kms = \App::make('aws')->createClient('kms');

        $key = $kms->encrypt([
            'KeyId'     => env('KMS_KEY'),
            'Plaintext' => $string,
        ]);
        return $key->get('CiphertextBlob');
    }

    /**
     * @param string $encryptedString
     *
     * @return string
     */
    public function decryptString($encryptedString)
    {
        $kms = \App::make('aws')->createClient('kms');

        $result = $kms->decrypt([
            'CiphertextBlob' => $encryptedString,
        ]);

        return $result->get('Plaintext');
    }
}