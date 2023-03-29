<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DataTables\CustomerDebtPaymentRecordsDataTable;
use App\DataTables\CustomersWithDebtsDataTable;
use Illuminate\Support\Facades\Validator;
use App\Models\CustomerDebtPayment;
use App\Repositories\CustomerRepository;
use App\Repositories\CustomerDebtPaymentRepository;
use App\Repositories\CreditSaleRepository;
use App\Models\Customer;
use App\Helpers\Helper;


class CustomerDebtPaymentController extends Controller
{

    protected $helper, $customerDebtPaymentRepository, $creditSaleRepository, $customerRepository;

    public function __construct(
        Helper $helper,
        CustomerDebtPaymentRepository $customerDebtPaymentRepository,
        CreditSaleRepository $creditSaleRepository,
        CustomerRepository $customerRepository
    ) {
        $this->helper = $helper;
        $this->customerDebtPaymentRepository = $customerDebtPaymentRepository;
        $this->creditSaleRepository = $creditSaleRepository;
        $this->customerRepository =  $customerRepository;
    }

    private function getCreditStatistics()
    {
        try {
            $total_records = $this->customerDebtPaymentRepository->count();
            $total_credit_sales = $this->creditSaleRepository->totalCreditSales();
            $total_debt_paid = $this->customerDebtPaymentRepository->totalPaid();
            $credit_balance = $this->customerRepository->getTotalCutomerDebt();
            return [
                'total_records' => $total_records,
                'total_credit_sales' => $total_credit_sales,
                'total_debt_paid' => $total_debt_paid,
                'credit_balance' => $credit_balance
            ];
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $stats = $this->getCreditStatistics();

        $total_records = $stats['total_records'];
        $total_credit_sales = $stats['total_credit_sales'];
        $total_debt_paid = $stats['total_debt_paid'];
        $credit_balance = $stats['credit_balance'];

        $customers = Customer::distinct()
            ->join('credit_sales', 'customers.id', '=', 'credit_sales.customer_id')
            ->select('customers.id', 'customers.name')
            ->get();

        return view('pages.main.customers.customer-debt-payment-records')
            ->with(compact(
                'total_records',
                'total_credit_sales',
                'total_debt_paid',
                'credit_balance',
                'customers'
            ));
    }

    public function getCustomerDebtPayments(CustomerDebtPaymentRecordsDataTable $dataTable)
    {
        return $dataTable->render('pages.main.customers.customer-debt-payment-records');
    }


    public function customersWithDebtsIndex()
    {
        $total_debts =  $this->customerRepository->getTotalCutomerDebt();
        return view('pages.main.customers.customers-with-debts')->with(compact('total_debts'));
    }

    public function getCustomersWithDebts(CustomersWithDebtsDataTable $dataTable)
    {
        return $dataTable->render('pages.main.customers.customers-with-debts');
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
                $balance = $this->helper->getCustomerDebt($customer_id) - $paid_amount;
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
                    $stats = $this->getCreditStatistics();
                    $data = ['success' => $message, 'data' => $stats];
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
            $debt = $this->helper->getCustomerDebt($customer_id);
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
                    $balance = $this->helper->getCustomerDebt($customer_id) - intval($paid_amount);

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
