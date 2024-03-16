<?php

namespace App\EventSubscriber;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use Symfony\Component\HttpKernel\KernelEvents;

class CarEventSubscriber
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['convertStringToInt', EventPriorities::PRE_DESERIALIZE],
        ];
    }

    public function convertStringToInt(KernelEvents $event): void
    {
        dd('ass');
    }
}
