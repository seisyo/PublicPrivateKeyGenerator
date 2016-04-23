<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use Response;
use ZipArchive;

class MainController extends Controller
{
    public function getIndex()
    {
        return view('index');
    }

    public function getGenerateKeys()
    {
        $config = [
            'digest_alg' => 'sha512',
            'private_key_bits' => 2048,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ];
        
        $res = openssl_pkey_new($config);
        // Private Key
        openssl_pkey_export($res, $privateKey);
        // Public Key
        $publicKey = openssl_pkey_get_details($res);
        $publicKey = $publicKey["key"];

        $zip = new ZipArchive();
        $filename = "../storage/keys.zip";
        if ($zip->open($filename, ZipArchive::CREATE)!==TRUE) {
            exit("cannot open <$filename>\n");
        }
        
        $zip->addFromString('id_rsa', $privateKey);
        $zip->addFromString('id_rsa.pub', $publicKey);

        $zip->close();

        $headers = ['charset' => 'utf-8'];
        return Response::download('../storage/keys.zip', 'keys.zip', $headers);
    }
}
