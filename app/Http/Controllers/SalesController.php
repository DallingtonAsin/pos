<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Expense;
use App\Exports\ExportSales;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\LogsController;
use App\Http\Controllers\LogAfterRequest;
use App\DataTables\SalesDataTable;
use App\DataTables\SalesWithDebtsDataTable;
use App\DataTables\TodaySalesDataTable;
use App\DataTables\TodaySalesWithDebtsDataTable;
use App\Exports\DailySalesReport;
use Illuminate\Support\Str;
use App\Helpers\Helper;
use  App\Helpers\Constants as Constant;
use App\Models\SupplierCredit;
use App\Models\SupplierDebt;
use App\Repositories\CreditSaleRepository;
use Excel;
use DataTable;


class SalesController extends Controller
{

  protected $controller, $creditSaleRepository;
  public function __construct(CreditSaleRepository $creditSaleRepository)
  {
    $this->controller = 'SalesController';
    $this->creditSaleRepository = $creditSaleRepository;
  }


  protected function GetSalesExcelFileReport()
  {
    try {
      return Excel::download(new DailySalesReport, 'daily-sales-report.xlsx');
    } catch (\Exception $ex) {
      dd($ex->getMessage());
    }
  }


  protected function getCustomSalesReview($startDate, $endDate)
  {

    $total_cost = Sale::whereBetween('date', [$startDate, $endDate])->sum('total_buying_cost');
    $total_sales = Sale::whereBetween('date', [$startDate, $endDate])->sum('amount');

    $total_expenses = Expense::whereBetween('date_of_expenditure', [$startDate, $endDate])->sum('amount');
    $cost_of_damages = Helper::getPeriodicDamageCost($startDate, $endDate);

    $supplierCredit = (SupplierCredit::whereDate('created_at', ">=", $startDate)
      ->whereDate('created_at', "<=", $endDate)
      ->sum('amount'))
      - (SupplierDebt::whereDate('created_at', ">=", $startDate)
        ->whereDate('created_at', "<=", $endDate)
        ->sum('amount'));

    $customerCredit = $this->creditSaleRepository->outstandingCreditFromSales();

    $netValue = (($total_sales - $total_cost) - ($total_expenses + $cost_of_damages) + ($supplierCredit + $customerCredit));

    return $netValue;
  }



  public function filterSales(Request $request)
  {

    if ($request->input('to')) {

      $startDate = $request->input('from');
      $endDate = $request->input('to');

      $data = Sale::whereBetween('date', [$startDate, $endDate])->orderBy('date', 'desc')->get();
      $totl_filtered = Sale::whereBetween('date', [$startDate, $endDate])->count();
      $volume_of_filteredsales = Sale::whereBetween('date', [$startDate, $endDate])->sum('amount');
      $netValue = $this->getCustomSalesReview($startDate, $endDate);

      return DataTable::of($data)
        ->addIndexColumn()
        ->addColumn('action', function ($sale) {

          $btn = "";

          if (Gate::allows('isAdmin')) {

            $btn .= '<a href="javascript:void(0)" data-toggle="tooltip"
                  data-id="' . $sale->id . '" data-item="' . $sale->item . '" data-original-title="Edit" id="edit-sale"
                  class="px-3 py-1 border border-success rounded mx-2 edit-sale pr-3">
                  <span class="fa fa-pen text-success"></span></a>';

            $btn .= '<a href="javascript:void(0);" id="delete-sale"
                  data-toggle="tooltip" data-original-title="Delete"
                  data-id="' . $sale->id . '" class="px-3 py-1 border border-danger rounded mx-2">
                  <span class="fa fa-trash-alt text-danger" ></span></a>';
          }

          $btn .= '<a href="javascript:void(0);" id="view-sale"
                  data-toggle="tooltip" data-original-title="View"
                  data-id="' . $sale->id . '" class="px-3 py-1 border border-secondary rounded text-secondary">
                  <i class="fa fa-eye" ></i></a>';

          return $btn;
        })->addColumn('checkbox', function ($sale) {
          $checkBox = '<input type="checkbox" id="' . $sale->id . '"/>';
          return $checkBox;
        })->addColumn('cashier', function ($sale) {
          $cashier = Helper::getUser($sale->cashier_id);
          return $cashier->first_name . ' ' . $cashier->last_name;
        })->editColumn('date', function ($sale) {
          $date_of_sale = $sale->date . ' ' . $sale->time;
          return date('Y-m-d H:i A', strtotime($date_of_sale));
        })->addColumn('customer', function ($sale) {
          $customer_name = null;
          if ($sale->customer_id) {
            $customer = Helper::getCustomer($sale->customer_id);
            $customer_name = $customer->name;
          }
          return $customer_name;
        })->editColumn('quantity', function ($data) {
          return Helper::convertNumber($data->quantity);
        })->editColumn('selling_price', function ($data) {
          return Helper::convertNumber($data->selling_price);
        })->editColumn('total_cost', function ($data) {
          return Helper::convertNumber($data->total_cost);
        })->editColumn('discount', function ($data) {
          return Helper::convertNumber($data->discount);
        })->editColumn('amount', function ($data) {
          return Helper::convertNumber($data->amount);
        })->rawColumns(['action', 'checkbox'])->with([
          "totl_filtered" => $totl_filtered,
          "volume" => $volume_of_filteredsales,
          "netValue" => $netValue,
        ])->make(true);
    }
    return view('pages.main.sales');
  }

