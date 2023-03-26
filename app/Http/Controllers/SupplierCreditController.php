<?php

namespace App\Http\Controllers;

use App\DataTables\SupplierCreditDataTable;
use App\Helpers\Helper;
use Illuminate\Http\Request;
use App\Repositories\SupplierCreditRepository;
use App\Repositories\SupplierRepository;
use Illuminate\Support\Facades\Validator;

class SupplierCreditController extends Controller
{

    protected $supplierCreditRepository, $supplierRepository;

    public function __construct(SupplierCreditRepository $supplierCreditRepository, SupplierRepository $supplierRepository)
    {
        $this->supplierCreditRepository = $supplierCreditRepository;
        $this->supplierRepository = $supplierRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $no_of_credits = $this->supplierCreditRepository->count();
        $total_supplier_credits = $this->supplierCreditRepository->total();

        return view('pages.main.suppliers.credits')->with(compact('total_supplier_credits'));
    }

    public function getStoresDataTable(SupplierCreditDataTable $dataTable)
    {
        return $dataTable->render('pages.main.suppliers.credits');
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

                if ($this->supplierCreditRepository->create($data)) {
                    $supplier = $this->supplierRepository->find($supplier_id);
                    $message = "Credit for supplier " . $supplier->name . " has been added successfully";
                    $stats = $this->getSupplierCreditStats();
                    $data = [
                        'success' => $message,
                        'data' => $stats,
                    ];
                } else {
                    $message = "Technical error in adding supplier credit";
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

                    if ($this->supplierCreditRepository->update($id, $data)) {
                        $supplier = $this->supplierRepository->find($supplier_id);
                        $message = "Credit for supplier " . $supplier->name . " has been added successfully";

                        $stats = $this->getSupplierCreditStats();
                        $data = [
                            'success' => $message,
                            'data' => $stats
                        ];
                    } else {
                        $message = "Technical error in updating supplier credit";
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
            return response()->json(['error' => 'System is unable to get supplier credit id']);
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
                $supplierCredit = $this->supplierCreditRepository->find($id);
                $data = [
                    'is_deleted' => !$supplierCredit->is_deleted
                ];

                if ($this->supplierCreditRepository->update($id, $data)) {
                    $supplier = $this->supplierRepository->find($supplierCredit->supplier_id);
                    $message = "Credit for supplier " . $supplier->name . " has been added successfully";

                    $stats = $this->getSupplierCreditStats();
                    $data = [
                        'success' => $message,
                        'data' => $stats
                    ];
                } else {
                    $message = "Technical error in deleting supplier credit";
                    $data = [
                        'error' => $message
                    ];
                }
                return response()->json($data);
            } catch (\Exception $ex) {
                return response()->json(['error' => $ex->getMessage()]);
            }
        } else {
            return response()->json(['error' => 'System is unable to get supplier credit id']);
        }
    }

    private function sendJson($id)
    {
        try {
            $data = $this->supplierCreditRepository->find($id);
            return response()->json(['success' => 'Ok', 'data' => $data]);
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }

    private function getSupplierCreditStats()
    {
        try {

            $credits = $this->supplierCreditRepository->get();
            $total_credits = $this->supplierCreditRepository->count();

            $data = array(
                'data' => $credits,
                'total' => $total_credits
            );

            return $data;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}
