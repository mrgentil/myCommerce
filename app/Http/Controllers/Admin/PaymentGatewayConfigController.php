<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentGatewayConfig;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PaymentGatewayConfigController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.payment_gateway_configs.index');
    }

    /**
     * Get data for DataTables.
     */
    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $configs = PaymentGatewayConfig::with('gateway')->select('payment_gateway_configs.*');

            return DataTables::of($configs)
                ->addColumn('gateway_name', fn ($row) => $row->gateway->name ?? 'N/A')
                ->addColumn('action', function ($row) {
                    return '
                        <a href="'.route('admin.payment_gateway_configs.edit', $row->id).'" class="btn btn-sm btn-primary me-1">
                            <i class="bi bi-pencil-fill"></i>
                        </a>
                        <span class="border border-danger dt-trash rounded-3 d-inline-block" onclick="deleteConfig('.$row->id.')">
                            <i class="bi bi-trash-fill text-danger"></i>
                        </span>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