  public function filterSalesWithDebts(Request $request)
  {

    if ($request->input('to')) {

      $startDate = $request->input('from');
      $endDate = $request->input('to');

      $data = Sale::whereBetween('date', [$startDate, $endDate])->orderBy('date', 'desc')->get();
      $totl_filtered = Sale::whereBetween('date', [$startDate, $endDate])->count();
      $volume_of_filteredsales = Sale::whereBetween('date', [$startDate, $endDate])->sum('amount');

      $netValue = $this->getCustomSalesReview($startDate, $endDate);

      return DataTable::of($data)->addIndexColumn()
        ->addColumn('checkbox', function ($sale) {
          $checkBox = '<input type="checkbox" id="' . $sale->id . '"/>';
          return $checkBox;
        })->addColumn('cashier', function ($sale) {
          $cashier = Helper::getUser($sale->cashier_id);
          return $cashier->first_name . ' ' . $cashier->last_name;
        })->addColumn('customer', function ($sale) {
          $customer_name = null;
          if ($sale->customer_id) {
            $customer = Helper::getCustomer($sale->customer_id);
            $customer_name = $customer->name;
          }
          return $customer_name;
        })->editColumn('quantity', function ($data) {
          return Helper::convertNumber($data->quantity);
        })->editColumn('selling_price', function ($data) {
          return Helper::convertNumber($data->selling_price);
        })->editColumn('amount', function ($data) {
          return Helper::convertNumber($data->amount);
        })->editColumn('discount', function ($data) {
          return Helper::convertNumber($data->discount);
        })->addColumn('action', function ($sale) {

          $btn = "";

          $btn .= '<a href="javascript:void(0);" id="view-sale"
          data-toggle="tooltip" data-original-title="View"
           data-id="' . $sale->id . '" class="text-info bolded pl-4">
          <i class="fa fa-eye" ></i></a>';

          if (Gate::allows('isAdmin')) {

            $btn .= '<a href="javascript:void(0);" id="delete-sale"
          data-toggle="tooltip" data-original-title="Delete"
           data-id="' . $sale->id . '" class="trash-btn pl-4">
          <span class="fa fa-trash-alt"></span></a>';
          }

          return $btn;
        })->rawColumns(['action', 'checkbox'])->with([
          "totl_filtered" => $totl_filtered,
          "volume" => $volume_of_filteredsales,
          "netValue" => $netValue,
        ])->make(true);
    }
    return view('pages.main.sales-with-debts');
  }



