<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Category;
use App\Models\Banner;
use App\Models\BlogPost;

class HomeController extends Controller
{
    /**
     * Page d'accueil avec formations en vedette et statistiques.
     */
    public function index()
    {
        $featuredCourses = Course::published()
            ->with(['category', 'instructor'])
            ->featured()
            ->take(6)
            ->get();

        $categories = Category::active()
            ->roots()
            ->ordered()
            ->withCount(['courses' => fn($q) => $q->published()])
            ->take(8)
            ->get();

        $banners = Banner::active()->hero()->orderBy('sort_order')->get();

        $latestPosts = BlogPost::published()
            ->with('author')
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('public.home', compact('featuredCourses', 'categories', 'banners', 'latestPosts'));
    }
}
