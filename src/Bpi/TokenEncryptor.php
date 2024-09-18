<?php

namespace Dmn\OnlineBankingOAuth2\Bpi;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Jose\Component\Core\AlgorithmManager;
use Jose\Component\Core\JWK;
use Jose\Component\Core\Util\JsonConverter;
use Jose\Component\Encryption\Algorithm\ContentEncryption\A128CBCHS256;
use Jose\Component\Encryption\Algorithm\KeyEncryption\RSAOAEP;
use Jose\Component\Encryption\Compression\CompressionMethodManager;
use Jose\Component\Encryption\Compression\Deflate;
use Jose\Component\Encryption\JWEBuilder;
use Jose\Component\Encryption\Serializer\CompactSerializer as JWECompactSerializer;
use Jose\Component\Encryption\Serializer\JWESerializerManager;
use Jose\Component\KeyManagement\JWKFactory;
use Jose\Component\NestedToken\NestedTokenBuilder;
use Jose\Component\Signature\Algorithm\RS256;
use Jose\Component\Signature\JWSBuilder;
use Jose\Component\Signature\Serializer\CompactSerializer as JWSCompactSerializer;
use Jose\Component\Signature\Serializer\JWSSerializerManager;

class TokenEncryptor
{
    protected $builder;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->builder = new NestedTokenBuilder(
            $this->jweBuilder(),
            $this->jweSerializerManager(),
            $this->jwsBuilder(),
            $this->jwsSerializerManager()
        );
    }

    /**
     * Encrypt
     *
     * @param array $data
     *
     * @return string
     */
    public function encrypt(array $data): string
    {
        $timestamp = new Carbon();

        $payloadFields = [
            'jti' => Str::uuid(),
            'iss' => 'PARTNER',
            'aud' => 'BPI',
            'sub' => 'fundTopUp',
            'iat' => $timestamp->getTimestamp(),
            'exp' => $timestamp->addMinutes(60 * 4)->getTimestamp(),
        ];

        $data = array_merge($payloadFields, $data);

        return $this->builder
            ->create(
                JsonConverter::encode($data),
                [
                    [
                        'key' => $this->signatureKey(),
                        'protected_header' => [
                            'alg' => 'RS256',
                        ],
                        'header' => [],
                    ],
                ],
                JWSCompactSerializer::NAME,
                [
                    'enc' => (new A128CBCHS256())->name(),
                    'alg' => (new RSAOAEP())->name(),
                    'cty' => 'JWT',
                ],
                [],
                [
                    [
                        'key' => $this->encryptionKey(),
                        'header' => [],
                    ],
                ],
                JWECompactSerializer::NAME
            );
    }

    /**
     * Signature key
     *
     * @return JWK
     */
    private function signatureKey(): JWK
    {
        $keyFile = config('services.bpi.private_key');

        return JWKFactory::createFromKeyFile($keyFile);
    }

    /**
     * Encryption key
     *
     * @return JWK
     */
    private function encryptionKey(): JWK
    {
        $keyFile = config('services.bpi.sender_certificate');

        return JWKFactory::createFromKeyFile($keyFile);
    }

    /**
     * Undocumented JWESerializerManager
     *
     * @return JWESerializerManager
     */
    private function jweSerializerManager(): JWESerializerManager
    {
        return new JWESerializerManager([new JWECompactSerializer()]);
    }

    /**
     * JWEBuilder
     *
     * @return JWEBuilder
     */
    private function jweBuilder(): JWEBuilder
    {
        $keyEncryptionAlgorithmManager = new AlgorithmManager([
            new RSAOAEP()
        ]);

        $contentEncryptionAlgorithmManager = new AlgorithmManager([
            new A128CBCHS256(),
        ]);

        $compressionMethodManager = new CompressionMethodManager([
            new Deflate(),
        ]);

        return new JWEBuilder(
            $keyEncryptionAlgorithmManager,
            $contentEncryptionAlgorithmManager,
            $compressionMethodManager
        );
    }

    /**
     * JWEBuilder
     *
     * @return JWSBuilder
     */
    private function jwsBuilder(): JWSBuilder
    {
        $signatureAlgorithmManager = new AlgorithmManager([new RS256()]);

        return new JWSBuilder($signatureAlgorithmManager);
    }

    /**
     * JWSSerializerManager
     *
     * @return JWSSerializerManager
     */
    private function jwsSerializerManager(): JWSSerializerManager
    {
        return new JWSSerializerManager([new JWSCompactSerializer()]);
    }
}
