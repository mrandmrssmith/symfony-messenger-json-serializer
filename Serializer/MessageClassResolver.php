<?php

namespace MrAndMrsSmith\SymfonyMessengerJSONSerializer\Serializer;

interface MessageClassResolver
{
    public function resolveClass(array $encodedEnvelope): string;
}