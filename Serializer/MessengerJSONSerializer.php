<?php

namespace MrAndMrsSmith\SymfonyMessengerJSONSerializer\Serializer;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\MessageDecodingFailedException;
use Symfony\Component\Messenger\Stamp\NonSendableStampInterface;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface as MessageSerializerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class MessengerJSONSerializer implements MessageSerializerInterface
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var string
     */
    private $messageClass;

    public function __construct(
        SerializerInterface $serializer,
        string $messageClass
    ) {
        $this->serializer = $serializer;
        $this->messageClass = $messageClass;
    }

    public function decode(array $encodedEnvelope): Envelope
    {
        if (! isset($encodedEnvelope['body'])) {
            throw new MessageDecodingFailedException('Encoded envelope should have body');
        }
        try {
            $message = $this->serializer->deserialize(
                $encodedEnvelope['body'],
                $this->messageClass,
                'json'
            );
            if (isset($encodedEnvelope['headers']['stamps'])) {
                $stamps = $this->decodeStamps(json_decode($encodedEnvelope['headers']['stamps'], true));
            }
        } catch (\Throwable $exception) {
            throw new MessageDecodingFailedException($exception->getMessage(), 0, $exception);
        }

        return new Envelope($message, $stamps ?? []);
    }

    public function encode(Envelope $envelope): array
    {
        $message = $envelope->getMessage();

        return [
            'body' => $this->serializer->serialize($message, 'json'),
            'headers' => [
                'stamps' => json_encode($this->encodeStamps($envelope))
            ]
        ];
    }

    private function encodeStamps(Envelope $envelope): array
    {
        $envelope = $envelope->withoutStampsOfType(NonSendableStampInterface::class);
        $allStamps = [];
        foreach ($envelope->all() as $stamps) {
            $allStamps = array_merge($allStamps, (array) $stamps);
        }
        foreach ($allStamps as $key => $stamps) {
            if (! is_array($stamps)) {
                continue;
            }
            unset($allStamps[$key]);
            $allStamps = array_merge($allStamps, $stamps);
        }

        $serializedStamps = [];
        foreach ($allStamps as $stamp) {
            $serializedStamps[get_class($stamp)][] = json_decode(
                $this->serializer->serialize($stamp, 'json'),
                true
            );
        }

        return $serializedStamps;
    }

    private function decodeStamps(array $stamps): array
    {
        $decodedStamps = [];
        foreach ($stamps as $stampType => $stampsOfType) {
            if (! class_exists($stampType)) {
                continue;
            }
            foreach ($stampsOfType as $stamp) {
                if (! is_string(json_encode($stamp))) {
                    continue;
                }
                $decodedStamps[] = $this->serializer->deserialize(
                    json_encode($stamp),
                    $stampType,
                    'json'
                );
            }
        }

        return $decodedStamps;
    }
}
