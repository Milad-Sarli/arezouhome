<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductQuestion;
use Illuminate\Http\Request;

class ProductQuestionController extends Controller
{
    public function index(string $slug)
    {
        $product = Product::where('slug', $slug)->where('status', true)->firstOrFail();

        $rows = ProductQuestion::where('product_id', $product->id)
            ->where('status', true)
            ->with('user')
            ->latest()
            ->paginate(10)
            ->through(fn ($q) => $this->row($q));

        return $rows;
    }

    public function store(Request $request, string $slug)
    {
        $product = Product::where('slug', $slug)->where('status', true)->firstOrFail();

        $data = $request->validate([
            'question' => ['required', 'string', 'max:1000'],
        ]);

        $clean = trim(strip_tags($data['question']));
        if (mb_strlen($clean) < 5) {
            return response()->json(['message' => 'سوال کوتاه است.'], 422);
        }

        // simple profanity/URL spam check
        if (preg_match('#https?://#i', $clean)) {
            return response()->json(['message' => 'لینک در سوال مجاز نیست.'], 422);
        }

        $recent = $request->user()->productQuestions()->where('product_id', $product->id)->where('created_at', '>', now()->subMinutes(5))->exists();
        if ($recent) {
            return response()->json(['message' => 'هر ۵ دقیقه فقط یک سوال می‌تونی بپرسی.'], 429);
        }

        $q = $request->user()->productQuestions()->create([
            'product_id' => $product->id,
            'question' => $clean,
            'status' => false,
        ]);

        return response()->json(['message' => 'سوالت ثبت شد؛ بعد از تأیید نمایش داده می‌شود.'], 201);
    }

    private function row(ProductQuestion $q): array
    {
        return [
            'id' => $q->id,
            'user_name' => $q->user?->name ?? 'کاربر',
            'question' => $q->question,
            'answer' => $q->answer,
            'is_answered' => (bool) $q->is_answered,
            'created_at' => $q->created_at?->toIso8601String(),
        ];
    }
}
