<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Seller;
use Illuminate\Http\Request;

class SellerTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Seller $seller)
    {
        $trasactions = $seller->products()
            ->with('transactions')
            ->get()
            ->pluck('transactions');
        return response()->json(['data' => $trasactions], 200);
    }
}
