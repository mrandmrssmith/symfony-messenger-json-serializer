<?php

namespace MrAndMrsSmith\SymfonyMessengerJSONSerializer\Serializer;

class DefaultMessageClassResolver implements MessageClassResolver
{
    private $messageClass;

    public function __construct(
      string $messageClass
    ) {
        $this->messageClass = $messageClass;
    }

    public function resolveClass(array $encodedEnvelope): string
    {
        return $this->messageClass;
    }
}