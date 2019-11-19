<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Messenger\Transport\InMemoryTransport;

class ImagePostControllerTest extends WebTestCase
{
    /** @test */
    public function it_creates_image(): void
    {
        $client = static::createClient();

        $uploadedFile = new UploadedFile(__DIR__.'/../fixtures/melvin.png', 'melvin.png');

        $client->request('POST', '/api/images', [], [
            'file' => $uploadedFile
        ]);

        self::assertResponseIsSuccessful();

        /** @var InMemoryTransport $transport */
        $transport = self::$container->get('messenger.transport.async_priority_high');
        $this->assertCount(1, $transport->get());
    }
}