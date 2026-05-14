<?php
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

class LabTest extends TestCase
{
    public function testEnvironmentVariablesAreLoaded()
    {
        $this->assertArrayHasKey('RABBITMQ_HOST', $_ENV);
    }

    public function testMockGuzzleRequest()
    {
        $mock = new MockHandler([
            new Response(200, [], '{"status": "ok"}')
        ]);
        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $response = $client->get('http://mock/api');
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testQueueMessageFormatting()
    {
        $payload = ['name' => 'Student', 'course' => 'PHP'];
        $encoded = json_encode($payload);
        $decoded = json_decode($encoded, true);

        $this->assertJson($encoded);
        $this->assertEquals('Student', $decoded['name']);
    }
}