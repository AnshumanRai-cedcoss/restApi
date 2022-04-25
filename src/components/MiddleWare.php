<?php

namespace Api\Components;

use Phalcon\Security\JWT\Validator;
use Phalcon\Security\JWT\Token\Parser;
use Phalcon\Mvc\Micro\MiddlewareInterface;
use Phalcon\Mvc\Micro;
use Phalcon\Http\Response;
use DateTimeImmutable;
use Phalcon\Mvc\Controller;
use Phalcon\Acl\Adapter\Memory;
use Phalcon\Security\JWT\Builder;
use Phalcon\Security\JWT\Signer\Hmac;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;




class MiddleWare implements MiddlewareInterface
{

    public function authorize()
    {
        $key = "example_key";
        $payload = array(
            "iss" => "http://example.org",
            "aud" => "https://target.phalcon.io",
            "iat" => 1356999524,
            "nbf" => 1357000000,
            "role" => 'user',
            "name" => 'Ashu',
            "email" => 'abc@xyz.com',
            "fsf" => "https://phalcon.io"
        );
        
        /**
         * IMPORTANT:
         * You must specify supported algorithms for your application. See
         * https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40
         * for a list of spec-compliant algorithms.
         */
          $jwt = JWT::encode($payload, $key, 'HS256');
    
        
          echo $jwt."<br>" ;
          echo "<p class='text-danger'>Please copy this token for future use</p>";
     
    
        header('Refresh: 10; URL=http://localhost:8080/');
     
        die ;

        // Phalcon\Security\JWT\Token\Token object
        $tokenObject = $builder->getToken();

        // The token
        $token = $tokenObject->getToken();

        $response->setStatusCode(400)
            ->setJsonContent($token)
            ->send();
    }
    public function validate($token, $app)
    {

        if (!empty($token)) {
            $tokenReceived = $token;
            $audience      = 'https://target.phalcon.io';
            $now           = new DateTimeImmutable();
            $issued        = $now->getTimestamp();
            $notBefore     = $now->modify('-1 minute')->getTimestamp();
            $expires       = $now->getTimestamp();

            $issuer        = 'https://phalcon.io';
            try {
                $signer     = new Hmac();
                $passphrase = 'QcMpZ&b&mo3TPsPk668J6QH8JA$&U&m2';

                // Parse the token
                $parser      = new Parser();

                // Phalcon\Security\JWT\Token\Token object
                $tokenObject = $parser->parse($tokenReceived);

                // Phalcon\Security\JWT\Validator object
                $validator = new Validator($tokenObject, 0); // allow for a time shift of 100

                // Throw exceptions if those do not 

                $validator
                    ->validateAudience($audience)
                    ->validateExpiration($expires)

                    ->validateIssuedAt($issued)
                    ->validateIssuer($issuer)
                    ->validateNotBefore($notBefore)
                    ->validateSignature($signer, $passphrase);
            } catch (\Exception $e) {
                $app->response->setStatusCode(400)
                    ->setJsonContent($e->getMessage())
                    ->send();
                die;
            }
        } else {
            $app->response->setStatusCode(401, 'Token not found')
                ->setJsonContent('Token not found')
                ->send();
            die;
        }
    }
    public function call(Micro $app)
    {
        $token = $this->authorize();
        print_r($token);
        die();
        
        $check = explode('/', $app->request->get()['_url'])[1];
        $view = explode('/', $app->request->get()['_url'])[2];
        if ($check == "authorize") {
            $this->authorize();
        } else if ($view == "signup") {
            return;
        } else {
            $token =  $app->request->get("token");
            $this->validate($token, $app);
        }
    }
}
