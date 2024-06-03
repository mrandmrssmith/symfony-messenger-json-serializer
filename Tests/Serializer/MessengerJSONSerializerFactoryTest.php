<?php

namespace MrAndMrsSmith\SymfonyMessengerJSONSerializer\Tests\Serializer;

use MrAndMrsSmith\SymfonyMessengerJSONSerializer\Serializer\MessageClassResolver;
use MrAndMrsSmith\SymfonyMessengerJSONSerializer\Serializer\MessengerJSONSerializer;
use MrAndMrsSmith\SymfonyMessengerJSONSerializer\Serializer\MessengerJSONSerializerFactory;
use MrAndMrsSmith\SymfonyMessengerJSONSerializer\Tests\Dummy\DummyObject;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\SerializerInterface;

class MessengerJSONSerializerFactoryTest extends TestCase
{
    /** @var SerializerInterface|MockObject */
    private $serializer;

    public function setUp(): void
    {
        $this->serializer = $this->getMockForAbstractClass(SerializerInterface::class);
    }

    public function testWhenClassResolverAsArgument(): void
    {
        $classResolver = $this->getMockForAbstractClass(MessageClassResolver::class);
        $messageSerializer = MessengerJSONSerializerFactory::create($this->serializer, $classResolver);
        $this->assertInstanceOf(MessengerJSONSerializer::class, $messageSerializer);
    }

    public function testWhenClassFQCNAsArgument(): void
    {
        $messageSerializer = MessengerJSONSerializerFactory::create($this->serializer, DummyObject::class);
        $this->assertInstanceOf(MessengerJSONSerializer::class, $messageSerializer);
    }

    public function testWhenClassFQCNAsArgumentIfClasNotExists(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        MessengerJSONSerializerFactory::create($this->serializer, 'NonExistingClass');
    }

    public function testWhenObjectAsArgument(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        MessengerJSONSerializerFactory::create($this->serializer, new \stdClass());
    }
}
