<?php

namespace MrAndMrsSmith\SymfonyMessengerJSONSerializer\Tests\Serializer;

use MrAndMrsSmith\SymfonyMessengerJSONSerializer\Serializer\MessengerJSONSerializer;
use MrAndMrsSmith\SymfonyMessengerJSONSerializer\Tests\Dummy\DummyObject;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\MessageDecodingFailedException;
use Symfony\Component\Serializer\SerializerInterface;
use PHPUnit\Framework\TestCase;

class MessengerJSONSerializerTest extends TestCase
{

    private function getSerializerMock(): MockObject
    {
        return $this->createMock(SerializerInterface::class);
    }

    public function testDecodeFailNoBody()
    {
        $serializer = new MessengerJSONSerializer($this->getSerializerMock(), '');
        $this->expectException(MessageDecodingFailedException::class);
        $serializer->decode([]);
    }

    public function testDecodeSuccessWithoutStamps()
    {
        $mockedSerializer = $this->getSerializerMock();
        $mockedSerializer->method('deserialize')->willReturn(new DummyObject());
        $serializer = new MessengerJSONSerializer($mockedSerializer, DummyObject::class);

        $enveloppe = $serializer->decode(['body' => ['property' => 'value']]);

        $this->assertInstanceOf(DummyObject::class, $enveloppe->getMessage());
        $this->assertEmpty($enveloppe->all());
    }

    public function testEncode()
    {
        $mockedSerializer = $this->getSerializerMock();
        $mockedSerializer->method('serialize')->willReturn('SerilizedMessage');

        $enveloppe = new Envelope(new DummyObject());

        $serializer = new MessengerJSONSerializer($mockedSerializer, DummyObject::class);
        $encoded = $serializer->encode($enveloppe);

        $this->assertEquals('SerilizedMessage', $encoded['body']);
    }
}
