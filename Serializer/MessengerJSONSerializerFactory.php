<?php

namespace MrAndMrsSmith\SymfonyMessengerJSONSerializer\Serializer;

use Symfony\Component\Serializer\SerializerInterface;

class MessengerJSONSerializerFactory
{
    /**
     * @param $messageClassResolver string|MessageClassResolver
     */
    public static function create(
        SerializerInterface $serializer,
        $messageClassResolver
    ): MessengerJSONSerializer {
        if (is_string($messageClassResolver)) {
            if (!class_exists($messageClassResolver)) {
                throw new \InvalidArgumentException(
                    sprintf('The class "%s" does not exist.', $messageClassResolver)
                );
            }
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
