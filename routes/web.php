<?php

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

Route::get('/', function () {
    $products = Product::with('category')->latest()->take(6)->get();
    return view('home', compact('products'));
})->name('home');

Route::get('/products', function (Request $request) {
    $query = Product::with('category')->latest();
    if ($request->filled('q')) {
        $term = $request->string('q')->toString();
        $query->where(fn ($builder) => $builder->where('name', 'like', "%{$term}%")
            ->orWhere('description', 'like', "%{$term}%"));
    }
    if ($request->filled('category')) {
        $query->where('category_id', $request->integer('category'));
    }
    $products = $query->paginate(12)->withQueryString();
    $categories = Category::orderBy('name')->get();
    return view('products.index', compact('products', 'categories'));
})->name('products.index');

Route::get('/products/{product}', function (Product $product) {
    return view('products.show', compact('product'));
})->name('products.show');

Route::get('/login', fn() => view('auth.login'))->name('login');
Route::post('/login', function (Request $request) {
    $credentials = $request->validate(['email' => ['required', 'email'], 'password' => ['required']]);
    if (Auth::attempt($credentials) && !Auth::user()->isAdmin()) {
        $request->session()->regenerate();
        return redirect()->intended('/')->with('success', 'Đăng nhập thành công.');
    }
    Auth::logout();
    return back()->withErrors(['email' => 'Thông tin đăng nhập không hợp lệ hoặc tài khoản không dành cho khách hàng.']);
})->name('login.post');

Route::get('/admin/login', fn() => view('auth.admin-login'))->name('admin.login');
Route::post('/admin/login', function (Request $request) {
    $credentials = $request->validate(['email' => ['required', 'email'], 'password' => ['required']]);
    if (Auth::attempt($credentials) && Auth::user()->isAdmin()) {
        $request->session()->regenerate();

        return redirect()->intended(route('admin.products.index'))->with('success', 'Đăng nhập quản trị thành công.');
    }

    Auth::logout();

    return back()->withErrors(['email' => 'Thông tin đăng nhập quản trị không hợp lệ.']);
})->name('admin.login.post');

Route::get('/register', fn() => view('auth.register'))->name('register');
Route::post('/register', function (Request $request) {
    $data = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'email', 'max:255', 'unique:users,email'],
        'password' => ['required', 'confirmed', 'min:8'],
    ]);
    $user = User::create([
        'name' => $data['name'],
        'email' => $data['email'],
        'password' => Hash::make($data['password']),
    ]);
    Auth::login($user);
    $request->session()->regenerate();

    return redirect()->route('home')->with('success', 'Đăng ký thành công. Bạn đã được đăng nhập.');
})->name('register.post');

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->name('logout');

$resolveCart = static function (): array {
    $cart = session('cart', []);
    $products = Product::with('category')->whereIn('id', array_keys($cart))->get()->keyBy('id');

    foreach ($cart as $id => $quantity) {
        $product = $products->get($id);
        $quantity = (int) $quantity;

        if (!$product || $product->quantity < 1 || $quantity < 1) {
            unset($cart[$id]);
            continue;
        }

        $cart[$id] = min($quantity, $product->quantity);
    }

    session(['cart' => $cart]);
    $total = collect($cart)
        ->map(fn ($quantity, $id) => $products->get($id)->price * $quantity)
        ->sum();

    return compact('cart', 'products', 'total');
};

Route::post('/cart/add/{product}', function (Request $request, Product $product) {
    if ($product->quantity < 1) {
        return back()->with('error', 'Sản phẩm hiện đã hết hàng.');
    }

    $data = $request->validate([
        'quantity' => ['nullable', 'integer', 'min:1'],
    ]);
    $quantity = (int) ($data['quantity'] ?? 1);
    $cart = session('cart', []);
    $cart[$product->id] = min((int) ($cart[$product->id] ?? 0) + $quantity, $product->quantity);
    session(['cart' => $cart]);
    return back()->with('success', "Đã thêm {$product->name} vào giỏ hàng.");
})->name('cart.add');

Route::get('/cart', function () use ($resolveCart) {
    ['cart' => $cart, 'products' => $products, 'total' => $total] = $resolveCart();
    return view('cart.index', compact('cart', 'products', 'total'));
})->name('cart.index');

