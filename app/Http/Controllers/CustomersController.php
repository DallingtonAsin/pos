<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\Stock;
use App\Helpers\Helper;
use App\Imports\ImportCustomers;
use App\Exports\ExportCustomers;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\LogsController;
use App\Http\Controllers\LogAfterRequest;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use App\DataTables\CustomersDataTable;
use App\DataTables\CustomersWithDebtsDataTable;
use App\DataTables\CustomerDebtPaymentRecordsDataTable;
use App\Services\CustomerDebtPaymentService;
use Illuminate\Support\Str;
use  App\Helpers\Constants as Constant;
use DataTable;
use Excel;

class CustomersController extends Controller
{

  public $controller;
  public function __construct()
  {
    $this->controller = 'CustomersController';
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    // $customers = DB::select('select * from customers');
    $customers = Customer::all();
    $number_of_customers = Customer::count();
    return view('pages.main.customers')->with(compact('customers', 'number_of_customers'));
  }

  public function GetCustomers(CustomersDataTable $dataTable)
  {
    return $dataTable->render('pages.main.customers');
  }

  public function customerDebtPaymentsIndex()
  {
    return view('pages.main.customer-debt-payment-records');
  }

  public function GetCustomerDebtPayments(CustomerDebtPaymentRecordsDataTable $dataTable)
  {
    return $dataTable->render('pages.main.customer-debt-payment-records');
  }


  public function customersWithDebtsIndex()
  {
    $total_debtors = Sale::where('balance', '>', 0)->where('fully_paid', 0)->count();
    $total_debts = Sale::where('balance', '>', 0)->where('fully_paid', 0)->sum('balance');
    return view('pages.main.customers-with-debts')->with(compact('total_debtors', 'total_debts'));
  }

