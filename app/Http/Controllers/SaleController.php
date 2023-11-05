<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;


class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sale = Sale::all();
        return response()->json($sale);
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
        $data = Validator::make($request->all(), [
            'status' => 'required',
            'ref_num' => 'required',
            'invoice_date' => 'required',
            'delivery_date' => 'nullable',
            'payee' => 'required',
            'payee_id' => 'required',
            'total' => 'required',
            'currency' => 'nullable',
            'currency_total' => 'required',
            'paid' => 'required',
            'due' => 'required',
            'rounding' => 'nullable',
            'due_date' => 'nullable',
            'attn' => 'nullable',
            'payment_term' => 'nullable',
            'payment_status' => 'nullable',
            'payment_status' => 'required',
            'delivery_status' => 'required',
            'branch_id' => 'nullable',
            'locked' => 'required',
            'staff_id' => 'nullable',
            'author_id' => 'nullable',
        ]);

        if ($data->fails()) {
            return response()->json(['errors' => $data->errors()], 400);
        }

        $sale = Sale::create($request->all());

        return response()->json($sale, 201);

    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $sale = Sale::find($id);

        if (!$sale) {
            return response()->json(['error' => 'Sale not found'], 404);
        }

        return response()->json($sale);
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
    public function update(Request $request, $id)
    {
        $sale = Sale::find($id);

        if (!$sale) {
            return response()->json(['error' => 'Sale not found'], 404);
        }

        $sale->update($request->all());
        return response()->json($sale, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $sale = Sale::find($id);

        if (!$sale) {
            return response()->json(['error' => 'Sale not found'], 404);
        }

        // soft delete
        $sale->delete();

        return response()->json(['message' => 'Sale deleted'], 200);
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
