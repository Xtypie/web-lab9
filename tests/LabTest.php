<?php
namespace Tests;

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
        $this->assertEquals('rabbitmq', $_ENV['RABBITMQ_HOST']);
    }

    public function testMockGuzzleRequest()
    {
        $mock = new MockHandler([
            new Response(200, [], '{"status": "ok", "queue": "student_queue"}')
        ]);
        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $response = $client->get('http://mock/api/status');
        $this->assertEquals(200, $response->getStatusCode());
        
        $data = json_decode($response->getBody()->getContents(), true);
        $this->assertEquals('ok', $data['status']);
    }

    public function testQueueMessageFormatting()
    {
        $payload = [
            'name' => 'Student_17',
            'course' => 'PHP',
            'timestamp' => '2024-05-15'
        ];
        $encoded = json_encode($payload);
        $decoded = json_decode($encoded, true);

        $this->assertJson($encoded);
        $this->assertEquals('Student_17', $decoded['name']);
        $this->assertEquals('PHP', $decoded['course']);
    }
}