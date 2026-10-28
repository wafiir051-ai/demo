<?php

namespace App\Providers;

use App\Models\BusinessSetting;
use App\Models\Page;
use App\Support\Media;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Throwable;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        if (!class_exists('Media', false)) {
            class_alias(Media::class, 'Media');
        }

        Paginator::defaultView('pagination');

        View::composer('*', fn ($view) => $view->with('biz', BusinessSetting::current()));

        View::composer('layouts.public', function ($view) {
            try {
                $pages = Page::where('status', 'published')->where('slug', '!=', 'tentang')->orderBy('title')->get(['title', 'slug']);
            } catch (Throwable $e) {
                $pages = collect();
            }
            $view->with('footerPages', $pages);
        });
    }
}
