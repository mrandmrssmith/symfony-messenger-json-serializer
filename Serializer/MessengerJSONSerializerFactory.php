<?php

namespace MrAndMrsSmith\SymfonyMessengerJSONSerializer\Serializer;

use Symfony\Component\Serializer\SerializerInterface;

class MessengerJSONSerializerFactory
{
    public static function create(
        SerializerInterface $serializer,
        $messageClassResolver
    ): MessengerJSONSerializer {
        if (is_string($messageClassResolver)) {
            $messageClassResolver = new DefaultMessageClassResolver($messageClassResolver);
        }
        if (!$messageClassResolver instanceof MessageClassResolver) {
            throw new \InvalidArgumentException(
                'The class resolver must be an instance of MessageClassResolver or a class name.'
            );
        }

        return new MessengerJSONSerializer($serializer, $messageClassResolver);
    }
}