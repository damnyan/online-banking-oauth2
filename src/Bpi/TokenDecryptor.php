<?php

namespace Dmn\OnlineBankingOAuth2\Bpi;

use Jose\Component\Checker\HeaderCheckerManager;
use Jose\Component\Checker\IssuerChecker;
use Jose\Component\Core\AlgorithmManager;
use Jose\Component\Core\JWKSet;
use Jose\Component\Core\Util\JsonConverter;
use Jose\Component\Encryption\Algorithm\ContentEncryption\A128CBCHS256;
use Jose\Component\Encryption\Algorithm\KeyEncryption\RSAOAEP;
use Jose\Component\Encryption\Compression\CompressionMethodManager;
use Jose\Component\Encryption\Compression\Deflate;
use Jose\Component\Encryption\JWEDecrypter;
use Jose\Component\Encryption\JWELoader;
use Jose\Component\Encryption\JWETokenSupport;
use Jose\Component\Encryption\Serializer\CompactSerializer as JWECompactSerializer;
use Jose\Component\Encryption\Serializer\JWESerializerManager;
use Jose\Component\KeyManagement\JWKFactory;
use Jose\Component\NestedToken\NestedTokenLoader;
use Jose\Component\Signature\Algorithm\RS256;
use Jose\Component\Signature\JWSLoader;
use Jose\Component\Signature\JWSTokenSupport;
use Jose\Component\Signature\JWSVerifier;
use Jose\Component\Signature\Serializer\CompactSerializer as JWSCompactSerializer;
use Jose\Component\Signature\Serializer\JWSSerializerManager;

class TokenDecryptor
{
    protected $loader;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->loader = new NestedTokenLoader(
            $this->jweLoader(),
            $this->jwsLoader()
        );
    }

    /**
     * Decrypte
     *
     * @param string $token
     *
     * @return array
     */
    public function decrypt(string $token): array
    {
        $jws = $this->loader
            ->load(
                $token,
                $this->encryptionKey(),
                $this->signatureKey()
            );

        return JsonConverter::decode($jws->getPayload());
    }

    /**
     * Ecryption key
     *
     * @return JWKSet
     */
    private function encryptionKey(): JWKSet
    {
        $keyFile = config('services.bpi.private_key');
        $jwk = JWKFactory::createFromKeyFile($keyFile);
        return new JWKSet([$jwk]);
    }

    /**
     * Signature key
     *
     * @return JWKSet
     */
    private function signatureKey(): JWKSet
    {
        $keyFile = config('services.bpi.sender_certificate');
        $jwk = JWKFactory::createFromKeyFile($keyFile);
        return new JWKSet([$jwk]);
    }

    /**
     * JWESerializerManager
     *
     * @return JWESerializerManager
     */
    private function jweSerializerManager(): JWESerializerManager
    {
        return new JWESerializerManager([new JWECompactSerializer()]);
    }

    /**
     * JWELoader
     *
     * @return JWELoader
     */
    private function jweLoader(): JWELoader
    {
        $serializerManager = $this->jweSerializerManager();

        // The key encryption algorithm manager with the A256KW algorithm.
        $keyEncryptionAlgorithmManager = new AlgorithmManager([new RSAOAEP()]);

        // The content encryption algorithm manager with the A256CBC-HS256 algorithm.
        $contentEncryptionAlgorithmManager = new AlgorithmManager([
            new A128CBCHS256(),
        ]);

        // The compression method manager with the DEF (Deflate) method.
        $compressionMethodManager = new CompressionMethodManager([
            new Deflate(),
        ]);

        $jweDecrypter = new JWEDecrypter(
            $keyEncryptionAlgorithmManager,
            $contentEncryptionAlgorithmManager,
            $compressionMethodManager
        );

        $headerCheckerManager = new HeaderCheckerManager(
            [new IssuerChecker(['BPI'])],
            [new JWETokenSupport()]
        );

        return new JWELoader(
            $serializerManager,
            $jweDecrypter,
            $headerCheckerManager
        );
    }

    /**
     * JWSLoader
     *
     * @return JWSLoader
     */
    private function jwsLoader(): JWSLoader
    {
        $serializerManager = new JWSSerializerManager(
            [new JWSCompactSerializer()]
        );

        $jwsVerifier = new JWSVerifier(
            new AlgorithmManager([
                new RS256()
            ])
        );

        $headerCheckerManager = new HeaderCheckerManager(
            [new IssuerChecker(['BPI'])],
            [new JWSTokenSupport()]
        );

        return new JWSLoader(
            $serializerManager,
            $jwsVerifier,
            $headerCheckerManager
        );
    }
}
