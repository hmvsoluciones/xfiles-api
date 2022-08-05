<?php
require_once __DIR__ . '../vendor/autoload.php';

var_dump(getToken());
/**
 * https://stackoverflow.com/questions/70641660/how-do-you-get-and-use-a-refresh-token-for-the-dropbox-api-python-3-x
 */

//https://www.dropbox.com/oauth2/authorize?client_id=<YOUR_APP_KEY>&response_type=code&token_access_type=offline

// https://www.dropbox.com/oauth2/authorize?client_id=s0vbxtuugtc6bee&response_type=code&token_access_type=offline

// curl https://api.dropbox.com/oauth2/token -d code=TOKEN_GENERADO -d grant_type=authorization_code -u v70u5o0081hqr68:40smd062968n027

// HMV curl https://api.dropbox.com/oauth2/token -d code=cKDS4b01t0YAAAAAAAAARj8ZcLZnEbryAWZL2H739aY -d grant_type=authorization_code -u s0vbxtuugtc6bee:zy8tc1w6wk1sn7u

/*
C:\Users\LUIS MUNGUIA>
curl https://api.dropbox.com/oauth2/token -d code=cKDS4b01t0YAAAAAAAAARj8ZcLZnEbryAWZL2H739aY -d grant_type=authorization_code -u s0vbxtuugtc6bee:zy8tc1w6wk1sn7u
{
    "access_token": "sl.BMviWtksdoODNvhn0FexIGWV0cBqNO9TebPT00-SlmE8_1YMmhQQw4Fmyjrnr6JFKZGonYNBU1SNPa93AUzqF5uXJuEzvNfifl3w0_AgjM7MRX40xd-H4gQXylZqeKCa6D860J1-t28", 
    "token_type": "bearer", "expires_in": 14400, 
    "refresh_token": "UHqjNVcy00UAAAAAAAAAAa5y2NclwW_u_BkAKAS5VdP8KiaxTI5v5-a2nvEUjcVY", "scope": "account_info.read account_info.write file_requests.read file_requests.write files.content.read files.content.write files.metadata.read files.metadata.write sharing.read sharing.write", "uid": "1260938369", "account_id": "dbid:AAASnE_7yRmXq1D2R2AuaZ1bcgmQoo8f6SU"}
C:\Users\LUIS MUNGUIA>

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