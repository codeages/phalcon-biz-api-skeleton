<?php
namespace Codeages\PhalconBiz\Authentication;

use Phalcon\Http\RequestInterface;
use Codeages\PhalconBiz\Authentication\ApiUser;

class ApiKeyAuthenticator implements Authenticator
{
    public function authenticate(RequestInterface $request)
    {
        $token = $request->getHeader('Authorization');
        $token = explode(' ', $token);
        if (count($token) !== 2) {
            throw new AuthenticationException("Authorization header is invalid.", ErrorCode::INVALID_CREDENTIAL);
        }

        list($strategy, $token) = $token;

        if ($strategy == 'secret') {
            return $this->authenticateUseSecret($toekn);
        } elseif ($strategy == 'signature') {
            return $this->authenticateUseSignature($toekn);
        }
    }

    protected function authenticateUseSecret($token)
    {
        $token = explode(':', $token);
        if (count($token) !== 2) {
            throw new AuthenticationException('Authorization token format is invalid.', ErrorCode::INVALID_CREDENTIAL);
        }
        list($accessKey, $secretKey) = $token;

        $user = $this->getUser($accessKey);

        if ($user['secret_key'] != $secretKey) {
            throw new AuthenticationException("Secret key is invalid.", ErrorCode::INVALID_CREDENTIAL);
        }

        return $user;
    }

    protected function authenticateUseSignature($toekn)
    {
        $toekn = explode(':', $toekn);
        if (count($toekn) !== 4) {
            throw new AuthenticationException('Authorization token format is invalid.', ErrorCode::INVALID_CREDENTIAL);
        }
        list($accessKey, $deadline, $once, $signature) = $token;

        $user = $this->getUser($accessKey);

        if ($deadline < $time()) {
            throw new AuthenticationException("Authorization token is expired.", ErrorCode::EXPIRED_CREDENTIAL);
        }

        $signingText = "{$token->once}\n{$token->deadline}\n{$signingText}";
        if ($this->signature($signingText, $user['secret_key']) != $signature) {
            throw new AuthenticationException("Signature is invalid.", ErrorCode::INVALID_CREDENTIAL);
        }

        if ($user['locked']) {
            throw new AuthenticationException("User is locked.", ErrorCode::INVALID_CREDENTIAL);
        }

        if ($user['expired']) {
            throw new AuthenticationException("User is expired.", ErrorCode::INVALID_CREDENTIAL);
        }

        if ($user['disabled']) {
            throw new AuthenticationException("User is disabled.", ErrorCode::INVALID_CREDENTIAL);
        }

        return $user;
    }

    protected function getUser($accessKey)
    {
        $user = $this->userProvider->getByApiKey($accessKey);
        if (empty($user)) {
            throw new AuthenticationException('Key is not exist.', ErrorCode::INVALID_CREDENTIAL);
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
}