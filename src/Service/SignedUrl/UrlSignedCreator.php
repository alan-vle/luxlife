<?php

namespace App\Service\SignedUrl;

use Spatie\UrlSigner\Exceptions\InvalidSignatureKey;
use Spatie\UrlSigner\Sha256UrlSigner;
use Symfony\Component\Uid\Uuid;

class UrlSignedCreator
{
    //    private static UrlSignerInterface $urlSigner;
    public function __construct(
    ) {
    }

    /**
     * @throws \Exception
     */
    public static function getSignedUrlTilleulsBundle(string $url, string $expirationDate): void
    {
        $expiration = (new \DateTime('now'))->add(new \DateInterval($expirationDate));

        //        return self::$urlSigner->sign($url, $expiration);
    }

    /**
     * @throws InvalidSignatureKey
     * @throws \Exception
     */
    public static function getSignedUrlBySpatieBundle(string $url, \DateTime|string $expirationDate, Uuid|string|null $secretKey): string
    {
        if (!$expirationDate instanceof \DateTime) {
            $expirationDate = (new \DateTime('now'))->add(new \DateInterval($expirationDate));
        }

        $urlSigner = new Sha256UrlSigner((string) $secretKey);

        return $urlSigner->sign($url, $expirationDate);
    }
}
