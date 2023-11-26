<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Author;
use App\Models\Category;
use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class ArticleController extends Controller
{


    public function fetch(): JsonResponse
    {
        $client = new Client();
        $guardianApiKey = '333ec1ad-5959-4675-a969-35fe4edd9932';
        $newsApiApiKey = 'c43de8a1904b4f349e0ebda45d132db1';
        $nytApiKey = 'y1x5W3aYXWjHN5l0acP6DHTHpmiypZJS';

        try {
            // Fetch news from The Guardian
            $guardianResponse = $client->get('https://content.guardianapis.com/search', [
                'query' => [
                    'api-key' => $guardianApiKey,
                ],
                'verify' => false,
            ]);

            $guardianResponseBody = json_decode($guardianResponse->getBody(), true);

            // Store articles from The Guardian
            if (isset($guardianResponseBody['response']['results'])) {
                $guardianArticles = $guardianResponseBody['response']['results'];

                foreach ($guardianArticles as $article) {
                    Article::create([
                        'title' => $article['webTitle'],
                        'content' => $article['webTitle'], // You might want to use a different key for content
                        'source_id' => 1, // Assume 'The Guardian' as source_id 1
                    ]);
                }

                Log::info('Guardian API Response: Data fetched and stored successfully');
            } else {
                Log::error('Guardian API Error: Unexpected response format');
            }

            // Fetch news from News API
            $newsApiResponse = $client->get('https://newsapi.org/v2/top-headlines', [
                'query' => [
                    'apiKey' => $newsApiApiKey,
                    'country' => 'us', // You can adjust parameters based on your requirements
                ],
                'verify' => false,
            ]);

            $newsApiResponseBody = json_decode($newsApiResponse->getBody(), true);

            // Store articles from News API
            if (isset($newsApiResponseBody['articles'])) {
                $newsApiArticles = $newsApiResponseBody['articles'];

                foreach ($newsApiArticles as $article) {
                    Article::create([
                        'title' => $article['title'],
                        'content' => $article['description'], // You might want to use a different key for content
                        'source_id' => 2, // Assume 'News API' as source_id 2
                    ]);
                }

                Log::info('News API Response: Data fetched and stored successfully');
            } else {
                Log::error('News API Error: Unexpected response format');
            }

            // Fetch news from New York Times
            $nytResponse = $client->get('https://api.nytimes.com/svc/topstories/v2/home.json', [
                'query' => [
                    'api-key' => $nytApiKey,
                ],
                'verify' => false,
            ]);

            $nytResponseBody = json_decode($nytResponse->getBody(), true);

            // Store articles from New York Times
            if (isset($nytResponseBody['results'])) {
                $nytArticles = $nytResponseBody['results'];

                foreach ($nytArticles as $article) {
                    Article::create([
                        'title' => $article['title'],
                        'content' => $article['abstract'], // You might want to use a different key for content
                        'source_id' => 3, // Assume 'New York Times' as source_id 3
                    ]);
                }

                Log::info('New York Times API Response: Data fetched and stored successfully');
            } else {
                Log::error('New York Times API Error: Unexpected response format');
            }

            return response()->json(['message' => 'Data fetched and stored successfully']);
        } catch (\Exception $e) {
            // Log error details
            Log::error('API Error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch data from one or more APIs'], 500);
        }
    }

    public function index(Request $request)
    {
        $query = Article::query();

        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->has('source_id')) {
            $query->where('source_id', $request->source_id);
        }

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('author_id')) {
            $query->where('author_id', $request->author_id);
        }

        // Add more filters based on your requirements

        $articles = $query->get();

        return response()->json($articles);
    }

    public function categories()
    {
        $categories = Category::all();

        return response()->json($categories);
    }

    public function authors()
    {
        $authors = Author::all();

        return response()->json($authors);
    }
}
