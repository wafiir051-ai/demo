<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Accommodation;
use App\Models\Facility;
use App\Models\Gallery;
use App\Models\Package;
use App\Models\Page;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    public function home()
    {
        return view('site.home', [
            'accommodations' => Accommodation::published()->limit(6)->get(),
            'facilities' => Facility::published()->limit(8)->get(),
            'packages' => Package::published()->with('items')->limit(3)->get(),
            'gallery' => Gallery::published()->limit(6)->get(),
        ]);
    }

    public function accommodations()
    {
        return view('site.accommodations', ['items' => Accommodation::published()->with('facilities')->get()]);
    }

    public function accommodation(Accommodation $accommodation)
    {
        abort_unless($accommodation->status === 'published', 404);
        $accommodation->load('facilities', 'images');

        return view('site.accommodation', ['a' => $accommodation, 'accommodations' => Accommodation::published()->get()]);
    }

    public function facilities()
    {
        return view('site.facilities', ['items' => Facility::published()->get()]);
    }

    public function packages()
    {
        return view('site.packages', ['items' => Package::published()->with('items')->get()]);
    }

    public function gallery(Request $r)
    {
        $cats = Gallery::published()->whereNotNull('category')->distinct()->pluck('category');
        $q = Gallery::published();
        $active = $cats->contains($r->get('kategori')) ? $r->get('kategori') : null;
        if ($active) {
            $q->where('category', $active);
        }

        return view('site.gallery', ['items' => $q->get(), 'cats' => $cats, 'active' => $active]);
    }

    public function about()
    {
        return view('site.about', ['page' => Page::where('slug', 'tentang')->where('status', 'published')->first()]);
    }

    public function page(Page $page)
    {
        abort_unless($page->status === 'published', 404);

        return view('site.page', ['page' => $page]);
    }
}
