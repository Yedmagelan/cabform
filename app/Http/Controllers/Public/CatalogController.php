<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Category;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    /**
     * Catalogue des formations avec filtres Ajax.
     */
    public function index(Request $request)
    {
        $categories = Category::active()->roots()->ordered()->get();

        $query = Course::published()->with(['category', 'instructor']);

        // Recherche
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filtre catégorie
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filtre niveau
        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }

        // Filtre prix
        if ($request->input('filter') === 'free') {
            $query->free();
        } elseif ($request->input('filter') === 'certified') {
            $query->where('is_certified', true);
        }

        // Filtre prix range
        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }
        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }

        // Tri
        $sort = $request->input('sort', 'latest');
        $query = match($sort) {
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'rating' => $query->orderByDesc('rating'),
            'popular' => $query->orderByDesc('enrollment_count'),
            default => $query->latest('published_at'),
        };

        $courses = $query->paginate(config('cabform.pagination.catalog', 12));

        // Retourner en JSON pour les requêtes Ajax
        if ($request->ajax()) {
            return response()->json([
                'html' => view('public.partials.course-grid', compact('courses'))->render(),
                'pagination' => $courses->links()->toHtml(),
                'total' => $courses->total(),
            ]);
        }

        return view('public.catalog', compact('courses', 'categories'));
    }
}
