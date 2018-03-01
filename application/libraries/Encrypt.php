<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Encrypt {
    function sha256encrypt($string,$secret_key,$secret_iv) {
        $method = 'aes-256-cfb';
        $encrypted = base64_encode( openssl_encrypt ($string, $method, $secret_key, true, $secret_iv));
        return $encrypted;
    }

    function sha256decrypt($string,$secret_key,$secret_iv) {
        $decrypted = '';
        $method = 'aes-256-cfb';
        $decrypted = openssl_decrypt( base64_decode($string), $method, $secret_key, 1, $secret_iv);
        return $decrypted;
    }
}
?>
