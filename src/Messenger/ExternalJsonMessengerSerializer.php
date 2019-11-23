<?php


namespace App\Messenger;


use App\Message\Command\LogEmoji;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\MessageDecodingFailedException;
use Symfony\Component\Messenger\Stamp\BusNameStamp;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;

class ExternalJsonMessengerSerializer implements SerializerInterface
{

    /**
     * Decodes an envelope and its message from an encoded-form.
     *
     * The `$encodedEnvelope` parameter is a key-value array that
     * describes the envelope and its content, that will be used by the different transports.
     *
     * The most common keys are:
     * - `body` (string) - the message body
     * - `headers` (string<string>) - a key/value pair of headers
     *
     * @throws MessageDecodingFailedException
     */
    public function decode(array $encodedEnvelope): Envelope
    {
        $body = $encodedEnvelope['body'];
        $headers = $encodedEnvelope['headers'];

        $data = json_decode($body, true);

        if (null === $data) {
            throw new MessageDecodingFailedException('Invalid JSON');
        }

        if (!isset($data['emoji'])) {
            throw new MessageDecodingFailedException('Missing the emoji key!');
        }

        if (!isset($headers['type'])) {
            throw new MessageDecodingFailedException('Missing "type" header');
        }

        switch ($headers['type']) {
            case 'emoji':
                return $this->createLogEmojiEnvelope($data);
//            case 'delete_photo':
//                break;
        }

        throw new MessageDecodingFailedException(sprintf('Invalid type "%s"', $headers['type']));
    }

    /**
     * Encodes an envelope content (message & stamps) to a common format understandable by transports.
     * The encoded array should only contain scalars and arrays.
     *
     * Stamps that implement NonSendableStampInterface should
     * not be encoded.
     *
     * The most common keys of the encoded array are:
     * - `body` (string) - the message body
     * - `headers` (string<string>) - a key/value pair of headers
     */
    public function encode(Envelope $envelope): array
    {
        throw new \Exception('Transport & serializer not meant for sending messages.');
    }

    /**
     * @param array $data
     * @return Envelope
     */
    public function createLogEmojiEnvelope(array $data): Envelope
    {
        $message = new LogEmoji($data['emoji']);

        $envelope = new Envelope($message);
        $envelope = $envelope->with(new BusNameStamp('command.bus'));

        return $envelope;
    }
}
