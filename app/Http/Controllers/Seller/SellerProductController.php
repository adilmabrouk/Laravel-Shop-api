<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Seller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SellerProductController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Seller $seller)
    {
        $products = $seller->products;

        return response()->json(['data' => $products], 200);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, User $seller)
    {
        $rules = [
            'name' => 'required',
            'description' => 'required',
            'quantity' => 'required|integer|min:1',
            'image' => 'required|image',
        ];

        $this->validate($request, $rules);

        $data = $request->all();

        $data['status'] = Product::UNAVAILABLE_PRODUCT;
        $data['image'] = '1.jpeg';
        $data['seller_id'] = $seller->id;

        $product = Product::create($data);

        return response()->json(['data' => $product], 200);
    }

    public function update(Request $request, Seller $seller, Product $product)
    {
        $rules = [

            'quantity' => 'integer|min:1',
            'status' => 'in:' . Product::UNAVAILABLE_PRODUCT . ',' . Product::AVAILABLE_PRODUCT,
            'image' => 'image',
        ];

        $this->validate($request, $rules);

        $this->checkSeller($product, $seller);

        $product->fill($request->all());

        if ($request->has('status')) {
            $product->status = $request->status;
            if ($product->isAvailable() && $product->categories()->count() == 0) {
                return $this->errorResponse('an active product must have at least one category', 409);
            }
        }

        if ($product->isClean()) {
            return $this->errorResponse('you need to specify a different value to update', 422);
        }

        $product->save();

        return $this->showOne($product);
    }

    public function destroy(Seller $seller, Product $product)
    {
        $this->checkSeller($product, $seller);

        $product->delete();

        return $this->showOne($product);
    }


    protected function checkSeller(Product $product, Seller $seller)
    {
        if ($seller->id != $product->seller_id) {
            throw new HttpException(422, 'the specified seller is not the actual seller of this product');
        }
    }
}
