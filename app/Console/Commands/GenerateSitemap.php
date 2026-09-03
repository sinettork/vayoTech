<?php

namespace App\Console\Commands;

use App\Models\Device;
use App\Models\NewsPost;
use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';

    protected $description = 'Generate the public XML sitemap.';

    public function handle(): int
    {
        $sitemap = Sitemap::create()
            ->add(Url::create(route('home'))->setPriority(1.0))
            ->add(Url::create(route('devices.index'))->setPriority(0.9))
            ->add(Url::create(route('news.index'))->setPriority(0.8))
            ->add(Url::create(route('compare.index'))->setPriority(0.5))
            ->add(Url::create(route('privacy'))->setPriority(0.2))
            ->add(Url::create(route('terms'))->setPriority(0.2));

        Device::query()->cursor()->each(function (Device $device) use ($sitemap): void {
            $sitemap->add(
                Url::create(route('devices.show', $device))
                    ->setLastModificationDate($device->updated_at)
                    ->setPriority(0.7),
            );
        });

        NewsPost::query()->whereNotNull('published_at')->cursor()->each(function (NewsPost $post) use ($sitemap): void {
            $sitemap->add(
                Url::create(route('news.show', $post))
                    ->setLastModificationDate($post->updated_at)
                    ->setPriority(0.6),
            );
        });

        $sitemap->writeToFile(public_path('sitemap.xml'));

        $this->info('Sitemap generated successfully.');

        return self::SUCCESS;
    }
}
