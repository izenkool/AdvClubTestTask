<?php

declare(strict_types=1);

namespace App\Tests\Web;

use PHPUnit\Framework\Attributes\CoversNothing;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class travelCostTest extends WebTestCase
{
    #[CoversNothing]
    public function testTravelCostEndpoint(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/v1/travel/cost?cost=100&birthDate=2020-10-18');

        $this->assertResponseIsSuccessful();
        $response = $client->getResponse();
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertJson($response->getContent());
    }
}
