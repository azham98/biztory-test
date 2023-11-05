<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Sale::all();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $sale = Sale::create($request->all());
        return response()->json($sale, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Sale $sale)
    {
        return $sale;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sale $sale)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sale $sale)
    {
        $sale->update($request->all());
        return response()->json($sale, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sale $sale)
    {
        $sale->delete();
        return response()->json(null, 204);
    }

    public function dailyTotalSales(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $dailySales = Sale::whereBetween('created_at', [$startDate, $endDate])
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total) as total_sales'))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->get();

        return response()->json($dailySales);
        // $startDate = Carbon::parse($args['startDate'])->startOfDay();
        // $endDate = Carbon::parse($args['endDate'])->endOfDay();

        // $dailySales = Sale::selectRaw('DATE(created_at) as date, SUM(amount) as totalAmount')
        //     ->whereBetween('created_at', [$startDate, $endDate])
        //     ->groupBy('date')
        //     ->get();

        // return $dailySales;
    }

    public function filter(Request $request)
    {
        $query = Sale::query();

        if ($request->has('payment_status')) {
            $query->where('payment_status', $request->input('payment_status'));
        }

        if ($request->has('payee_id')) {
            $query->where('payee_id', $request->input('payee_id'));
        }

        $sales = $query->get();

        return response()->json($sales);
    }
}
