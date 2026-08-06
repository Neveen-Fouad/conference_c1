<?php

namespace App\services;

class TransportationService
{
    /**
     * Create a new class instance.
     */

    protected $tripGoService;
    public function __construct( TransportationService $tripGoService)
    {
        $this->t = config('services.tripgo.key');


    }
}
