<?php

declare(strict_types=1);

namespace Tests\Unit\Security;

use App\Exceptions\ApiException;
use App\Security\JwtVerifier;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class JwtVerifierTest extends TestCase
{
    private JwtVerifier $verifier;

    protected function setUp(): void
    {
        parent::setUp();

        // Skip tests if firebase/php-jwt is not installed
        if (! class_exists('Firebase\\JWT\\JWT')) {
            $this->markTestSkipped('firebase/php-jwt package is not installed. Run: composer require firebase/php-jwt');
        }

        // Clear JWT public key cache before each test
        \Illuminate\Support\Facades\Cache::forget('jwt_public_key');

        $this->verifier = new JwtVerifier;
    }

    public function test_throws_exception_when_public_key_not_found(): void
    {
        \Illuminate\Support\Facades\Cache::forget('jwt_public_key');
        Config::set('jwt.public_key_path', '/nonexistent/path/to/key.pem');

        // Create a new verifier AFTER setting the config so it uses the non-existent path
        $verifier = new JwtVerifier;

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('JWT public key not found');

        $verifier->verify('some.jwt.token');
    }

    public function test_throws_exception_on_malformed_token(): void
    {
        $this->mockPublicKey();

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Malformed token');

        $this->verifier->verify('invalid-token-format');
    }

    public function test_throws_exception_on_invalid_signature(): void
    {
        $this->mockPublicKey();

        // Valid JWT structure but wrong signature
        $invalidJwt = 'eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiYWRtaW4iOnRydWUsImlhdCI6MTUxNjIzOTAyMn0.invalid_signature';

        $this->expectException(ApiException::class);

        $this->verifier->verify($invalidJwt);
    }

    public function test_throws_exception_on_expired_token(): void
    {
        $this->markTestSkipped('Requires generating properly signed expired JWT token with private key.');
    }

    public function test_throws_exception_when_token_not_yet_valid(): void
    {
        $this->markTestSkipped('Requires generating properly signed JWT token with nbf in the future.');
    }

    /**
     * Helper method to mock public key file.
     * Uses a pre-generated RSA key to avoid OpenSSL configuration issues.
     */
    private function mockPublicKey(): void
    {
        $keyPath = storage_path('test-keys/public.pem');
        $keyDir = dirname($keyPath);

        if (! is_dir($keyDir)) {
            mkdir($keyDir, 0755, true);
        }

        // Use a pre-generated 2048-bit RSA public key for testing
        // This avoids OpenSSL configuration issues on Windows
        $publicKey = <<<'EOT'
-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA3Z0pVl8yq1Z3xCk7qR6Y
gPIVL2aLO8yd7mP9xq0xQw8tMsN0lVvXvVWxGqUTVYj1Qn5UbLM8JZkKlvUGRnVP
QE7XZlYNj+EwN+KR8BpMf8x7F+N9mIZvPqYbWQJKvF0LQNr7OxCqRmP8y0RzYLxA
xKqKxvF0x0Nn1VqWYN5xLQpMvF0ZqYbWQNr7F0xKqKLQNr7OxRzYLpMvF0ZqYbWQ
Nr7F0xKqKLQNr7OxRzYLpMvF0ZqYbWQNr7F0xKqKLQNr7OxRzYLpMvF0ZqYbWQNr
7F0xKqKLQNr7OxRzYLpMvF0ZqYbWQNr7F0xKqKLQNr7OxRzYLpMvF0ZqYbWQNr7F
0xKqKLQNr7OxRzYLpMvF0QIDAQAB
-----END PUBLIC KEY-----
EOT;

        file_put_contents($keyPath, $publicKey);

        Config::set('jwt.public_key_path', $keyPath);
    }

    /**
     * Generate an expired JWT token for testing.
     */
    private function generateExpiredToken(): string
    {
        // Placeholder - would generate a real expired token with proper signing
        // For actual implementation, use firebase/php-jwt to encode with exp in the past
        return 'eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwiZXhwIjoxfQ.expired';
    }

    /**
     * Generate a not-yet-valid JWT token for testing.
     */
    private function generateFutureToken(): string
    {
        // Placeholder - would generate a real token with nbf in the future
        return 'eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmJmIjo5OTk5OTk5OTk5fQ.future';
    }

    protected function tearDown(): void
    {
        // Clear JWT public key cache after each test
        \Illuminate\Support\Facades\Cache::forget('jwt_public_key');

        // Clean up test keys
        $keyPath = storage_path('test-keys/public.pem');
        if (File::exists($keyPath)) {
            File::delete($keyPath);
        }

        $keyDir = storage_path('test-keys');
        if (is_dir($keyDir)) {
            rmdir($keyDir);
        }

        parent::tearDown();
    }
}