  public function GetSales(SalesDataTable $dataTable)
  {
    return $dataTable->render('pages.main.sales');
  }

  public function getTodaySales(TodaySalesDataTable $dataTable)
  {
    return $dataTable->render('pages.main.sales-with-debts');
  }

  public function GetSalesWithDebts(SalesWithDebtsDataTable $dataTable)
  {
    return $dataTable->render('pages.main.sales-with-debts');
  }

  public function getTodaySalesWithDebts(TodaySalesWithDebtsDataTable $dataTable)
  {
    return $dataTable->render('pages.main.sales');
  }


  protected function GetDailySalesReview()
  {

    $total_number_of_sales = Sale::where('date', Date('Y-m-d'))->count();
    $value1 = Sale::where('date', Date('Y-m-d'))->sum('total_buying_cost');
    $total_sales = $value2 = Sale::where('date', Date('Y-m-d'))->sum('amount');
    $total_expenses = Expense::where('date_of_expenditure', Date('Y-m-d'))->sum('amount');
    $cost_of_damages = Helper::getPeriodicDamageCost(Date('Y-m-d'), Date('Y-m-d'));
    $net_supplier_value = (SupplierCredit::whereDate('created_at', Date('Y-m-d'))->sum('amount')) - (SupplierDebt::whereDate('created_at', Date('Y-m-d'))->sum('amount'));
    $net_customer_value = $this->creditSaleRepository->outstandingCreditFromSales(null, null, Date('Y-m-d'));

    $netValue = (($value2 - $value1) - ($total_expenses + $cost_of_damages) + ($net_supplier_value + $net_customer_value));

    $data = array(
      'totl_no' => $total_number_of_sales,
      'totl_sales' => $total_sales,
      'NetWorth' => $netValue
    );
    return $data;
  }

  public function salesForToday(Request $request)
  {

    $request->session()->forget('filtered_sales');
    $arr = $this->GetDailySalesReview();
    $today = Date('Y-m-d');

    $today_sales = Sale::whereDate('date', $today)->get();
    $all_sales = Sale::where('date', Date('Y-m-d'))->get();

    $sales_made_today = Sale::whereDate('date', $today)->sum('amount');
    $today_credit_sales =  $this->creditSaleRepository->outstandingCreditFromSales(null, null, $today);
    $sales_made_today = $sales_made_today - $today_credit_sales;

    $totl_no =  $arr['totl_no'];
    $total_sales =  $arr['totl_sales'];
    $netValue =  $arr['NetWorth'];

    ($netValue > 0)
      ? $net_title = "Net Profit made: shs"
      : $net_title = "Losses made: shs";

    $menu_selected = 'sales';

    return view('pages.main.sales')->with(
      compact(
        'today_sales',
        'totl_no',
        'all_sales',
        'netValue',
        'sales_made_today',
        'total_sales',
        'menu_selected'
      )
    );
  }


  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {

    $request->session()->forget('filtered_sales');
    $arr = $this->GetSalesReview();
    $today = Date('Y-m-d');

    $today_sales = Sale::whereDate('date', $today)->get();
    $all_sales = Sale::get();

    $sales_made_today = Sale::whereDate('date', $today)->sum('amount');
    $today_credit_sales =  $this->creditSaleRepository->outstandingCreditFromSales(null, null, $today);
    $sales_made_today = $sales_made_today - $today_credit_sales;

    $totl_no = $arr['totl_no'];
    $total_sales = $arr['totl_sales'];
    $netValue = $arr['NetWorth'];

    ($netValue > 0)
      ? $net_title = "Net Profit made: shs"
      : $net_title = "Losses made: shs";

    // if ($request->ajax()) {
    //   $this->GetSales();
    // }

    return view('pages.main.sales')->with(
      compact(
        'today_sales',
        'totl_no',
        'all_sales',
        'netValue',
        'sales_made_today',
        'total_sales'
      )
    );
  }



