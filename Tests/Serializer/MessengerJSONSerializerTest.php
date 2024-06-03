<?php

namespace MrAndMrsSmith\SymfonyMessengerJSONSerializer\Tests\Serializer;

use MrAndMrsSmith\SymfonyMessengerJSONSerializer\Serializer\MessageClassResolver;
use MrAndMrsSmith\SymfonyMessengerJSONSerializer\Serializer\MessengerJSONSerializer;
use MrAndMrsSmith\SymfonyMessengerJSONSerializer\Tests\Dummy\DummyObject;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\MessageDecodingFailedException;
use Symfony\Component\Serializer\SerializerInterface;

class MessengerJSONSerializerTest extends TestCase
{
    /**
     * @var SerializerInterface|MockObject
     */
    private $serializer;

    /**
     * @var MessageClassResolver|MockObject
     */
    private $classResolver;

    /**
     * @var MessengerJSONSerializer
     */
    private $messageSerializer;

    public function setUp(): void
    {
        $this->serializer = $this->createMock(SerializerInterface::class);
        $this->classResolver = $this->getMockForAbstractClass(MessageClassResolver::class);

        $this->messageSerializer = new MessengerJSONSerializer($this->serializer, $this->classResolver);
    }
    public function testDecodeFailNoBody(): void
    {
        $this->expectException(MessageDecodingFailedException::class);

        $this->messageSerializer->decode([]);
    }

    public function testDecodeFailDeserializationException(): void
    {
        $this->classResolver
            ->expects($this->once())
            ->method('resolveClass')
            ->willReturn(DummyObject::class);
        $this->serializer
            ->expects($this->once())
            ->method('deserialize')
            ->willThrowException(new \Exception());

        $this->expectException(MessageDecodingFailedException::class);
        $this->messageSerializer->decode(['body' => ['property' => 'value']]);
    }

    public function testDecodeSuccessWithoutStamps(): void
    {
        $this->classResolver
            ->expects($this->once())
            ->method('resolveClass')
            ->willReturn(DummyObject::class);
        $this->serializer
            ->expects($this->once())
            ->method('deserialize')
            ->willReturn(new DummyObject());

        $envelope = $this->messageSerializer->decode(['body' => ['property' => 'value']]);

        $this->assertInstanceOf(DummyObject::class, $envelope->getMessage());
        $this->assertEmpty($envelope->all());
    }

    public function testDecodeSuccessWithStamps(): void
    {
        $this->classResolver
            ->expects($this->once())
            ->method('resolveClass')
            ->willReturn(DummyObject::class);
        $this->serializer
            ->method('deserialize')
            ->willReturnMap(
                [
                    [['property' => 'value'], DummyObject::class, 'json', new DummyObject()],
                    [json_encode([]), DummyObject::class, 'json', ['messenger_serialization' => true]],
                ]
            )
            ->willReturn(new DummyObject());

        $envelope = $this->messageSerializer->decode(
            [
                'body' => '{"property": "value"}',
                'headers' => [
                    'stamps' => json_encode(
                        [
                            'UnkownStampType' => [],
                            DummyObject::class => [[]],
                        ]
                    )
                ]
            ]
        );

        $this->assertInstanceOf(DummyObject::class, $envelope->getMessage());
        $this->assertCount(1, $envelope->all());
    }

    public function testEncode(): void
    {
        $this->serializer
            ->expects($this->once())
            ->method('serialize')
            ->willReturn('SerilizedMessage');

        $envelope = new Envelope(new DummyObject());

        $encoded = $this->messageSerializer->encode($envelope);

        $this->assertEquals('SerilizedMessage', $encoded['body']);
    }
}
