<?php

namespace Database\Seeders;

use App\Models\Source;
use App\Models\SourceFieldMapping;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NewsSourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         // News API
         $newsApi = Source::create([
            'name' => 'News Api',
            'type' => 'news_api',
            'api_key' => config('secret.news_api'),
            'api_key_param' => "apiKey",
            'method' => "get",
            'uses_header' => false,
            'default_params' => json_encode(["q"=>"news"]),
            'base_url' => 'https://newsapi.org/v2/everything'
        ]);

        // The News API field mappings
        $this->createFieldMappings($newsApi, [
            ['source_field' => 'title', 'target_field' => 'title'],
            ['source_field' => 'description', 'target_field' => 'summary'],
            ['source_field' => 'content', 'target_field' => 'content'],
            ['source_field' => 'url', 'target_field' => 'url'],
            ['source_field' => 'urlToImage', 'target_field' => 'image_url'],
            ['source_field' => 'author', 'target_field' => 'author'],
            ['source_field' => 'publishedAt', 'target_field' => 'published_at'],
            ['source_field' => 'id', 'target_field' => 'external_id', 'path' => 'source.id']
        ]);

        // The Guardian
        $guardian = Source::create([
            'name' => 'The Guardian',
            'type' => 'guardian',
            'has_pagination' => true,
            'api_key' => config('secret.guardian'),
            'api_key_param' => "api-key",
            'default_params' => null,
            'method' => "get",
            'uses_header' => false,
            'base_url' => 'https://content.guardianapis.com/search'
        ]);

        // The Guardian field mappings
        $this->createFieldMappings($guardian, [
            ['source_field' => 'webTitle', 'target_field' => 'title'],
            ['source_field' => 'id', 'target_field' => 'external_id'],
            ['source_field' => 'webUrl', 'target_field' => 'url'],
            ['source_field' => 'webPublicationDate', 'target_field' => 'published_at'],
            ['source_field' => 'sectionName', 'target_field' => 'category']
        ]);

        // World News
        $worldnews = Source::create([
            'name' => 'World News API',
            'api_key' => config('secret.world_news'),
            'type' => 'world_news_api',
            'api_key_param' => "api-key",
            'default_params' => json_encode(
                [
                    "source-country"=>"us",
                    "language"=>"en",
                    "date"=>"today",
                    ]
            ),
            'method' => "get",
            'uses_header' => false,
            'base_url' => 'https://api.worldnewsapi.com/top-news'
        ]);

        // World News field mappings
        $this->createFieldMappings($worldnews, [
            ['source_field' => 'title', 'target_field' => 'title'],
            ['source_field' => 'id', 'target_field' => 'external_id'],
            ['source_field' => 'text', 'target_field' => 'content'],
            ['source_field' => 'summary', 'target_field' => 'summary'],
            ['source_field' => 'url', 'target_field' => 'url'],
            ['source_field' => 'image', 'target_field' => 'image_url'],
            ['source_field' => 'author', 'target_field' => 'author'],
            ['source_field' => 'publish_date', 'target_field' => 'published_at'],
            ['source_field' => 'category', 'target_field' => 'category']
        ]);
    }

    private function createFieldMappings(Source $source, array $mappings)
    {
        foreach ($mappings as $mapping) {
            SourceFieldMapping::create([
                'source_id' => $source->id,
                'source_field' => $mapping['source_field'],
                'target_field' => $mapping['target_field'],
                'path' => $mapping['path'] ?? null
            ]);
        }
    }
}