  public function GetCustomersWithDebts(CustomersWithDebtsDataTable $dataTable)
  {
    return $dataTable->render('pages.main.customers-with-debts');
  }



  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    return view('pages.main.customers');
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $req)
  {

    $customerId = $req->input('id');

    $customer_name = $req->input('name');
    $contact = $req->input('contact');
    $address = $req->input('address');

    (empty($customerId)) ? $keyAction = 'registered' : $keyAction = 'updated';

    if (isset($customerId)) {

      $response = Customer::where('id', $customerId)
        ->update([
          'name' => $customer_name,
          'contact' => $contact,
          'address' => $address
        ]);
    } else {

      $customer = new Customer();
      $customer->name = $customer_name;
      $customer->contact = $contact;
      $customer->address = $address;
      $customer->added_by = $req->user()->id;
      $response = $customer->save();
    }


    if ($response) {

      $action = "" . $keyAction . " customer " . $customer_name . "";
      LogsController::logger($req, $action, now());
      $dataArr = array(
        "code" => '200',
        "message" => $action,
        "method" => "CustomersController@store"
      );
      LogAfterRequest::LogRequest($req, $dataArr);
      $sessionVariable = 'success';
      $responseInfo = $this->SuccessMessage($action);
    } else {

      $messageErr = "registering of customer details not failed!";
      $dataArr = array(
        "code" => '101',
        "message" => $messageErr,
        "method" => "CustomersController@store"
      );
      LogAfterRequest::LogRequest($req, $dataArr);
      $sessionVariable = 'fail';
      $responseInfo = $this->FailedMessage($messageErr);
    }

    $arr = $this->GetSumupDetails();

    return response()
      ->json([
        $sessionVariable => $responseInfo,
        'totl_no' => $arr['totl_no'],
      ]);
  }


  protected function GetSumupDetails()
  {
    $number_of_customers = Customer::count();
    $data = array(
      'totl_no' => $number_of_customers
    );

    return $data;
  }


  public function showCustomerWithDebt($id)
  {
    $sale = Sale::find($id);
    return response()->json($sale);
  }



  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    $customer = Customer::find($id);
    return response()->json($customer);
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
    $customer = Customer::find($id);
    return response()->json($customer);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $req, $id)
  {


    $customer = Customer::find($id);

    $customer_name = $req->input('name');
    $contact = $req->input('contact');
    $address = $req->input('address');

    $customer->name = $customer_name;
    $customer->contact = $contact;
    $customer->address = $address;

    $save_status = $customer->save();

    if ($save_status) {

      // $action = "updated details of customer ".$name."";
      // LogsController::logger($req, $action, now());
      // $dataArr = array("code" => '200',
      // "message" => $action,
      // "method" => "CustomersController@update");
      // LogAfterRequest::LogRequest($req, $dataArr);
      // return back()->with("success", $this->SuccessMessage($action));

      $action = "updated record for customer " . $customer_name . "";
      LogsController::logger($req, $action, now());
      $dataArr = array(
        "code" => '200',
        "message" => $action,
        "method" => "CustomersController@update"
      );
      LogAfterRequest::LogRequest($req, $dataArr);
      $sessionVariable = 'success';
      $responseInfo = $this->SuccessMessage($action);
    } else {

      // $messageErr = 'Customer Update failed!';
      // $dataArr = array("code" => '101',
      // "message" => $messageErr,
      // "method" => "CustomersController@update");
      // LogAfterRequest::LogRequest($req, $dataArr);
      // return back()->with('fail', $messageErr);

      $messageErr = "editing of customer details not failed!";
      $dataArr = array(
        "code" => '101',
        "message" => $messageErr,
        "method" => "CustomersController@update"
      );
      LogAfterRequest::LogRequest($req, $dataArr);
      $sessionVariable = 'fail';
      $responseInfo = $this->FailedMessage($messageErr);
    }

    $arr = $this->GetSumupDetails();

    return response()
      ->json([
        $sessionVariable => $responseInfo,
        'totl_no' => $arr['totl_no']
      ]);
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  //   public function destroy(Request $request, $id){

  //     $customer = Customer::findOrFail($id);
  //     $customer_name = $customer->name;

  //     $delete_status = $customer->delete();
  //     if($delete_status){

  //       $action = "removed customer ".$customer_name." from list of customers in the system";
  //       LogsController::logger($request, $action, now());
  //       $dataArr = array("code" => '200',
  //       "message" => $action,
  //       "method" => "CustomersController@destroy");
  //       LogAfterRequest::LogRequest($request, $dataArr);
  //       return back()->with("success", $this->SuccessMessage($action));
  //     }
  //     else
  //     {
  //       $messageErr = 'Customer not deleted!';
  //       $dataArr = array("code" => '101',
  //       "message" => $messageErr,
  //       "method" => "CustomersController@destroy");
  //       LogAfterRequest::LogRequest($request, $dataArr);
  //       return back()->with('fail', $messageErr);
  //    }

  //  }


  public function destroy(Request $request, $id)
  {


    $method = "CustomersController@destroy";

    $customer_name = Customer::where('id', $id)->value('name');
    $response = Customer::find($id)->delete();

    if ($response) {

      $action = "removed customer " . $customer_name . " from the system";
      LogsController::logger($request, $action, now());
      $dataArr = array(
        "code" => '200',
        "message" => $action,
        "method" => $method
      );
      LogAfterRequest::LogRequest($request, $dataArr);
      $sessionVariable = 'success';
      $responseInfo = $this->SuccessMessage($action);
    } else {

      $messageErr = "customer not removed";
      $dataArr = array(
        "code" => '101',
        "message" => $messageErr,
        "method" => $method
      );
      LogAfterRequest::LogRequest($request, $dataArr);
      $sessionVariable = 'fail';
      $responseInfo = $this->FailedMessage($messageErr);
    }

    $arr = $this->GetSumupDetails();

    return response()
      ->json([
        $sessionVariable => $responseInfo,
        'totl_no' => $arr['totl_no']
      ]);
  }

  public function deleteAllCustomers(Request $request)
  {

    $result = Customer::truncate();
    if ($result) {

      $action = "removed all customers from the system";
      LogsController::logger($request, $action, now());
      $dataArr = array(
        "code" => '200',
        "message" => $action,
        "method" => "CustomersController@deleteAllCustomers"
      );
      LogAfterRequest::LogRequest($request, $dataArr);
      $sessionVariable = 'success';
      $responseInfo = $this->SuccessMessage($action);
      //  return back()->with("success", $this->SuccessMessage($action));
    } else {
      $messageErr = 'Customers not removed from the system!';
      $dataArr = array(
        "code" => '101',
        "message" => $messageErr,
        "method" => "CustomersController@deleteAllCustomers"
      );
      LogAfterRequest::LogRequest($request, $dataArr);
      $sessionVariable = 'fail';
      $responseInfo = $messageErr;
      //  return back()->with('fail', $messageErr);
    }

    $arr = $this->GetSumupDetails();

    return response()
      ->json([
        $sessionVariable => $responseInfo,
        'totl_no' => $arr['totl_no']
      ]);
  }


  public function RemoveSelected(Request $request)
  {
    try {
      $ids =  $request->input('selected_rows');
      $deletedCustomers = array();

      if (count($ids) > 0) {
        foreach ($ids as $id) {
          $findId = Customer::find($id);
          $findId->delete();
          array_push($deletedCustomers, $findId->name);
        }
      }
      $sessionVariable = 'success';
      $deletedCustomerStr = implode(", ", $deletedCustomers);
      $action = "removed customers " . $deletedCustomerStr . " from the system";
      if (count($ids) == 1) {
        $action = Str::replaceFirst('customers', 'customer', $action);
      }
      $response = $this->SuccessMessage($action);

      $dataArr = array(
        "code" => '200',
        "message" => $action,
        "method" => "" . $this->controller . "@RemoveSelected"
      );
      LogsController::logger($request, $action, now());
      LogAfterRequest::LogRequest($request, $dataArr);

      $arr = $this->GetSumupDetails();

      return response()
        ->json([
          $sessionVariable => $response,
          'totl_no' => $arr['totl_no']
        ]);
    } catch (\Exception $ex) {
      $data = array(
        'username' => auth()->user()->username,
        'error_code' => $ex->getCode(),
        'error_message' => $ex->getMessage(),
        'error_severity' => Constant::$STATUS_ERROR_SEVERITY,
        'controller' => $this->controller,
        'method' => 'RemoveSelected'
      );
      Helper::logError($data);
      abort(409, $ex->getMessage());
    }
  }



  public function importCustomers(Request $request)
  {

    $this->validate(
      $request,
      ['select_file' => 'required|mimes:xls,xlsx'],
      ['select_file.mimes' => 'Please select only excel files to import customers']
    );
    $importSuccess = Excel::import(new ImportCustomers, request()->file('select_file'));

    if ($importSuccess) {
      $action = "imported an excel file of customers into the system";
      LogsController::logger($request, $action, now());
      $dataArr = array(
        "code" => '200',
        "message" => $action,
        "method" => "CustomersController@importCustomers"
      );
      LogAfterRequest::LogRequest($request, $dataArr);

      return back()->with('success', $this->SuccessMessage($action));
    } else {
      $messageErr = "Excel Customers data not imported!";
      $dataArr = array(
        "code" => '101',
        "message" => $messageErr,
        "method" => "CustomersController@importCustomers"
      );
      LogAfterRequest::LogRequest($request, $dataArr);
      return back()->with('fail', $messageErr);
    }
  }

  /**
   * @return \Illuminate\Support\Collection
   */
  public function exportCustomers()
  {
    return Excel::download(new ExportCustomers, 'customers.xlsx');
  }


  public function downloadCustomersPdf()
  {
    $customers = Customer::all();
    $pdf = PDF::loadView('pages.main.customers', compact('customers'));
    return $pdf->download('customers.pdf');
  }


  protected function SuccessMessage($msg)
  {
    $message = "You have successfully " . $msg . "";
    return $message;
  }


  protected function FailedMessage($failmsg)
  {
    $message = "" . $failmsg . "";
    return $message;
  }

  public function updateCustomerDebts(CustomerDebtPaymentService $debtPaymentService, Request $req)
  {


    $id = $req->input('id');


    if (!empty($id)) {

      $sale = Sale::find($id);
      $received =  $req->input('received');
      $date =  $req->input('date');

      $balance = $sale->amount - ($sale->paid_amount + $received);

      if ($balance == 0) {
        $paid_amount = $sale->amount;
        $fully_paid = 1;
      } else {
        $paid_amount = $sale->paid_amount + $received;
        $fully_paid = 0;
      }

      $response = Sale::where('id', $id)
        ->update([
          'paid_amount' => $paid_amount,
          'balance' => $balance,
          'fully_paid' => $fully_paid,
        ]);

      if ($response) {

        $paymentDetails = [
          'sale_id' => $id,
          'amount_paid' => $paid_amount,
          'balance' => $balance,
          'date' => $date,
        ];
        $debtPaymentService->recordPayment($paymentDetails);
        $action = "updated sale debt details for customer " . $sale->customer . "";
        LogsController::logger($req, $action, now());
        $dataArr = array(
          "code" => '200',
          "message" => $action,
          "method" => "CustomersController@updateCustomerDebts"
        );
        LogAfterRequest::LogRequest($req, $dataArr);
        $sessionVariable = 'success';
        $responseInfo = $this->SuccessMessage($action);
      } else {
        $messageErr = "Unable to update customer debt!";
        $dataArr = array(
          "code" => '101',
          "message" => $messageErr,
          "method" => "CustomersController@updateCustomerDebts"
        );
        LogAfterRequest::LogRequest($req, $dataArr);
        $sessionVariable = 'fail';
        $responseInfo = $this->FailedMessage($messageErr);
      }
    } else {
      $messageErr = "Unable to get sale id!";
      $dataArr = array(
        "code" => '101',
        "message" => $messageErr,
        "method" => "CustomersController@updateCustomerDebts"
      );
      LogAfterRequest::LogRequest($req, $dataArr);
      $sessionVariable = 'fail';
      $responseInfo = $this->FailedMessage($messageErr);
    }
    $arr = $this->GetDebtStats();

    return response()
      ->json([
        $sessionVariable => $responseInfo,
        'total_debts' => $arr['total_debts'],
        'total_debtors' => $arr['total_debtors'],
      ]);
  }


  private function GetDebtStats()
  {
    $total_debtors = Sale::where('balance', '>', 0)->where('fully_paid', 0)->count();
    $total_debts = Sale::where('balance', '>', 0)->where('fully_paid', 0)->sum('balance');
    $data = array(
      'total_debtors' => $total_debtors,
      'total_debts' => $total_debts,
    );

    return $data;
  }
}
