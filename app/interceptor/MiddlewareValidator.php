<?php
require_once __DIR__ . '../../../app/connection/Connection.php';
require_once __DIR__ . '../../../app/module/demo/dao/DemoDao.php';
require_once __DIR__ . '../../../app/module/demo/dao/impl/DemoDaoImpl.php';


class MiddlewareValidator {

    /**
     * Midleware
     * Interceptor de validacion de seguridad
     *https://github.com/dyorg/slim-token-authentication/blob/master/src/TokenAuthentication.php
     * @param  \Psr\Http\Message\ServerRequestInterface $request  PSR7 request
     * @param  \Psr\Http\Message\ResponseInterface      $response PSR7 response
     * @param  callable                                 $next     Next middleware
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke($request, $response, $next) {                 

      $valid = new DemoDaoImpl();
            

      if(strpos($request->getServerParams()["REQUEST_URI"], "/users/login") 
      || strpos($request->getServerParams()["REQUEST_URI"], "/demo")
      || strpos($request->getServerParams()["REQUEST_URI"], "users/password-recovery")
      || strpos($request->getServerParams()["REQUEST_URI"], "events-site")
      || strpos($request->getServerParams()["REQUEST_URI"], "boletos/get-boletos-lista")
      || strpos($request->getServerParams()["REQUEST_URI"], "boletos/get-lista-inivitados")
      || strpos($request->getServerParams()["REQUEST_URI"], "boletos/get-kardex/")      
      || strpos($request->getServerParams()["REQUEST_URI"], "boletos/kardex/update")      
      
      ){               
          $response = $next($request, $response);                        
      } else if(
        !empty($request->getHeaders()["HTTP_X_API_KEY"]) 
        && isset($request->getHeaders()["HTTP_X_API_KEY"][0])
        && in_array($request->getHeaders()["HTTP_X_API_KEY"][0], API_KEYS) 
        && count($valid->getAllData()) > 0
      ){
        $response = $next($request, $response);  
      } else {

        $response->getBody()->write(json_encode(array("error" => "Error 401 unauthorized")));                           
        
        return $response->withHeader('Content-Type', 'application/json')
        ->withStatus(401);
      }
        return $response;
    }

}
