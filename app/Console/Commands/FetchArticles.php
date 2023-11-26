<?php

namespace App\Console\Commands;

use App\Models\Article;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class FetchArticles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fetch-articles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $response = Http::get('https://newsapi.org/v2/top-headlines', [
            'apiKey' => 'your_news_api_key',
            'sources' => 'bbc-news,the-guardian',
        ]);

        $articles = $response->json()['articles'];

        foreach ($articles as $article) {
            Article::updateOrCreate(
                ['title' => $article['title']],
                [
                    'content' => $article['description'],
                    'source_id' => 1, // Assume 'The Guardian' as source_id 1
                ]
            );
        }

        $this->info('Articles fetched and updated successfully');
    }
    

}
