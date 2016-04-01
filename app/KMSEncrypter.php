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
            'KeyId'     => '7593d6d3-f4af-4733-90be-77e0f70a35dc',
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