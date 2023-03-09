<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CustomerDebtPayment;
use App\DataTables\CustomerDebtPaymentRecordsDataTable;
use App\DataTables\CustomersWithDebtsDataTable;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Helpers\Helper;


class CustomerDebtPaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        $customers = DB::table('customers')->distinct()
            ->join('sales', 'customers.id', '=', 'sales.customer_id')
            ->select('customers.id', 'customers.name')
            ->get();
        return view('pages.main.customer-debt-payment-records')->with(compact('customers'));
    }

    public function GetCustomerDebtPayments(CustomerDebtPaymentRecordsDataTable $dataTable){
        return $dataTable->render('pages.main.customer-debt-payment-records');
    }


    public function customersWithDebtsIndex(){
        $total_debts = Helper::getTotalCustomerDebt();
        return view('pages.main.customers-with-debts')->with(compact('total_debts'));
    }

    public function GetCustomersWithDebts(CustomersWithDebtsDataTable $dataTable){
        return $dataTable->render('pages.main.customers-with-debts');
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


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer' => 'required',
            'paid_amount' => 'required',
            'payment_date' => 'required'
        ]);

        try {
            if ($validator->fails()) {
                $message = $validator->errors()->all();
                return response()->json(['error' => $message]);
            } else {

                $customer_id = $request->input('customer');
                $paid_amount = $request->input('paid_amount');
                $paid_amount =  Helper::Numberize($paid_amount);
                $balance = Helper::customerDebt($customer_id) - $paid_amount;
                $payment_date = $request->input('payment_date');

                $recorded_by = $request->user()->id;
                $data = [
                    'customer_id' => $customer_id,
                    'paid_amount' => $paid_amount,
                    'balance' => $balance,
                    'date' => $payment_date,
                    'recorded_by' => $recorded_by
                ];

                if (CustomerDebtPayment::create($data)) {
                    $message = "Customer payment has been recorded successfully";

                    $data = ['success' => $message];
                } else {
                    $message = "Technical error in adding customer payment";
                    $data = [
                        'error' => $message
                    ];
                }
                return response()->json($data);
            }
        } catch (\Exception $ex) {
            return response()->json(['error' => 'Exception ' . $ex->getMessage()]);
        }
    }

  
    public function getCustomerDebt($customer_id)
    {
        try {
            $debt = Helper::customerDebt($customer_id);
            return response()->json(['success' => 'OK', 'data' => $debt]);
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }


    protected function getPaymentDetails($id)
    {
        try {

            $payment = CustomerDebtPayment::find($id);
            return response()->json(['data' => $payment]);
        } catch (\Exception $ex) {
            throw $ex;
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
        return $this->getPaymentDetails($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return $this->getPaymentDetails($id);
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
                'customer' => 'required',
                'paid_amount' => 'required',
                'payment_date' => 'required'
            ]);

            try {
                if ($validator->fails()) {
                    $message = $validator->errors()->all();
                    return response()->json(['error' => $message]);
                } else {

                    $customer_id = $request->input('customer');
                    $paid_amount = $request->input('paid_amount');
                    $payment_date = $request->input('payment_date');
                    $balance = Helper::customerDebt($customer_id) - intval($paid_amount);

                    $created_by = $request->user()->id;
                    $data = [
                        'paid_amount' => $paid_amount,
                        'balance' => $balance,
                        'date' => $payment_date,
                        'recorded_by' => $created_by
                    ];

                    if (CustomerDebtPayment::where('id', $id)->where('customer_id', $customer_id)->update($data)) {
                        $message = "Customer payment has been updated successfully";
                        $stats = $this->GetDepartmentStats();
                        $data = [
                            'success' => $message,
                            'data' => $stats
                        ];
                    } else {
                        $message = "Technical error in updating department";
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
            return response()->json(['error' => 'System is unable to get department id']);
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
        //
    }
}
