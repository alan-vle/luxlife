<?php

namespace App\Service\SignedUrl;

// use CoopTilleuls\UrlSignerBundle\UrlSigner\UrlSignerInterface;

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
    public static function getSignedUrlTilleulsBundle(string $url, string $expirationDate): void
    {
        $expiration = (new \DateTime('now'))->add(new \DateInterval($expirationDate));

        //        return self::$urlSigner->sign($url, $expiration);
    }

    public static function getSignedUrlBySpatieBundle(string $url, \DateTime|string $expirationDate): string
    {
        if (!$expirationDate instanceof \DateTime) {
            $expirationDate = (new \DateTime('now'))->add(new \DateInterval($expirationDate));
        }

        $urlSigner = new Sha256UrlSigner('A15EZEQS257854EZASDZZNJK');

        return $urlSigner->sign($url, $expirationDate);
    }
}
