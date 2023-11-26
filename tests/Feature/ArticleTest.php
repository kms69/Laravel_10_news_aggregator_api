<?php

namespace Tests\Feature;

use App\Models\Author;
use App\Models\Category;
use GuzzleHttp\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;


class ArticleTest extends TestCase
{
    use RefreshDatabase;



    public function test_can_fetch_articles_from_guardian()
    {
        $this->artisan('migrate:refresh');

        try {
            // Use Guzzle directly to make the request
            $client = new Client([
                'verify' => false, // Disable SSL verification for local testing
            ]);

            $response = $client->get('https://content.guardianapis.com/search', [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
                'query' => [
                    'api-key' => '333ec1ad-5959-4675-a969-35fe4edd9932', // Update with your Guardian API key
                    'section' => 'world', // Add any other necessary parameters here
                ],
            ]);

            // Assert that the response was successful (status code 200)
            $this->assertEquals(200, $response->getStatusCode());

            // Assert that the response has the expected structure
            $responseData = json_decode($response->getBody()->getContents(), true);

            $this->assertArrayHasKey('response', $responseData);
            // Add more assertions based on the Guardian API response structure

            // Continue with the rest of your test logic...

        } catch (\Exception $e) {
            // Log error details
            Log::error('Test Error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function test_can_fetch_articles_from_newsapi()
    {
        $this->artisan('migrate:refresh');

        try {
            // Use Guzzle directly to make the request
            $client = new Client([
                'verify' => false, // Disable SSL verification for local testing
            ]);

            $response = $client->get('https://newsapi.org/v2/top-headlines', [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
                'query' => [
                    'apiKey' => 'c43de8a1904b4f349e0ebda45d132db1',
                    'country' => 'us', // Add any other necessary parameters here
                ],
            ]);

            // Assert that the response was successful (status code 200)
            $this->assertEquals(200, $response->getStatusCode());

            // Assert that the response has the expected structure
            $responseData = json_decode($response->getBody()->getContents(), true);

            $this->assertArrayHasKey('status', $responseData);
            $this->assertEquals('ok', $responseData['status']);

            // Continue with the rest of your test logic...

        } catch (\Exception $e) {
            // Log error details
            Log::error('Test Error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function test_can_fetch_articles_from_nyt()
    {
        $this->artisan('migrate:refresh');

        try {
            // Use Guzzle directly to make the request
            $client = new Client([
                'verify' => false, // Disable SSL verification for local testing
                'timeout' => 30, // Set timeout to 30 seconds
            ]);

            $response = $client->get('https://api.nytimes.com/svc/topstories/v2/world.json', [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
                'query' => [
                    'api-key' => 'y1x5W3aYXWjHN5l0acP6DHTHpmiypZJS', // Update with your NYT API key
                ],
            ]);

            // Assert that the response was successful (status code 200)
            $this->assertEquals(200, $response->getStatusCode());

            // Assert that the response has the expected structure
            $responseData = json_decode($response->getBody()->getContents(), true);

            $this->assertArrayHasKey('status', $responseData);
            $this->assertEquals('OK', $responseData['status']);


        } catch (\Exception $e) {
            // Log error details
            Log::error('Test Error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function test_can_fetch_categories()
    {
        // Test fetching categories
        $response = $this->get('/api/articles/categories');
        $response->assertStatus(200);
        // Add more assertions based on your data
    }

    public function test_can_fetch_authors()
    {
        // Test fetching authors
        $response = $this->get('/api/articles/authors');
        $response->assertStatus(200);
        // Add more assertions based on your data
    }

    public function test_can_fetch_filtered_articles()
    {
        // Create sample articles
        $category = Category::first();
        $author = Author::first();

        // Mock the HTTP client to simulate the API response
        $response = $this->get('/api/articles', [
            'category_id' => $category->id,
            'author_id' => $author->id,
        ]);

        // Assert that the response was successful (status 200)
        $response->assertStatus(200);


        // Check if the response is an array
        $this->assertIsArray($response->json());

        // If the array is not empty, check 'message' key
        if (!empty($response->json())) {
            // If 'message' key exists, assert its value
            if (isset($response->json()[0]['message'])) {
                $this->assertEquals('Data fetched and stored successfully', $response->json()[0]['message']);
            }
        }
    }

    protected function setUp(): void
    {
        parent::setUp();

        // Run migrations and seeders
        Artisan::call('migrate');
        Artisan::call('db:seed', ['--class' => 'CategorySeeder']);
        Artisan::call('db:seed', ['--class' => 'AuthorSeeder']);
    }

    protected function tearDown(): void
    {
        // Rollback migrations
        Artisan::call('migrate:rollback');

        parent::tearDown();
    }
}
