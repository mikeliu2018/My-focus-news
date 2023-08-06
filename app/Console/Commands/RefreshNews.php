<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Vedmant\FeedReader\Facades\FeedReader;
use App\Models\News;

class RefreshNews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'refresh-news';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh news by category';

    protected $resources = [];

    protected function setting()
    {
        $path = 'console/resources/news.json';
        // $storage_path = storage_path("app/$path");
        // Log::debug('storage_path', [$storage_path]);
        if (Storage::disk('local')->exists($path)) {
            $json = Storage::json($path);
            // Log::debug('json', $json);
            $this->resources = $json;
            return true;
        }
        return false;
    }

    protected function getImageUrl($url)
    {
        $imageUrl = "";
        $response = Http::get($url);
        if ($response->ok()) {
            // Log::debug("now get image url done: $url");
            $html = $response->body();
            $doc = new \DOMDocument();
            $doc->loadHTML($html, LIBXML_NOERROR);
            $xpath = new \DOMXPath($doc);
            $imageUrl = $xpath->query('*/meta[@property="og:image"]/@content')->item(0)->nodeValue;
        }
        return $imageUrl;
    }

    protected function getNews($resources)
    {
        $result = [];
        foreach ($this->resources as $resource) {
            $category = $resource['category'];
            $url = $resource['url'];
            $f = FeedReader::read($url);
            $items = $f->get_items();
            foreach ($items as $item) {
                $data['id'] = $item->get_id();
                $data['category'] = $category;
                $data['gmdate'] = $item->get_gmdate();
                $data['updated_gmdate'] = $item->get_updated_gmdate();
                $data['link'] = $item->get_link();
                $data['image'] = $this->getImageUrl($item->get_link());
                $data['title'] = $item->get_title();
                $data['content'] = $item->get_content();
                array_push($result, $data);
            }
        }
        return $result;
    }

    protected function saveNews($item)
    {
        if (isset($item['id']) && !empty($item['id'])) {
            $news = News::where('id', $item['id'])->firstOr(function () use ($item) {
                $news = new News();
                $news->id = $item['id'];
                $news->category = $item['category'];
                $news->gmdate = $item['gmdate'] && !empty($item['gmdate']) ? date_format(date_create($item['gmdate']), 'Y-m-d H:i:s') : NULL;
                $news->updated_gmdate = $item['updated_gmdate'] && !empty($item['updated_gmdate']) ? date_format(date_create($item['updated_gmdate']), 'Y-m-d H:i:s') : NULL;
                $news->link = $item['link'];
                $news->image = $item['image'];
                $news->title = $item['title'];
                $news->content = $item['content'];
                $news->save();
            });

            if ($news) {
                $news->id = $item['id'];
                $news->category = $item['category'];
                $news->gmdate = $item['gmdate'] && !empty($item['gmdate']) ? date_format(date_create($item['gmdate']), 'Y-m-d H:i:s') : NULL;
                $news->updated_gmdate = $item['updated_gmdate'] && !empty($item['updated_gmdate']) ? date_format(date_create($item['updated_gmdate']), 'Y-m-d H:i:s') : NULL;
                $news->link = $item['link'];
                $news->image = $item['image'];
                $news->title = $item['title'];
                $news->content = $item['content'];
                $news->save();
            }
        }
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Log::info("$this->signature start");
        if ($this->setting()) {
            // Log::debug('$this->resources', $this->resources);
            if ($this->resources) {
                $news = $this->getNews($this->resources);
                // Log::debug('$news', $news);
                foreach ($news as $item) {
                    $this->saveNews($item);
                }
            }
        }
        Log::info("$this->signature done");
    }
}