  public function salesWithDebtsIndex(Request $request)
  {

    $today = Date('Y-m-d');
    $no_of_credit_sales =  $this->creditSaleRepository->count();
    $today_credit_sales =  $this->creditSaleRepository->outstandingCreditFromSales(null, null, $today);
    $outstanding_credit_sales = $this->creditSaleRepository->outstandingCreditFromSales();

    return view('pages.main.sales-with-debts')->with(
      compact(
        'no_of_credit_sales',
        'today_credit_sales',
        'outstanding_credit_sales'
      )
    );
  }


  private function GetSalesReview()
  {

    $total_number_of_sales = Sale::count();
    $total_sales = Sale::sum('amount');
    $total_expenses = Expense::sum('amount');
    $cost_of_damages = Helper::getDamageCost();
    $total_initial_cost = Sale::sum('total_buying_cost');
    $supplier_debts = (SupplierCredit::sum('amount')) - (SupplierDebt::sum('amount'));
    $customer_debts = $this->creditSaleRepository->outstandingCreditFromSales();
    $netValue = (($total_sales - $total_initial_cost) - ($total_expenses + $cost_of_damages) + ($supplier_debts + $customer_debts));

    $data = array(
      'totl_no' => $total_number_of_sales,
      'totl_sales' => $total_sales,
      'NetWorth' => $netValue
    );
    return $data;
  }

