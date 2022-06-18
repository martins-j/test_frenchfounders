<?php

declare(strict_types=1);

namespace App\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

/**
 * LoginAuthenticator class
 */
class LoginAuthenticator extends AbstractGuardAuthenticator
{
    /** @var UserPasswordEncoderInterface $passwordEncoder */
    private $passwordEncoder;

    /**
     * LoginAuthenticator construct.
     * 
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * Tells Symfony that this authenticator should be used when :
     *  - the user posts to the /login endpoint (name = 'login')
     *  - the requested method is "POST"
     * 
     * @param Request $request
     * 
     * @return bool
     */
    public function supports(Request $request): bool
    {
        return ($request->get('_route') === 'login') && $request->isMethod('POST');
    }

    /**
     * Gets the user information required to log in (email and password)
     * 
     * @param Request $request
     * 
     * @return array
     */
    public function getCredentials(Request $request): array
    {
        return [
            'email'    => $request->request->get("email"),
            'password' => $request->request->get("password")
        ];
    }

    /**
     * Gets the user that is trying to log in
     * 
     * @param array                 $credentials
     * @param UserProviderInterface $userProvider
     * 
     * @return UserInterface
     */
    public function getUser($credentials, UserProviderInterface $userProvider): UserInterface
    {
        return $userProvider->loadUserByUsername($credentials['email']);
    }

    /**
     * Checks the user's credentials
     * 
     * @param array         $credentials
     * @param UserInterface $user
     * 
     * @return bool
     */
    public function checkCredentials($credentials, UserInterface $user): bool
    {
        return $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
    }

    /**
     * Gets called any time there is an error
     *  (e.g.:
     *    - if the getUser or the loadUserByUsername method does not find the user with the email given
     *    - when the credentials are not correct
     * )
     * 
     * @param Request                 $request
     * @param AuthenticationException $exception
     * 
     * @return JsonResponse
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): JsonResponse
    {
        return new JsonResponse(
            [
                'error' => $exception->getMessageKey()
            ],
            400
        );
    }

    /**
     * Gets called when the checkCredentials method returns
     * 
     * @param Request        $request
     * @param TokenInterface $token
     * @param string         $providerKey
     * 
     * @return JsonResponse
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): JsonResponse
    {
        return new JsonResponse(
            [
                'result' => 'Successfully logged in!'
            ]
        );
    }

    /**
     * Gets called whenever an endpoint that requires authentication is hit
     * 
     * @param Request                 $request
     * @param AuthenticationException $authException
     * 
     * @return JsonResponse
     */
    public function start(Request $request, AuthenticationException $authException = null): JsonResponse
    {
        return new JsonResponse(
            [
                'error' => 'Access Denied!'
            ]
        );
    }

    /**
     * @return bool
     */
    public function supportsRememberMe(): bool
    {
        return false;
    }
}
