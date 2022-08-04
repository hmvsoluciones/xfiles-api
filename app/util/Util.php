<?php

interface Util
{

    public function encrypt($text);

    public function decrypt($text);
    /**
     *
     * $params["subject"]
     * $params["from"]
     * $params["fromName"]
     * $params["toArray"]
     * $params["body"]       
     */
    public function sendMail($params);

    public function mailTemplate($titulo, $body);

    public function mailTemplateMandrill($titulo, $body);

    public function sendWhatsapp($params);

    public function sendSMS($params);
}

/*
echo "INICIO <br/>";


function encrypt($plaintext, $password) {
    $method = "AES-256-CBC";
    $key = hash('sha256', $password, true);
    $iv = openssl_random_pseudo_bytes(16);

    echo "iv:".$iv."<br/>";
    $ciphertext = openssl_encrypt($plaintext, $method, $key, OPENSSL_RAW_DATA, $iv);
    $hash = hash_hmac('sha256', $ciphertext, $key, true);

    return base64_encode ($iv . $hash . $ciphertext);
}

function decrypt($ivHashCiphertext, $password) {
	$ivHashCiphertext = base64_decode($ivHashCiphertext);
    $method = "AES-256-CBC";
    $iv = substr($ivHashCiphertext, 0, 16);
    $hash = substr($ivHashCiphertext, 16, 32);
    $ciphertext = substr($ivHashCiphertext, 48);
    $key = hash('sha256', $password, true);

    if (hash_hmac('sha256', $ciphertext, $key, true) !== $hash) return null;

    return openssl_decrypt($ciphertext, $method, $key, OPENSSL_RAW_DATA, $iv);
}


$texto = "Texto a encriptar";
$encrypted = encrypt($texto, 'password'); // this yields a binary string

echo $encrypted."<br/>";

echo decrypt($encrypted, 'password');
// decrypt($encrypted, 'wrong password') === null
 * */