  private function GetSalesWithDebtsReview()
  {

    $today = Date('Y-m-d');
    if (Gate::allows('isAdmin')) {

      $total_number_of_sales = Sale::count();
      $total_sales = Sale::sum('amount');
      $total_expenses = Expense::sum('amount');
      $cost_of_damages = Helper::getDamageCost();
      $total_initial_cost = Sale::sum('total_buying_cost');
      $supplier_debts = (SupplierCredit::sum('amount')) - (SupplierDebt::sum('amount'));
      $customer_debts = $this->creditSaleRepository->outstandingCreditFromSales();
      $netValue = (($total_sales - $total_initial_cost) - ($total_expenses + $cost_of_damages) + ($supplier_debts + $customer_debts));
    } else {

      $total_number_of_sales = Sale::whereDate('date', $today)->count();
      $total_sales = Sale::whereDate('date', $today)->sum('amount');
      $netValue = 0;
    }


    $data = array(
      'totl_no' => $total_number_of_sales,
      'totl_sales' => $total_sales,
      'NetWorth' => $netValue
    );
    return $data;
  }



  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    return view('pages.main.sales');
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    //
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    $sales = Sale::find($id);
    return response()->json($sales);
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
    $sales = Sale::find($id);
    return response()->json($sales);
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
  }

  public function updateSaleRecord(Request $request)
  {

    if ($request->has('item_id')) {

      $item_id = $request->input('item_id');
      $date_of_sale = $request->input('date_of_sale');
      $sale = Sale::find($item_id);
      $recordedSaleDate = $sale->date;
      $item_sale_date = isset($date_of_sale) ? $date_of_sale : $recordedSaleDate;

      $sale->date = $item_sale_date;
      $saveResponse = $sale->save();

      if ($saveResponse) {

        $action = "updated date of sale for sold item " . $sale->item . "";
        LogsController::logger($request, $action, now());
        $dataArr = array(
          "code" => '200',
          "message" => $action,
          "method" => "" . $this->controller . "@updateSaleRecord"
        );
        LogAfterRequest::LogRequest($request, $dataArr);
        $sessionVariable = 'success';
        $responseInfo = $this->ActionMessage($action);
      } else {
        $messageErr = "Unable to update sale details of item " . $sale->item . "!";
        $dataArr = array(
          "code" => '101',
          "message" => $messageErr,
          "method" => "" . $this->controller . "@updateSaleRecord"
        );
        LogAfterRequest::LogRequest($request, $dataArr);
        $sessionVariable = 'error';
        $responseInfo = $this->FailedMessage($messageErr);
      }
    } else {
      $messageErr = "Unable to find sale item id";
      $dataArr = array(
        "code" => '101',
        "message" => $messageErr,
        "method" => "" . $this->controller . "@updateSaleRecord"
      );
      LogAfterRequest::LogRequest($request, $dataArr);
      $sessionVariable = 'error';
      $responseInfo = $this->FailedMessage($messageErr);
    }

    $arr = $this->GetSalesReview();
    return response()
      ->json([
        $sessionVariable => $responseInfo,
        'totl_no' => $arr['totl_no'],
        'totl_sales' => $arr['totl_sales'],
        'net_worth' => $arr['NetWorth'],
      ]);
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy(Request $request, $id)
  {

    $sale = Sale::find($id);
    $sale_item = $sale->item;
    $sale_delete_status = $sale->delete();
    if ($sale_delete_status) {

      $action = "deleted item " . $sale_item . " from list of sold items in the system";
      LogsController::logger($request, $action, now());
      $dataArr = array(
        "code" => '200',
        "message" => $action,
        "method" => "" . $this->controller . "@store"
      );
      LogAfterRequest::LogRequest($request, $dataArr);

      $sessionVariable = 'success';
      $responseInfo = $this->SuccessMessage($action);
    } else {
      $messageErr = "Sale not deleted!";
      $dataArr = array(
        "code" => '101',
        "message" => $messageErr,
        "method" => "" . $this->controller . "@store"
      );
      LogAfterRequest::LogRequest($request, $dataArr);
      $sessionVariable = 'fail';
      $responseInfo = $this->FailedMessage($messageErr);
    }

    $arr = $this->GetSalesReview();
    return response()
      ->json([
        $sessionVariable => $responseInfo,
        'totl_no' => $arr['totl_no'],
        'totl_sales' => $arr['totl_sales'],
        'net_worth' => $arr['NetWorth'],
      ]);
  }

  public function RemoveSelected(Request $request)
  {
    try {
      $ids =  $request->input('selected_rows');
      $deletedSales = array();

      if (count($ids) > 0) {
        foreach ($ids as $id) {
          $findId = Sale::find($id);
          $findId->delete();
          array_push($deletedSales, $findId->item);
        }
      }
      $sessionVariable = 'success';
      $deletedSalesStr = implode(", ", $deletedSales);
      $action = "removed sale items " . $deletedSalesStr . " from the system";
      if (count($ids) == 1) {
        $action = Str::replaceFirst('items', 'item', $action);
      }
      $response = $this->SuccessMessage($action);

      $dataArr = array(
        "code" => '200',
        "message" => $action,
        "method" => "" . $this->controller . "@RemoveSelected"
      );
      LogsController::logger($request, $action, now());
      LogAfterRequest::LogRequest($request, $dataArr);

      $arr = $this->GetSalesReview();
      return response()
        ->json([
          $sessionVariable => $response,
          'totl_no' => $arr['totl_no'],
          'totl_sales' => $arr['totl_sales'],
          'net_worth' => $arr['NetWorth'],
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

  public function GetItem($id)
  {
    $item = Sale::where('id', $id)->value('item');
    return response()
      ->json(['item' => $item]);
  }



  /**
   * @return \Illuminate\Support\Collection
   */
  public function exportSales()
  {
    return Excel::download(new ExportSales, 'sales.xlsx');
  }

  protected function ActionMessage($action)
  {
    $message = "You have successfully " . $action . "";
    return $message;
  }

  protected function SuccessMessage($action)
  {
    $message = "You have successfully " . $action . "";
    return $message;
  }

  protected function FailedMessage($failmsg)
  {
    return $failmsg;
  }
}
