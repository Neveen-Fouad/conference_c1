<?php

namespace App\services;

class GroqService
{
    /**
     * Create a new class instance.
     */
    // public function __construct()
    // {
    //     //
    // }

    function MakeTrip(){
        groq::chat()->completions()->create([
            "model"=> config("model"),
            
        ]);
    }
}
