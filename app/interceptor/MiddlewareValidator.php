<?php
require_once __DIR__ . '../../../app/connection/Connection.php';

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

      if(
        !empty($request->getHeaders()["HTTP_X_API_KEY"]) 
        && isset($request->getHeaders()["HTTP_X_API_KEY"][0])
        && in_array($request->getHeaders()["HTTP_X_API_KEY"][0], API_KEYS) 
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
