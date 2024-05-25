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
        // This calls KernelTestCase::bootKernel(), and creates a
        // "client" that is acting as the browser
        $client = static::createClient();

        // Request a specific page
        $crawler = $client->request('GET', '/api/v1/travel/cost?cost=100&birthDate=2020-10-18');

        // Validate a successful response and some content
        $this->assertResponseIsSuccessful();
//        $this->assertSelectorTextContains('h1', 'Hello World');

//        $this->assertGreaterThan(0, $crawler->filter('html:contains("Hello World")')->count());
    }
}
