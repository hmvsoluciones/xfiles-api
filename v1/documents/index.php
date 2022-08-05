<?php
date_default_timezone_set('America/Mexico_City');

require_once __DIR__ . '../../../vendor/autoload.php';
/**
 * Importaciones de clases, modulos y DTOS
 */
require_once __DIR__ . '../../../app/config/api.php';
require_once __DIR__ . '../../../app/connection/Connection.php';
require_once __DIR__ . '../../../app/config/SlimConfig.php';
require_once __DIR__ . '../../../app/interceptor/MiddlewareValidator.php';
require_once __DIR__ . '../../../app/module/multimedia/dao/MultimediaDao.php';
require_once __DIR__ . '../../../app/module/multimedia/dao/impl/MultimediaDaoImpl.php';
require_once __DIR__ . '../../../app/module/multimedia/service/MultimediaService.php';
require_once __DIR__ . '../../../app/module/multimedia/service/impl/MultimediaServiceImpl.php';
require_once __DIR__ . '../../../app/util/Util.php';
require_once __DIR__ . '../../../app/util/impl/UtilImpl.php';
require_once __DIR__ . '../../../app/model/WSResponse.php';
require_once __DIR__ . '../../../app/model/MultimediaDTO.php';

/**
 * Configuracion SLIM para API Restful Cors
 */
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, cache");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");

use Kunnu\Dropbox\Dropbox;
use Kunnu\Dropbox\DropboxApp;

/**
 * Configuracion SLIM para API Restful
 */
$slimConfig = new SlimConfig();
$container = new \Slim\Container($slimConfig->getSlimConfiguration());
$app = new \Slim\App($container);

/**
 * @type POST
 * http://localhost/ticketapp-systems/api/v1/demo/add
 * Params:
 * {
 *   "file": multipart form
 * }
 * Response
 * {
 *   "url": "https://www.dropbox.com/s/i0v1gygwglurl9d/61158e9698921.png?dl=0",
 *   "url-get": "https://www.dropbox.com/s/i0v1gygwglurl9d/61158e9698921.png?raw=1",
 *   "id": "id:0eJ7a2_XAPIAAAAAAAAASw",
 *   "name": "61158e9698921.png"
 *}
 */
$app->post('/upload', function ($request, $response, $args = []) {

   

    $multimediaDTO = new MultimediaDTO();
    $multimediaService = new MultimediaServiceImpl();

    $config = parse_ini_file(__DIR__ . '../../../app/config/config dropbox.ini');
    $dropboxKey = $config["key"];
    $dropboxSecret = $config["secret"];
    $dropboxRefreshToken = $config["refreshToken"];

    try {
        // Generacion de token dinamico
        $dropboxToken =  getDropboxToken($dropboxKey, $dropboxSecret, $dropboxRefreshToken);

        if (!$dropboxToken) {
            return $response->withStatus(500)
                ->withHeader('Content-Type', 'application/json')
                ->write(json_encode(array("error" => "No fue posible obtener el token dinamico para la carga de archivos")));
        }

        $app = new DropboxApp($dropboxKey, $dropboxSecret, $dropboxToken);
        $dropbox = new Dropbox($app);

        if (!empty($_FILES)) {
            $nombre = uniqid();
            $tempfile = $_FILES['file']['tmp_name'];
            $ext = explode(".", $_FILES['file']['name']);
            $ext = end($ext);
            $nombredropbox = "/" . $nombre . "." . $ext;


            $file = $dropbox->simpleUpload($tempfile, $nombredropbox, ['autorename' => true]);
            //echo "archivo subido";                  
            // print_r($file);
            //print_r($file->getName());        

            $parameters = array('path' => "/" . $file->getName());

            $headers = array(
                "Authorization: Bearer {$dropboxToken}",
                "Content-Type: application/json"
            );

            $curlOptions = array(
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($parameters),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_VERBOSE => true
            );

            $ch = curl_init('https://api.dropboxapi.com/2/sharing/create_shared_link_with_settings');
            curl_setopt_array($ch, $curlOptions);
            $responseCurl = curl_exec($ch);
            $dboxResponse = json_decode($responseCurl);
            curl_close($ch);

            $multimediaDTO->setIdRemote($dboxResponse->{'id'});
            $multimediaDTO->setNombreMultimedia($dboxResponse->{'name'});
            $multimediaDTO->setUrlMultimedia($dboxResponse->{'url'});
            $multimediaDTO->setUrlMultimediaGet(str_replace("?dl=0", "?raw=1", $dboxResponse->{'url'}));
            $multimediaDTO->setExtensionMultimedia(pathinfo($dboxResponse->{'name'}, PATHINFO_EXTENSION));

            
            $multimediaResponse = $multimediaService->add($multimediaDTO);
            if ($multimediaResponse) {
                return $response->withStatus(200)
                    ->withHeader('Content-Type', 'application/json')
                    ->write(json_encode($multimediaResponse->expose()));
            } else {
                return $response->withStatus(500)
                    ->withHeader('Content-Type', 'application/json')
                    ->write(json_encode(array("error" => "No fue posible cargar el archivo")));
            }
        }
    } catch (Exception $exc) {
        return $response->withStatus(500)
            ->withHeader('Content-Type', 'application/json')
            ->write(json_encode(array("error" => "Ocurrio un error:" . $exc->getMessage())));
    }
})->add(new MiddlewareValidator());
/**
 * @type POST
 * http://localhost/ticketapp-systems/api/v1/demo/add
 * Params:
 * {
 *   "file": multipart form
 * }
 * Response
 * {
 *   "url": "https://www.dropbox.com/s/i0v1gygwglurl9d/61158e9698921.png?dl=0",
 *   "url-get": "https://www.dropbox.com/s/i0v1gygwglurl9d/61158e9698921.png?raw=1",
 *   "id": "id:0eJ7a2_XAPIAAAAAAAAASw",
 *   "name": "61158e9698921.png"
 *}
 */