Route::patch('/cart/{product}', function (Request $request, Product $product) {
    $data = $request->validate([
        'quantity' => ['required', 'integer', 'min:0'],
    ]);
    $quantity = min($data['quantity'], $product->quantity);
    $cart = session('cart', []);
    if ($quantity === 0) unset($cart[$product->id]); else $cart[$product->id] = $quantity;
    session(['cart' => $cart]);
    return back()->with('success', 'Đã cập nhật giỏ hàng.');
})->name('cart.update');

Route::delete('/cart/{product}', function (Product $product) {
    $cart = session('cart', []);
    unset($cart[$product->id]);
    session(['cart' => $cart]);
    return back()->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng.');
})->name('cart.remove');

Route::middleware('auth')->group(function () use ($resolveCart) {
    Route::get('/checkout', function () use ($resolveCart) {
        ['cart' => $cart, 'products' => $products, 'total' => $total] = $resolveCart();
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng đang trống.');
        }

        return view('checkout.index', compact('cart', 'products', 'total'));
    })->name('checkout.index');

    Route::post('/checkout', function (Request $request) {
        $data = $request->validate([
            'phone' => ['required', 'string', 'max:30'],
            'address' => ['required', 'string', 'max:500'],
            'payment_method' => ['required', 'in:cod,bank_transfer'],
        ]);
        $cart = session('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng đang trống.');
        }

        $availableProducts = Product::whereIn('id', array_keys($cart))->get()->keyBy('id');
        $removedProducts = false;
        foreach ($cart as $id => $quantity) {
            if (!$availableProducts->has($id)) {
                unset($cart[$id]);
                $removedProducts = true;
                continue;
            }

            if ($quantity > $availableProducts[$id]->quantity) {
                return redirect()->route('cart.index')
                    ->with('error', "Số lượng {$availableProducts[$id]->name} vừa thay đổi. Vui lòng kiểm tra lại giỏ hàng.");
            }
        }

        if ($removedProducts || empty($cart)) {
            session(['cart' => $cart]);
            return redirect()->route('cart.index')
                ->with('error', 'Một số sản phẩm trong giỏ không còn bán. Vui lòng kiểm tra lại đơn hàng.');
        }

        $order = DB::transaction(function () use ($cart, $data) {
            $products = Product::whereIn('id', array_keys($cart))->lockForUpdate()->get()->keyBy('id');
            $total = 0;
            foreach ($cart as $id => $quantity) {
                $product = $products[$id] ?? abort(404);
                abort_if($quantity > $product->quantity,  redirect()->route('cart.index')->with('error', "Sản phẩm {$product->name} không đủ tồn kho."));
                $total += $product->price * $quantity;
            }
            $order = Order::create([
                'user_id' => Auth::id(),
                'total' => $total,
                'status' => 'pending',
                'payment_method' => $data['payment_method'],
                'phone' => $data['phone'],
                'address' => $data['address'],
            ]);
            foreach ($cart as $id => $quantity) {
                $product = $products[$id];
                $order->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $quantity,
                    'price' => $product->price,
                ]);
                $product->decrement('quantity', $quantity);
            }
            return $order;
        });
        session()->forget('cart');
        return redirect()->route('orders.show', $order)->with('success', 'Đặt hàng thành công.');
    })->name('checkout.store');

    Route::get('/orders', function (Request $request) {
        $orders = $request->user()->orders()->with('items')->latest()->paginate(10);

        return view('orders.index', compact('orders'));
    })->name('orders.index');

    Route::get('/orders/{order}', function (Order $order) {
        abort_unless((int) $order->user_id === (int) Auth::id(), 403);
        $order->load(['items', 'user']);
        return view('orders.show', compact('order'));
    })->name('orders.show');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('products', ProductController::class)->except(['show']);

    Route::get('/orders', function () {
        $orders = Order::with(['user', 'items'])->latest()->paginate(15);

        return view('admin.orders', compact('orders'));
    })->name('orders.index');

    Route::patch('/orders/{order}/status', function (Request $request, Order $order) {
        $data = $request->validate([
            'status' => ['required', 'in:pending,processing,shipped,delivered,cancelled'],
        ]);

        $order->update($data);

        return redirect()->route('admin.orders.index')->with('success', 'Đã cập nhật trạng thái đơn hàng.');
    })->name('orders.update-status');
});