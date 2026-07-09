<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Faq;
use App\Models\BlogPost;
use App\Models\Certificate;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function about() { return view('public.about'); }

    public function contact() { return view('public.contact'); }

    public function contactSubmit(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
        ]);

        // TODO: Send email notification
        return response()->json(['success' => true, 'message' => 'Votre message a été envoyé avec succès. Nous vous répondrons dans les plus brefs délais.']);
    }

    public function faq()
    {
        $faqs = Faq::active()->orderBy('sort_order')->get()->groupBy('category');
        return view('public.faq', compact('faqs'));
    }


    public function page($slug)
    {
        $page = Page::where('slug', $slug)->published()->firstOrFail();
        return view('public.page', compact('page'));
    }

    public function verifyCertificate(Request $request)
    {
        $certificate = null;
        if ($request->filled('code')) {
            $certificate = Certificate::where('certificate_number', $request->code)
                ->orWhere('hash', $request->code)
                ->with(['user', 'course'])
                ->first();
        }
        return view('public.verify-certificate', compact('certificate'));
    }

    public function blog()
    {
        $posts = BlogPost::published()->with(['author', 'category'])->latest('published_at')->paginate(config('cabform.pagination.blog', 9));
        return view('public.blog.index', compact('posts'));
    }

    public function blogShow($slug)
    {
        $post = BlogPost::where('slug', $slug)->published()->with(['author', 'category'])->firstOrFail();
        $post->increment('views_count');
        $relatedPosts = BlogPost::published()->where('id', '!=', $post->id)->where('category_id', $post->category_id)->take(3)->get();
        return view('public.blog.show', compact('post', 'relatedPosts'));
    }
}
