<?php

namespace App\Service\SignedUrl;

//use CoopTilleuls\UrlSignerBundle\UrlSigner\UrlSignerInterface;

use Spatie\UrlSigner\Sha256UrlSigner;

class UrlSignedCreator
{
//    private static UrlSignerInterface $urlSigner;
    public function __construct(
    ) {
    }

    /**
     * @throws \Exception
     */
    public static function getSignedUrlTilleulsBundle(string $url, string $expiration): void
    {
        $expiration = (new \DateTime('now'))->add(new \DateInterval($expiration));

//        return self::$urlSigner->sign($url, $expiration);
    }

    public static function getSignedUrlBySpatieBundle(string $url, string $expirationDate): string
    {
        $urlSigner = new Sha256UrlSigner('A15EZEQS257854EZASDZZNJK');

        return $urlSigner->sign($url, $expirationDate);
    }
}
