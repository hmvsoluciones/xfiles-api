# XFiles API

API carga de archivos en dropbox, actualizacion dinamica de token


# Implementación:
```
{
	
}
```

# Funcionalidades
- upload -> Carga de un solo archivo
- upload-multiple -> Carga de multiples archivos
- delete -> elimina un archivo por id
- get/{id} -> obtiene la información de un archivo 

# Creacion y actualizacion de TokenDropbox

# Generar el refresh tocken para cada solicitud
- Se deben configurar primero los permisos para que el token tenga la version final
- obtener key de dropbox
- obtener secret de dropbox 
- Generar refresh token(Unica ves)
    - Generar [ACCESS_CODE] -> https://www.dropbox.com/oauth2/authorize?client_id=<APP_KEY>&response_type=code&token_access_type=offline
    - Ejecutar en CMD: curl https://api.dropbox.com/oauth2/token -d code=[ACCESS_CODE] -d grant_type=authorization_code -u <APP_KEY>:<SECRET_KEY>
    - Obtener el refresh token y usarlo en la siguiente funcion para obtener un token por petición
```
    function getToken(){

    $key ="?";
    $secret ="?";
    $refreshToken ="RESULT OF PREVIOUS STEEPS";
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
    ```
