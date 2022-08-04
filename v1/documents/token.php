<?php
require_once __DIR__ . '../vendor/autoload.php';

var_dump(getToken());

//https://www.dropbox.com/oauth2/authorize?client_id=<YOUR_APP_KEY>&response_type=code&token_access_type=offline

// https://www.dropbox.com/oauth2/authorize?client_id=v70u5o0081hqr68&response_type=code&token_access_type=offline

// curl https://api.dropbox.com/oauth2/token -d code=cKDS4b01t0YAAAAAAAAANGkCXwKWG4NwPBFeZLwGd8M -d grant_type=authorization_code -u v70u5o0081hqr68:40smd062968n027

/*
C:\Users\LUIS MUNGUIA>curl https://api.dropbox.com/oauth2/token -d code=cKDS4b01t0YAAAAAAAAANGkCXwKWG4NwPBFeZLwGd8M -d grant_type=authorization_code -u v70u5o0081hqr68:40smd062968n027
{"access_token": 
    "sl.BIlyZdy9PxJRz4eDuSy5rsmlkyMXLUzI-O42RY0iOvmqolih03dcIicxK0Hyt3QlIH4EyB5CcerUPotop2H0eLFFjtgvSLUTD2av6rP2x42D7qFb4DxpNnqC5JRA9INggo7W0uKwgVU", 
    "token_type": "bearer", 
    "expires_in": 14400, 
    "refresh_token": "acOMYLSmGyIAAAAAAAAAAQxa9iUc2m69dloyb7p-WVlmqq-D7-eTxcbkyyNkcnFW", 
    "scope": "account_info.read account_info.write file_requests.read file_requests.write files.content.read files.content.write files.metadata.read files.metadata.write sharing.read sharing.write", 
    "uid": "1260938369", 
    "account_id": "dbid:AAASnE_7yRmXq1D2R2AuaZ1bcgmQoo8f6SU"
}

{"access_token": "sl.BMsiifgqEzikLtLnUbIxPf1bOlg_zc6T8dnTgr0Qox_rI3T8Re3KEm0IZ53tY-N_v31Uz_sEfqNi6JsxCglSKw979E_HMMoXfToJcYkYTKLonv5l3JZK8XL1-aFHtx21kCy3-7EG_Sc", "token_type": "bearer", "expires_in": 14399, "refresh_token": "q9ov80vvB4EAAAAAAAAAARVwQ9qlMONopKjyklimYbsIFFjSjUKAbpKQSqKMcvdE", "scope": "account_info.read account_info.write file_requests.read file_requests.write files.content.read files.content.write files.metadata.read files.metadata.write sharing.read sharing.write", "uid": "1260938369", "account_id": "dbid:AAASnE_7yRmXq1D2R2AuaZ1bcgmQoo8f6SU"}
C:\Users\
*/


function getToken()
{
 /*
key s0vbxtuugtc6bee
secret 	
zy8tc1w6wk1sn7u
*/


$key ="v70u5o0081hqr68";
$secret ="40smd062968n027";
$refreshToken ="acOMYLSmGyIAAAAAAAAAAQxa9iUc2m69dloyb7p-WVlmqq-D7-eTxcbkyyNkcnFW";
    try {
        $client = new \GuzzleHttp\Client();
        $res = $client->request("POST", "https://{$key}:{$secret}@api.dropbox.com/oauth2/token", [
            'form_params' => [
                'grant_type' => 'refresh_token',
                'refresh_token' => $refreshToken,
            ]
        ]);
        if ($res->getStatusCode() == 200) {
            return json_decode($res->getBody(), TRUE);
        } else {
            return false;
        }
    }
    catch (Exception $e) {
        echo ("[{$e->getCode()}] {$e->getMessage()}");
        return false;
    }
}