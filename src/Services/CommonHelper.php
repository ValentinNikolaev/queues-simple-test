<?php


namespace IWS\Queues\Services;

use Faker\Factory;

class CommonHelper
{
    public static function generateBunchFakeMessages($count = 2)
    {
        $result = [];
        for ($i = 0; $i < $count; $i++) {
            $result[] = self::generateFakeMessage();
        }

        return $result;
    }

    public static function generateFakeMessage()
    {
        $faker = Factory::create();
        return [
            'id' => $faker->randomNumber(),
            'from' => $faker->name,
            'country' => $faker->country,
            'payment' => $faker->creditCardDetails
        ];
    }

    public static function generateConsumerName()
    {
        $faker = Factory::create();
        return str_replace(" ", "_", $faker->name);
    }
}