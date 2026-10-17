<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\CategoryResource;
use App\Http\Resources\Api\V1\ProductResource;
use App\Models\Amazing;
use App\Models\Blog;
use App\Models\Brand;
use App\Models\Category;
use App\Models\DiscountCode;
use App\Models\InstallmentPlan;
use App\Models\NewsletterSubscription;
use App\Models\Product;
use App\Models\Province;
use App\Models\Setting;
use App\Models\Shipment;
use App\Models\Slider;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function home()
    {
        $products = Product::query()
            ->with(['brand', 'warranty', 'categories'])
            ->where('status', true)
            ->where('in_stock', true)
            ->where('quantity', '>', 0)
            ->latest()
            ->take(8)
            ->get();

        $now = now();
        $amazings = Amazing::with('product.brand')
            ->where('status', true)
            ->where(fn ($q) => $q->whereNull('start_at')->orWhere('start_at', '<=', $now))
            ->where(fn ($q) => $q->whereNull('end_at')->orWhere('end_at', '>=', $now))
            ->get();

        return response()->json([
            'categories' => ['data' => $this->categoriesData()],
            'brands' => $this->brandsData(),
            'settings' => $this->settingsData(),
            'amazings' => $amazings->map(fn ($a) => [
                'id' => $a->id,
                'product' => (new ProductResource($a->product))->toArray(request()),
                'percent' => $a->percent,
                'price_rial' => $a->price_rial ? (int) $a->price_rial : null,
                'end_at' => $a->end_at?->toIso8601String(),
            ])->values(),
            'products' => ['data' => ProductResource::collection($products)->toArray(request())],
            'sliders' => $this->slidersData(),
            'blogs' => Blog::where('status', true)
                ->with('category')
                ->latest('publish_at')
                ->take(3)
                ->get()
                ->map(fn ($b) => $this->blogRow($b)),
        ]);
    }

    private function categoriesData(): array
    {
        return cache()->remember('api.categories', 3600, function () {
            $roots = Category::where('status', true)
                ->whereNull('parent_id')
                ->with('children.children')
                ->orderBy('sort')
                ->get();

            return $this->plain(CategoryResource::collection($roots)->toArray(request()));
        });
    }

    private function brandsData(): array
    {
        return cache()->remember('api.brands', 3600, fn () => Brand::where('status', true)->orderBy('name')->get(['id', 'name', 'slug', 'logo'])->toArray());
    }

    private function slidersData(): array
    {
        return cache()->remember('api.sliders', 3600, fn () => Slider::where('status', true)
            ->with(['slides' => fn ($q) => $q->where('status', true)->orderBy('sort')])
            ->orderBy('sort')
            ->get()
            ->toArray());
    }

    /**
     * Force nested API resources into plain arrays so the payload
     * survives cache serialization.
     */
    private function plain(mixed $data): array
    {
        return json_decode(json_encode($data), true) ?? [];
    }

    public function categories()
    {
        return response()->json(['data' => $this->categoriesData()]);
    }

    public function brands()
    {
        return $this->brandsData();
    }

    public function products(Request $request)
    {
        $query = Product::query()
            ->with(['brand', 'warranty', 'categories'])
            ->where('status', true);

        if ($request->filled('category_id')) {
            $query->whereHas('categories', fn ($q) => $q->where('categories.id', $request->integer('category_id')));
        }

        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->integer('brand_id'));
        }

        if ($request->filled('ids')) {
            $ids = collect(explode(',', $request->string('ids')))
                ->map(fn ($id) => (int) $id)
                ->filter(fn ($id) => $id > 0)
                ->take(20)
                ->values();
            $query->whereIn('id', $ids);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%'.$request->string('search').'%');
        }

        if ($request->filled('min_price')) {
            $query->where('price_rial', '>=', $request->integer('min_price'));
        }

        if ($request->filled('max_price')) {
            $query->where('price_rial', '<=', $request->integer('max_price'));
        }

        if ($request->boolean('in_stock')) {
            $query->where('in_stock', true)->where('quantity', '>', 0);
        }

        match ($request->string('sort', 'newest')) {
            'cheapest' => $query->orderBy('price_rial'),
            'expensive' => $query->orderByDesc('price_rial'),
            default => $query->latest(),
        };

        return ProductResource::collection($query->paginate(min($request->integer('per_page', 24) ?: 24, 24)));
    }

    public function product(string $slug)
    {
        $product = Product::with(['brand', 'warranty', 'categories', 'variants.color', 'specs.attr'])
            ->where('slug', $slug)
            ->where('status', true)
            ->firstOrFail();

        return new ProductResource($product);
    }

    public function sliders()
    {
        return $this->slidersData();
    }

    public function amazings()
    {
        $now = now();

        $rows = Amazing::with('product.brand')
            ->where('status', true)
            ->where(fn ($q) => $q->whereNull('start_at')->orWhere('start_at', '<=', $now))
            ->where(fn ($q) => $q->whereNull('end_at')->orWhere('end_at', '>=', $now))
            ->get();

        return $rows->map(fn ($a) => $this->amazingRow($a));
    }

    public function provinces()
    {
        return cache()->remember('api.provinces', 3600, fn () => Province::where('status', true)
            ->with(['cities' => fn ($q) => $q->where('status', true)->orderBy('name')])
            ->orderBy('name')
            ->get()
            ->map(fn ($p) => [
                'id' => $p->id,
                'name' => $p->name,
                'cities' => $p->cities->map(fn ($c) => ['id' => $c->id, 'name' => $c->name])->values()->all(),
            ])->values()->all());
    }

    public function blogs()
    {
        return Blog::where('status', true)
            ->with('category')
            ->latest('publish_at')
            ->paginate(12)
            ->through(fn ($b) => $this->blogRow($b));
    }

    public function blog(string $slug)
    {
        $blog = Blog::where('slug', $slug)
            ->where('status', true)
            ->with('category')
            ->firstOrFail();

        $blog->increment('views');

        return $this->blogRow($blog->fresh());
    }

    private function blogRow(Blog $blog): array
    {
        $plain = trim(preg_replace('/\s+/', ' ', strip_tags($blog->description ?? '')));
        $words = $plain === '' ? 0 : count(preg_split('/\s+/u', $plain));

        return [
            'id' => $blog->id,
            'title' => $blog->title,
            'slug' => $blog->slug,
            'image' => $blog->image,
            'description' => $blog->description,
            'excerpt' => $blog->excerpt ?: mb_substr($plain, 0, 160),
            'meta_title' => $blog->meta_title,
            'meta_description' => $blog->meta_description,
            'canonical_url' => $blog->canonical_url,
            'is_featured' => (bool) $blog->is_featured,
            'views' => (int) $blog->views,
            'reading_minutes' => max(1, (int) ceil($words / 200)),
            'category' => $blog->category?->title,
            'publish_at' => $blog->publish_at?->toIso8601String(),
            'updated_at' => $blog->updated_at?->toIso8601String(),
        ];
    }

    public function shipments()
    {
        return Shipment::where('status', true)
            ->orderByDesc('is_default')
            ->orderBy('title')
            ->get(['id', 'title', 'default_price_rial', 'logo', 'is_default']);
    }

    public function installments()
    {
        return InstallmentPlan::where('status', true)
            ->orderBy('months')
            ->get(['id', 'title', 'down_payment_percent', 'months', 'fee_percent', 'description']);
    }

    public function newsletter(Request $request)
    {
        $data = $request->validate([
            'phone' => ['required', 'regex:/^09\d{9}$/'],
        ]);

        NewsletterSubscription::firstOrCreate(['phone' => $data['phone']]);

        return response()->json(['message' => 'عضویتت ثبت شد؛ به‌زودی خبرهای خوب میاد 🎉'], 201);
    }

    public function stockAlert(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
        ]);

        $product = Product::findOrFail($data['product_id']);

        if ($product->in_stock && $product->quantity > 0) {
            return response()->json(['message' => 'کالا موجود است.'], 422);
        }

        $request->user()->stockAlerts()->firstOrCreate(['product_id' => $product->id]);

        return response()->json(['message' => 'خبرت می‌کنیم به محض موجود شدن ✓'], 201);
    }

    public function discountCheck(Request $request)
    {
        $data = $request->validate([
            'code' => ['required', 'string'],
            'amount_rial' => ['required', 'integer', 'min:0'],
        ]);

        $code = DiscountCode::where('code', $data['code'])->first();

        if (! $code || ! $code->isUsable() || $data['amount_rial'] < $code->min_order_rial) {
            return response()->json(['valid' => false, 'message' => 'کد تخفیف معتبر نیست.'], 422);
        }

        $discount = $code->type === 'percent'
            ? (int) ($data['amount_rial'] * $code->value_rial / 100)
            : min($code->value_rial, $data['amount_rial']);

        return response()->json(['valid' => true, 'discount_rial' => $discount, 'type' => $code->type]);
    }

    public function settings()
    {
        return $this->settingsData();
    }

    private function amazingRow($a): array
    {
        return [
            'id' => $a->id,
            'product' => new ProductResource($a->product),
            'percent' => $a->percent,
            'price_rial' => $a->price_rial ? (int) $a->price_rial : null,
            'end_at' => $a->end_at?->toIso8601String(),
        ];
    }

    private function settingsData(): mixed
    {
        $keys = [
            'store_name', 'contact_phone', 'contact_phone_2', 'contact_email',
            'contact_address', 'working_hours', 'support_info', 'about',
            'instagram', 'telegram', 'whatsapp',
        ];

        return cache()->remember('api.settings', 3600, fn () => collect($keys)->mapWithKeys(fn ($key) => [$key => Setting::get($key, '')])->all());
    }
}
