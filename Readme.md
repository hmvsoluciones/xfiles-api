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

- obtener key de dropbox
- obtener secret de dropbox 
- Generar refresh token(Unica ves)
    - https://www.dropbox.com/oauth2/authorize?client_id=<APP_KEY>&response_type=code&token_access_type=offline
    - curl https://api.dropbox.com/oauth2/token -d code=cKDS4b01t0YAAAAAAAAANGkCXwKWG4NwPBFeZLwGd8M -d grant_type=authorization_code -u <APP_KEY>:<SECRET_KEY>
    - Obtener token para cada solicitud
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
