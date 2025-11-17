<?php

declare(strict_types=1);

namespace App\Security;

use App\Contracts\Security\JwtVerifierContract;
use App\Exceptions\ApiException;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;

class JwtVerifier extends BaseJwtHandler implements JwtVerifierContract
{
    /**
     * Verify and decode a JWT token.
     *
     * @throws ApiException
     */
    public function verify(string $jwt): array
    {
        $publicKey = $this->loadPublicKey();

        try {
            $decoded = JWT::decode(
                $jwt,
                new Key($publicKey, $this->getAlgorithm())
            );

            return $this->convertToArray($decoded);
        } catch (SignatureInvalidException $e) {
            throw new ApiException('Invalid token signature', 401);
        } catch (BeforeValidException $e) {
            throw new ApiException('Token not yet valid', 401);
        } catch (ExpiredException $e) {
            throw new ApiException('Token has expired', 401);
        } catch (\UnexpectedValueException $e) {
            throw new ApiException('Malformed token: '.$e->getMessage(), 401);
        } catch (\DomainException $e) {
            throw new ApiException('Token verification failed: '.$e->getMessage(), 401);
        }
    }
}
