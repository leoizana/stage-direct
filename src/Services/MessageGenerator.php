<?php

namespace App\Services;

class MessageGenerator{
    public function getRandomMessage(): string
    {
        $messages = [
            'Bonjour le monde !',
            'Comment ça va ?',
            'Bienvenue sur notre site !',
        ];

        return $messages[array_rand($messages)];
    }
}