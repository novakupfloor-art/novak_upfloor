<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class MobileArticleController extends \App\Http\Controllers\Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $page = $request->get('page', 1);
            $perPage = $request->get('per_page', 10);
            $search = $request->get('search');
            $jenisBerita = $request->get('jenis_berita'); // Filter by jenis_berita

            // Hitung offset untuk pagination
            $offset = ($page - 1) * $perPage;

            // Query untuk mengambil data artikel dengan pagination
            $query = DB::table('berita')
                ->join('kategori', 'kategori.id_kategori', '=', 'berita.id_kategori', 'LEFT')
                ->join('users', 'users.id_user', '=', 'berita.id_user', 'LEFT')
                ->select(
                    'berita.id_berita as id',
                    'berita.judul_berita as title',
                    'berita.isi as content',
                    'berita.tanggal_publish as created_at',
                    'berita.gambar as image',
                    'berita.jenis_berita as type',
                    'berita.slug_berita as slug',
                    'berita.hits as view_count',
                    'berita.keywords',
                    'kategori.nama_kategori as category_name',
                    'users.nama as author_name'
                )
                ->where('berita.status_berita', 'Publish');

            // Filter by jenis_berita if specified (default to 'Berita' to exclude services)
            if ($jenisBerita) {
                $query->where('berita.jenis_berita', $jenisBerita);
            }

            // Add search functionality
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('berita.judul_berita', 'LIKE', '%' . $search . '%')
                      ->orWhere('berita.isi', 'LIKE', '%' . $search . '%')
                      ->orWhere('berita.keywords', 'LIKE', '%' . $search . '%');
                });
            }

            $articles = $query->orderBy('berita.tanggal_publish', 'desc')
                ->offset($offset)
                ->limit($perPage)
                ->get();

            // Hitung total artikel untuk pagination
            $totalQuery = DB::table('berita')->where('berita.status_berita', 'Publish');
            
            if ($jenisBerita) {
                $totalQuery->where('berita.jenis_berita', $jenisBerita);
            }
            
            if ($search) {
                $totalQuery->where(function($q) use ($search) {
                    $q->where('berita.judul_berita', 'LIKE', '%' . $search . '%')
                      ->orWhere('berita.isi', 'LIKE', '%' . $search . '%')
                      ->orWhere('berita.keywords', 'LIKE', '%' . $search . '%');
                });
            }
            
            $totalArticles = $totalQuery->count();

            // Hitung total halaman
            $totalPages = ceil($totalArticles / $perPage);

            // Transform data menggunakan metode transform seperti MobilePropertyController
            $transformedArticles = $articles->transform(function ($article) {
                // Generate full URL for article image using asset() helper
                $imageUrl = null;
                if ($article->image) {
                    $imageUrl = asset('assets/upload/image/' . $article->image);
                }

                return [
                    'id' => (int) $article->id,
                    'title' => $article->title,
                    'content' => $article->content,
                    'created_at' => $article->created_at,
                    'article_images' => $imageUrl,
                    'type' => $article->type,
                    'slug' => $article->slug,
                    'viewCount' => (int) $article->view_count,
                    'keywords' => $article->keywords,
                    'category' => $article->category_name,
                    'author' => $article->author_name
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Daftar artikel berhasil diambil',
                'data' => $transformedArticles,
                'current_page' => (int) $page,
                'last_page' => (int) $totalPages,
                'total' => (int) $totalArticles,
                'per_page' => (int) $perPage,
                'next_page_url' => $page < $totalPages ? $page + 1 : null
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching articles: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $article = DB::table('berita')
                ->join('kategori', 'kategori.id_kategori', '=', 'berita.id_kategori', 'LEFT')
                ->join('users', 'users.id_user', '=', 'berita.id_user', 'LEFT')
                ->select(
                    'berita.id_berita as id',
                    'berita.judul_berita as title',
                    'berita.isi as content',
                    'berita.tanggal_publish as created_at',
                    'berita.gambar as image',
                    'berita.jenis_berita as type',
                    'berita.slug_berita as slug',
                    'berita.hits as view_count',
                    'berita.keywords',
                    'kategori.nama_kategori as category_name',
                    'users.nama as author_name'
                )
                ->where('berita.id_berita', $id)
                ->where('berita.status_berita', 'Publish')
                ->first();

            if (!$article) {
                return response()->json([
                    'success' => false,
                    'message' => 'Artikel tidak ditemukan'
                ], 404);
            }

            // Transform data
            // Generate full URL for article image using asset() helper
            $imageUrl = null;
            if ($article->image) {
                $imageUrl = asset('assets/upload/image/' . $article->image);
            }

            $transformedArticle = [
                'id' => (int) $article->id,
                'title' => $article->title,
                'content' => $article->content,
                'created_at' => $article->created_at,
                'imageUrl' => $imageUrl,
                'type' => $article->type,
                'slug' => $article->slug,
                'viewCount' => (int) $article->view_count,
                'keywords' => $article->keywords,
                'category' => $article->category_name,
                'author' => $article->author_name
            ];

            return response()->json([
                'success' => true,
                'message' => 'Detail artikel berhasil diambil',
                'data' => $transformedArticle
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching article detail: ' . $e->getMessage()
            ], 500);
        }
    }
}
