<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\StockCat;
use App\Models\Supplier;
use App\Imports\ImportStock;
use App\Exports\ExportStock;
use Illuminate\Http\Request;
use App\Http\Controllers\LogsController;
use App\Http\Controllers\LogAfterRequest;
use Maatwebsite\Excel\Facades\Excel;
use App\DataTables\StockDataTable;
use Illuminate\Support\Str;
use  App\Helpers\Constants as Constant;
use App\Helpers\Helper;


class StockController extends Controller
{

  public $controller;
  public function __construct()
  {
    $this->controller = 'StockController';
  }
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $stock = Stock::all(); //DB::select('exec GetStockProc');
    $number_of_stockItems = Stock::count();
    $stock_value = Stock::sum('total_cost_price');
    $categories = StockCat::get();
    $suppliers = Supplier::get();
    return view('pages.main.stock')->with(compact('stock', 'stock_value', 'categories', 'suppliers', 'number_of_stockItems'));
  }


  public function GetStock(StockDataTable $dataTable)
  {
    //if(Auth::check()){
    return $dataTable->render('pages.main.stock');
    // }else{
    //    return redirect('/');
    // }
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    return view('pages.main.stock');
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {

    $request->validate([
      'item' => 'required',
      'quantity' => 'required',
      'original_price' => 'required',
      'selling_price' => 'required'
    ]);

    $stock = new Stock;

    $item_code = $request->input('item_code');
    $item = $request->input('item');
    $category_id = $request->input('category');
    $supplier_id = $request->input('supplier');
    $quantity = Helper::Numberize($request->input('quantity'));

    ($request->has('thresholdQty') && $request->filled("thresholdQty"))
      ? $thresholdQty = Helper::Numberize($request->input("thresholdQty"))
      : $thresholdQty = 0;

    $expiry_date = $request->input('expiry_date');
    $buying_price = Helper::Numberize($request->input('original_price'));
    $selling_price = Helper::Numberize($request->input('selling_price'));
    $wholesale_price = Helper::Numberize($request->input('wholesale_price'));


    $stock->item_code = $item_code;
    $stock->item = $item;
    $stock->category_id = $category_id;
    $stock->supplier_id = $supplier_id;
    $stock->quantity = $quantity;
    $stock->threshold_qty = $thresholdQty;
    $expiry_date = $expiry_date;
    $stock->buying_price = $buying_price;
    $stock->selling_price = $selling_price;
    $stock->wholesale_price = $wholesale_price;
    $stock->expiry_date = empty($expiry_date) ? "" : $expiry_date;
    $saveStockResponse = $stock->save();

    if ($saveStockResponse) {

      $action = "recorded stock item " . $item . " in the system";
      LogsController::logger($request, $action, now());
      $dataArr = array(
        "code" => '200',
        "message" => $action,
        "method" => "StockController@store"
      );

      LogAfterRequest::LogRequest($request, $dataArr);

      $sessionVariable = 'success';
      $responseInfo = $this->ActionMessage($action);
    } else {
      $messageErr = "Stock addition failed!";
      $dataArr = array(
        "code" => '101',
        "message" => $messageErr,
        "method" => "StockController@store"
      );
      LogAfterRequest::LogRequest($request, $dataArr);

      $sessionVariable = 'fail';
      $responseInfo = $this->FailedMessage($messageErr);
    }


    $arr = $this->GetSumupDetails();

    return response()
      ->json([
        $sessionVariable => $responseInfo,
        'totl_stock' => $arr['totl'],
        'stock_value' => $arr['value'],
      ]);
  }

  protected function GetSumupDetails()
  {
    $totl = Stock::count();
    $stockValue = Stock::sum('total_cost_price');
    $data = array(
      'totl' => $totl,
      'value' => $stockValue,
    );
    return $data;
  }


  public function findStockItem($item_id)
  {
    try {
      $item = Stock::find($item_id);
      return response()->json(['success' => 'Ok', 'data' => $item]);
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
    $item = Stock::find($id);
    return response()->json($item);
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
    $item = Stock::find($id);
    return response()->json($item);
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

    $request->validate([
      'item' => 'required',
      'quantity' => 'required',
      'original_price' => 'required',
      'selling_price' => 'required',
    ]);

    $stock = Stock::find($id);
    $stock->item_code = $request->input('item_code');
    $stock->item = $item = $request->input('item');
    $supplier_id= $request->input('supplier');
    $category_id = $request->input('category');

    empty($category_id)
      ? $stock->category_id = $stock->category_id
      : $stock->category_id = $request->input('category');

    empty($supplier_id)
      ? $stock->supplier_id = $stock->supplier_id
      : $stock->supplier_id = $supplier_id;

    $stock->quantity = Helper::Numberize($request->input('quantity'));

    $expiry_date = $request->input('expiry_date');
    $stock->buying_price = Helper::Numberize($request->input('original_price'));
    $stock->selling_price = Helper::Numberize($request->input('selling_price'));
    $stock->wholesale_price = Helper::Numberize($request->input('wholesale_price'));

    ($request->has('thresholdQty') && $request->filled("thresholdQty"))
      ? $thresholdQty = Helper::Numberize($request->input("thresholdQty"))
      : $thresholdQty = 0;
    $stock->threshold_qty = $thresholdQty;
    (empty($expiry_date)) ? $stock->expiry_date = "" : $stock->expiry_date = $expiry_date;
    ($expiry_date == "mm/dd/yyyy") ? $stock->expiry_date = "" : $stock->expiry_date = $expiry_date;

    $saveResponse = $stock->save();
    if ($saveResponse) {

      $action = "updated details of stock item " . $item . "";
      LogsController::logger($request, $action, now());
      $dataArr = array(
        "code" => '200',
        "message" => $action,
        "method" => "StockController@update"
      );
      LogAfterRequest::LogRequest($request, $dataArr);

      $sessionVariable = 'success';
      $responseInfo = $this->ActionMessage($action);
    } else {
      $messageErr = "Stock Update failed!";
      $dataArr = array(
        "code" => '101',
        "message" => $messageErr,
        "method" => "StockController@update"
      );
      LogAfterRequest::LogRequest($request, $dataArr);

      $sessionVariable = 'fail';
      $responseInfo = $this->FailedMessage($messageErr);
    }

    $arr = $this->GetSumupDetails();

    return response()
      ->json([
        $sessionVariable => $responseInfo,
        'totl_stock' => $arr['totl'],
        'stock_value' => $arr['value'],
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
    $stock = Stock::find($id);
    $stock_item = $stock->item;
    $stock_delete_status = $stock->delete();
    if ($stock_delete_status) {

      $action = "removed item " . $stock_item . " from list of stock items";
      LogsController::logger($request, $action, now());
      $dataArr = array(
        "code" => '200',
        "message" => $action,
        "method" => "StockController@destroy"
      );
      LogAfterRequest::LogRequest($request, $dataArr);

      $sessionVariable = 'success';
      $responseInfo = $this->ActionMessage($action);
    } else {
      $messageErr = "item not removed!";
      $dataArr = array(
        "code" => '101',
        "message" => $messageErr,
        "method" => "StockController@destroy"
      );
      LogAfterRequest::LogRequest($request, $dataArr);

      $sessionVariable = 'fail';
      $responseInfo = $this->FailedMessage($messageErr);
    }

    $arr = $this->GetSumupDetails();

    return response()
      ->json([
        $sessionVariable => $responseInfo,
        'totl_stock' => $arr['totl'],
        'stock_value' => $arr['value'],
      ]);
  }

  public function deleteAllStockItems(Request $request)
  {

    $result = Stock::truncate();
    if ($result) {
      $sessionVariable = 'success';
      $action = "deleted all stock items from the system";
      LogsController::logger($request, $action, now());
      $dataArr = array(
        "code" => '200',
        "message" => $action,
        "method" => "StockController@deleteAllStockItems"
      );
      LogAfterRequest::LogRequest($request, $dataArr);
      $responseInfo = $this->ActionMessage($action);
      //return back()->with("success", $this->ActionMessage($action));
    } else {
      $sessionVariable = 'fail';
      $messageErr = "stock items not deleted from the system!";
      $dataArr = array(
        "code" => '101',
        "message" => $messageErr,
        "method" => "StockController@deleteAllStockItems"
      );
      LogAfterRequest::LogRequest($request, $dataArr);
      $responseInfo = $this->FailedMessage($messageErr);
      //return back()->with('fail', $messageErr);
    }

    $arr = $this->GetSumupDetails();

    return response()
      ->json([
        $sessionVariable => $responseInfo,
        'totl_stock' => $arr['totl'],
        'stock_value' => $arr['value'],
      ]);
  }

  public function RemoveSelected(Request $request)
  {
    try {
      $ids =  $request->input('selected_rows');
      $deletedStock = array();

      if (count($ids) > 0) {
        foreach ($ids as $id) {
          $findId = Stock::find($id);
          $findId->delete();
          array_push($deletedStock, $findId->item);
        }
      }
      $sessionVariable = 'success';
      $deletedStockStr = implode(", ", $deletedStock);
      $action = "removed stock " . $deletedStockStr . " from the system";
      if (count($ids) == 1) {
        $action = Str::replaceFirst('stock', 'stock item', $action);
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
          'totl_stock' => $arr['totl'],
          'stock_value' => $arr['value'],
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

  public function importStock(Request $request)
  {
    $this->validate(
      $request,
      ['select_file' => 'required|mimes:xls,xlsx'],
      ['select_file.mimes' => 'Please select only excel files to import stock data']
    );
    $importSuccess = Excel::import(new ImportStock, request()->file('select_file'));

    if ($importSuccess) {

      $action = "imported an excel file of stock items into the system";
      LogsController::logger($request, $action, now());
      $dataArr = array(
        "code" => '200',
        "message" => $action,
        "method" => "StockController@importStock"
      );
      LogAfterRequest::LogRequest($request, $dataArr);

      return back()->with('success', $this->ActionMessage($action));
    } else {
      $messageErr = "Excel stock data not imported!";
      $dataArr = array(
        "code" => '101',
        "message" => $messageErr,
        "method" => "StockController@importStock"
      );
      LogAfterRequest::LogRequest($request, $dataArr);
      return back()->with('fail', $messageErr);
    }
  }


  /**
   * @return \Illuminate\Support\Collection
   */
  public function exportStock()
  {
    return Excel::download(new ExportStock, 'stock.xlsx');
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

  protected function ActionMessage($action)
  {
    $message = "You have successfully " . $action . "";
    return $message;
  }

  public function fetchStockItemsAjax(Request $request)
  {
    try {
      if ($request->ajax()) {

        $stock = Stock::all();
        echo json_encode($stock);
        die();
      }
    } catch (\Exception $ex) {
      echo "Error " . $ex->getMessage();
    }
  }
}
