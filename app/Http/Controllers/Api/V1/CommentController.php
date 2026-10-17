<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index(Request $request, string $slug)
    {
        $product = Product::where('slug', $slug)->where('status', true)->firstOrFail();

        $base = Comment::where('product_id', $product->id)
            ->where('status', true)
            ->whereNull('parent_id');

        $ratings = (clone $base)->pluck('rating');
        $dist = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];

        foreach ($ratings as $r) {
            if (isset($dist[(int) $r])) {
                $dist[(int) $r]++;
            }
        }

        $comments = (clone $base)->with('user')->latest()->paginate(10)
            ->through(fn ($c) => $this->row($c, $product->id));

        $out = $comments->toArray();
        $out['summary'] = [
            'count' => $ratings->count(),
            'avg' => $ratings->count() > 0 ? round($ratings->avg(), 1) : null,
            'dist' => $dist,
        ];

        return response()->json($out);
    }

    public function store(Request $request, string $slug)
    {
        $product = Product::where('slug', $slug)->where('status', true)->firstOrFail();

        $data = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $clean = trim(strip_tags($data['body']));
        if (mb_strlen($clean) < 3) {
            return response()->json(['message' => 'متن نظر کوتاه است.'], 422);
        }
        if (preg_match('#https?://#i', $clean)) {
            return response()->json(['message' => 'لینک در نظر مجاز نیست.'], 422);
        }

        $request->user()->comments()->create([
            'product_id' => $product->id,
            'rating' => $data['rating'],
            'body' => $clean,
            'status' => false,
        ]);

        return response()->json(['message' => 'نظرت ثبت شد؛ بعد از تأیید نمایش داده می‌شود.'], 201);
    }

    private function row(Comment $comment, int $productId): array
    {
        $verified = $comment->user_id
            ? Order::where('user_id', $comment->user_id)
                ->whereIn('status', ['paid', 'processing', 'shipped', 'delivered'])
                ->whereHas('items', fn ($q) => $q->where('product_id', $productId))
                ->exists()
            : false;

        return [
            'id' => $comment->id,
            'user_name' => $comment->user?->name ?? 'کاربر',
            'rating' => (int) $comment->rating,
            'body' => $comment->body,
            'verified' => $verified,
            'created_at' => $comment->created_at?->toIso8601String(),
        ];
    }
}