$app->post('/upload-multiple', function ($request, $response, $args = []) {
    $multimediaService = new MultimediaServiceImpl();
    $multimediaDTO = new MultimediaDTO();

    $config = parse_ini_file(__DIR__ . '../../../app/config/config dropbox.ini');
    $dropboxKey = $config["key"];
    $dropboxSecret = $config["secret"];
    $dropboxRefreshToken = $config["refreshToken"];

    try {
        // Generacion de token dinamico
        $dropboxToken =  getDropboxToken($dropboxKey, $dropboxSecret, $dropboxRefreshToken);

        if (!$dropboxToken) {
            return $response->withStatus(500)
                ->withHeader('Content-Type', 'application/json')
                ->write(json_encode(array("error" => "No fue posible obtener el token dinamico para la carga de archivos")));
        }

        $app = new DropboxApp($dropboxKey, $dropboxSecret, $dropboxToken);
        $dropbox = new Dropbox($app);

        $serviceResponseArray = array();

        for ($i = 0; $i < count($_FILES['fileSlider']['name']); $i++) {

            $nombre = uniqid();
            $tempfile = $_FILES['fileSlider']['tmp_name'][$i];
            $ext = explode(".", $_FILES['fileSlider']['name'][$i]);
            $ext = end($ext);
            $nombredropbox = "/" . $nombre . "." . $ext;

            $file = $dropbox->simpleUpload($tempfile, $nombredropbox, ['autorename' => true]);
            $parameters = array('path' => "/" . $file->getName());

            $headers = array(
                "Authorization: Bearer {$dropboxToken}",
                "Content-Type: application/json"
            );

            $curlOptions = array(
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($parameters),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_VERBOSE => true
            );

            $ch = curl_init('https://api.dropboxapi.com/2/sharing/create_shared_link_with_settings');
            curl_setopt_array($ch, $curlOptions);
            $responseCurl = curl_exec($ch);
            $dboxResponse = json_decode($responseCurl);
            curl_close($ch);

            $multimediaDTO->setIdRemote($dboxResponse->{'id'});
            $multimediaDTO->setNombreMultimedia($dboxResponse->{'name'});
            $multimediaDTO->setUrlMultimedia($dboxResponse->{'url'});
            $multimediaDTO->setUrlMultimediaGet(str_replace("?dl=0", "?raw=1", $dboxResponse->{'url'}));
            $multimediaDTO->setExtensionMultimedia(pathinfo($dboxResponse->{'name'}, PATHINFO_EXTENSION));

            $multimediaResponse = $multimediaService->add($multimediaDTO);

            array_push($serviceResponseArray, $multimediaResponse->expose());
        }
        if ($serviceResponseArray) {
            return $response->withStatus(200)
                ->withHeader('Content-Type', 'application/json')
                ->write(json_encode($serviceResponseArray));
        } else {
            return $response->withStatus(500)
                ->withHeader('Content-Type', 'application/json')
                ->write(json_encode(array("error" => "No fue posible cargar el archivo")));
        }
    } catch (Exception $exc) {
        return $response->withStatus(500)
            ->withHeader('Content-Type', 'application/json')
            ->write(json_encode(array("error" => "Ocurrio un error:" . $exc->getMessage())));
    }
})->add(new MiddlewareValidator());
/**
 * @type POST
 * http://localhost/ticketapp-systems/api/v1/demo/add
 * Params:
 * {
 *   "id":"id:0eJ7a2_XAPIAAAAAAAAARw"
 * }
 */
