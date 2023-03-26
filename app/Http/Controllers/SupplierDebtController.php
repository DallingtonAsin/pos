<?php

namespace App\Http\Controllers;

use App\DataTables\SupplierDebtDataTable;
use App\Helpers\Helper;
use App\Repositories\SupplierDebtRepository;
use App\Repositories\SupplierRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SupplierDebtController extends Controller
{

    protected $supplierDebtRepository, $supplierRepository;

    public function __construct(SupplierDebtRepository $supplierDebtRepository, SupplierRepository $supplierRepository)
    {
        $this->supplierDebtRepository = $supplierDebtRepository;
        $this->supplierRepository = $supplierRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $no_of_debts = $this->supplierDebtRepository->count();
        $total_debts = $this->supplierDebtRepository->total();
        $suppliers = $this->supplierRepository->get();

        return view('pages.main.suppliers.debts')->with(compact('suppliers', 'no_of_debts', 'total_debts'));
    }

    public function getSupplierDebtDataTable(SupplierDebtDataTable $dataTable)
    {
        return $dataTable->render('pages.main.suppliers.debts');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'supplier' => 'required',
            'amount' => 'required',
            'date' => 'required',
        ]);

        try {
            if ($validator->fails()) {
                $message = $validator->errors()->all();
                return response()->json(['error' => $message]);
            } else {

                $supplier_id = $request->input('supplier');
                $amount = Helper::Numberize($request->input('amount'));
                $date = $request->input('date');
                $created_by = $request->user()->id;

                $data = [
                    'supplier_id' => $supplier_id,
                    'amount' => $amount,
                    'date' => $date,
                    'added_by' => $created_by
                ];

                if ($this->supplierDebtRepository->create($data)) {
                    $supplier = $this->supplierRepository->find($supplier_id);
                    $message = "Debt for supplier " . $supplier->name . " has been added successfully";
                    $stats = $this->getSupplierDebtStats();
                    $data = [
                        'success' => $message,
                        'data' => $stats,
                    ];
                } else {
                    $message = "Technical error in adding supplier debt";
                    $data = [
                        'error' => $message
                    ];
                }
                return response()->json($data);
            }
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->sendJson($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return $this->sendJson($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (!empty($id)) {

            $validator = Validator::make($request->all(), [
                'supplier' => 'required',
                'amount' => 'required',
                'date' => 'required',
            ]);

            try {
                if ($validator->fails()) {
                    $message = $validator->errors()->all();
                    return response()->json(['error' => $message]);
                } else {

                    $supplier_id = $request->input('supplier');
                    $amount = Helper::Numberize($request->input('amount'));
                    $date = $request->input('date');
                    $created_by = $request->user()->id;

                    $data = [
                        'supplier_id' => $supplier_id,
                        'amount' => $amount,
                        'date' => $date,
                        'added_by' => $created_by
                    ];

                    if ($this->supplierDebtRepository->update($id, $data)) {
                        $supplier = $this->supplierRepository->find($supplier_id);
                        $message = "Debt for supplier " . $supplier->name . " has been updated successfully";

                        $stats = $this->getSupplierDebtStats();
                        $data = [
                            'success' => $message,
                            'data' => $stats
                        ];
                    } else {
                        $message = "Technical error in updating supplier debt";
                        $data = [
                            'error' => $message
                        ];
                    }
                    return response()->json($data);
                }
            } catch (\Exception $ex) {
                return response()->json(['error' => $ex->getMessage()]);
            }
        } else {
            return response()->json(['error' => 'System is unable to get supplier debt id']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!empty($id)) {

            try {

                $id = intval($id);
                $supplierDebt = $this->supplierDebtRepository->find($id);
                $data = [
                    'is_deleted' => !$supplierDebt->is_deleted
                ];

                if ($this->supplierDebtRepository->update($id, $data)) {
                    $supplier = $this->supplierRepository->find($supplierDebt->supplier_id);
                    $message = "Debt for supplier " . $supplier->name . " has been deleted successfully";

                    $stats = $this->getSupplierDebtStats();
                    $data = [
                        'success' => $message,
                        'data' => $stats
                    ];
                } else {
                    $message = "Technical error in deleting supplier debt";
                    $data = [
                        'error' => $message
                    ];
                }
                return response()->json($data);
            } catch (\Exception $ex) {
                return response()->json(['error' => $ex->getMessage()]);
            }
        } else {
            return response()->json(['error' => 'System is unable to get supplier debt id']);
        }
    }

    private function sendJson($id)
    {
        try {
            $data = $this->supplierDebtRepository->find($id);
            $data->amount = number_format($data->amount);
            $data->supplier = $this->supplierRepository->find($data->supplier_id)->name;
            return response()->json(['success' => 'Ok', 'data' => $data]);
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }

    private function getSupplierDebtStats()
    {
        try {

            $debts = $this->supplierDebtRepository->get();
            $no_of_debts = $this->supplierDebtRepository->count();
            $total_debts = $this->supplierDebtRepository->total();

            $data = array(
                'data' => $debts,
                'no_of_debts' => $no_of_debts,
                'total_debts' => $total_debts,
            );

            return $data;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}