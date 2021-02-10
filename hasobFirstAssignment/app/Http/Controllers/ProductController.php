<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductIndexResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Models\CheckOut;
use App\Models\ItemCheckOut;
use Validator;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.verify', ['except' => ['index', 'show', 'store']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        // if ($request->all() == null) {
        //     return response()->json(['error' => "Fields can't be blank"]);
        // }

        $products = Product::where([['name', '!=', Null], [function ($query) use ($request) {
            if ($search = $request->search) {
                $query
                    ->orWhere('name', 'LIKE', "% {$search} %")
                    ->orWhere('description', 'LIKE', "% {$search} %")
                    ->get();
            }
        }]])->orderBy('id', 'DESC')->paginate(5);
        // dd($products);

        return ProductIndexResource::collection($products);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */


    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'name' => 'required|max:100',
            'description' => 'required',
            'image_path' => 'required|image|mimes:jpeg,jpg,png,svg',
            'price' => 'required',
            'quantity' => 'required'

        ]);

        if ($validation->fails()) {
            return response()->json(['error' => $validation->errors()], 400);
        }
        $path = $request->file('image_path')->store('public/products');
        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'image_path' => $path,
            'price' => $request->price,
            'quantity' => $request->quantity,
            'slug' => Str::slug($request->name),
        ]);

        return response(new ProductIndexResource($product), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product, Request $request)
    {
        return new ProductIndexResource($product);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        $validation = Validator::make($request->all(), [
            'name' => 'min:3|max:100',
            'description' => 'min:5',
            'price' => 'integer',
            'quantity' => 'integer',
            'image_path' => 'image|mimes:jpg,jpeg,svg,png'
        ]);

        $newPath = "";
        if ($request->hasFile('image_path')) {
            Storage::delete('products/' . $product->image_path);
            $newPath = $request->file('image_path')->store('public/products');
        }

        if ($validation->fails()) {
            return response()->json($validation->errors(), 422);
        }

        $product->name = $request->get('name', $product->name);
        $product->description = $request->get('description', $product->description);
        $product->price = $request->get('price', $product->price);
        $product->quantity = $request->get('quantity', $product->quantity);
        $product->image_path = $request->get($newPath, $product->image_path);

        $product->save();
        return new ProductIndexResource($product);
    }

    public function checkout(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'cartItems' => 'required|array',
            'total' => 'required|numeric',
        ]);

        if ($validation->fails()) {
            return response()->json($validation->errors()->toJson(), 400);
        }

        // save to checkout and retrieve id
        $checkout = CheckOut::create([
            'total' => $request->total,
            'user_id' => auth()->id()
        ]);

        $cartItems = $request->cartItems;
        for ($i = 0; $i < count($cartItems); $i++) {
            $item = $cartItems[$i];
            ItemCheckOut::create([
                'checkout_id' => $checkout['id'],
                'product_id' => $item['id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'sub_total' => $item['sub_total']
            ]);
        }

        return response('Checkout was added successfuly', 201);
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return response('', 204);
    }
}