$app->post('/delete', function ($request, $response, $args = []) {
    $multimediaService = new MultimediaServiceImpl();

    $config = parse_ini_file(__DIR__ . '../../../app/config/config dropbox.ini');
    $dropboxKey = $config["key"];
    $dropboxSecret = $config["secret"];
    $dropboxRefreshToken = $config["refreshToken"];

    try {
        // Generacion de token dinamico
        $dropboxToken =  getDropboxToken($dropboxKey, $dropboxSecret, $dropboxRefreshToken);

        if (!$dropboxToken) {
            return $response->withStatus(500)
                ->withHeader('Content-Type', 'application/json')
                ->write(json_encode(array("error" => "No fue posible obtener el token dinamico para la carga de archivos")));
        }

        $app = new DropboxApp($dropboxKey, $dropboxSecret, $dropboxToken);
        $dropbox = new Dropbox($app);
        $parsedBody = $request->getParsedBody();

        $deleted = $dropbox->delete($parsedBody['id']);
        $deletedDb = $multimediaService->delete($parsedBody['id']);

        if ($deleted && $deletedDb) {
            return $response->withStatus(200)
                ->withHeader('Content-Type', 'application/json')
                ->write(json_encode($deleted));
        } else {
            return $response->withStatus(500)
                ->withHeader('Content-Type', 'application/json')
                ->write(json_encode(array("error" => "No fue posible eliminar el archivo dbx:{$deleted}, db:{$deletedDb}")));
        }
    } catch (Exception $exc) {
        return $response->withStatus(500)
            ->withHeader('Content-Type', 'application/json')
            ->write(json_encode(array("error" => "Ocurrio un error al eliminar el registro:" . $exc->getMessage())));
    }
})->add(new MiddlewareValidator());

/**
 * @type GET
 * @param Path id
 * Obtenert todos los registros
 * http://localhost/ticketapp-systems/api/v1/demo/1
 */
$app->get('/{id}', function ($request, $response, $args = []) {
    try {

        $multimediaService = new MultimediaServiceImpl();

        $serviceResponse = $multimediaService->get($args['id']);

        if ($serviceResponse) {
            return $response->withStatus(200)
                ->withHeader('Content-Type', 'application/json')
                ->write(json_encode($serviceResponse));
        } else {
            return $response->withStatus(500)
                ->withHeader('Content-Type', 'application/json')
                ->write(json_encode(array("error" => "No fue posible obtener el registro")));
        }
    } catch (Exception $exc) {
        return $response->withStatus(500)
            ->withHeader('Content-Type', 'application/json')
            ->write(json_encode(array("error" => "Ocurrio un error al obtener el registro:" . $exc->getMessage())));
    }
})->add(new MiddlewareValidator());

/**
 * Funcion que genera el token dinamico
 */
function getDropboxToken($key, $secret, $refreshToken)
{
    $client = new \GuzzleHttp\Client();
    $res = $client->request("POST", "https://{$key}:{$secret}@api.dropbox.com/oauth2/token", [
        'form_params' => [
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken,
        ]
    ]);
    if ($res->getStatusCode() == 200) {        
        $response =  json_decode($res->getBody(), TRUE);
        return $response["access_token"];
    } else {
        return false;
    }
}
$app->run();
