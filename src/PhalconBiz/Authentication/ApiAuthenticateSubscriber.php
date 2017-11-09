<?php
namespace Codeages\PhalconBiz\Authentication;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Codeages\PhalconBiz\Event\WebEvents;
use Codeages\PhalconBiz\Event\GetResponseEvent;
use Phalcon\Http\RequestInterface;
use Codeages\PhalconBiz\ErrorCode;

class ApiAuthenticateSubscriber implements EventSubscriberInterface
{
    public function onRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $this->authenticate($request);
    }

    public function authenticate(RequestInterface $request)
    {
        $token = $request->getHeader('Authorization');
        if (empty($token)) {
            throw new AuthenticateException("Authorization token is missing.", ErrorCode::INVALID_CREDENTIAL);
        }

        $token = explode(' ', $token);
        if (count($token) !== 2) {
            throw new AuthenticateException("Authorization token is invalid.", ErrorCode::INVALID_CREDENTIAL);
        }

        list($strategy, $token) = $token;

        $strategy = strtolower($strategy);
        if ($strategy == 'secret') {
            return $this->authenticateUseSecret($token);
        } elseif ($strategy == 'signature') {
            return $this->authenticateUseSignature($token);
        } else {
            throw new AuthenticateException("Authorization token is invalid.", ErrorCode::INVALID_CREDENTIAL);
        }
    }

    protected function authenticateUseSecret($token)
    {
        $token = explode(':', $token);
        if (count($token) !== 2) {
            throw new AuthenticateException('Authorization token format is invalid.', ErrorCode::INVALID_CREDENTIAL);
        }
        list($accessKey, $secretKey) = $token;

        $user = $this->getUser($accessKey);

        if ($user['secret_key'] != $secretKey) {
            throw new AuthenticateException("Secret key is invalid.", ErrorCode::INVALID_CREDENTIAL);
        }

        return $user;
    }

    protected function authenticateUseSignature($toekn)
    {
        $toekn = explode(':', $toekn);
        if (count($toekn) !== 4) {
            throw new AuthenticateException('Authorization token format is invalid.', ErrorCode::INVALID_CREDENTIAL);
        }
        list($accessKey, $deadline, $once, $signature) = $token;

        $user = $this->getUser($accessKey);

        if ($deadline < $time()) {
            throw new AuthenticateException("Authorization token is expired.", ErrorCode::EXPIRED_CREDENTIAL);
        }

        $signingText = "{$token->once}\n{$token->deadline}\n{$signingText}";
        if ($this->signature($signingText, $user['secret_key']) != $signature) {
            throw new AuthenticateException("Signature is invalid.", ErrorCode::INVALID_CREDENTIAL);
        }

        if ($user['locked']) {
            throw new AuthenticateException("User is locked.", ErrorCode::INVALID_CREDENTIAL);
        }

        if ($user['expired']) {
            throw new AuthenticateException("User is expired.", ErrorCode::INVALID_CREDENTIAL);
        }

        if ($user['disabled']) {
            throw new AuthenticateException("User is disabled.", ErrorCode::INVALID_CREDENTIAL);
        }

        return $user;
    }

    protected function getUser($accessKey)
    {
        $user = $this->userProvider->getByApiKey($accessKey);
        if (empty($user)) {
            throw new AuthenticateException('Key is not exist.', ErrorCode::INVALID_CREDENTIAL);
        }
        return ApiUser($user);
    }

    public function signature($signingText, $secretKey)
    {
        $signature = hash_hmac('sha1', $signingText, $secretKey, true);
        return  str_replace(array('+', '/'), array('-', '_'), base64_encode($signature));
    }

    public function makeSigningTextForSignature($request)
    {
        $uri = $request->getURI();
        $body = $request->getRawBody();

        return "{$uri}\n{$body}";
    }

    public function getClientIp($request)
    {
        if ($request instanceof \Phalcon\Http\Request) {
            return $request->getClientAddress(true);
        } elseif ($request instanceof \Symfony\Component\HttpFoundation\Request) {
            return $request->getClientIp();
        }
        throw new \InvalidArgumentException("Request class is not supported.");
    }

    public static function getSubscribedEvents()
    {
        return [
            WebEvents::REQUEST => 'onRequest',
        ];
    }
}
