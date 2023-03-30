<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\Stock;
use Illuminate\Http\Request;
use App\DataTables\PurchasesDataTable;
use App\Http\Controllers\LogsController;
use App\Http\Controllers\LogAfterRequest;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;
use App\Imports\ImportPurchases;
use  App\Helpers\Constants as Constant;
use App\Helpers\Helper;

class PurchasesController extends Controller
{

  public $controller;
  public function __construct()
  {
    $this->controller = 'PurchasesController';
  }

  public function GetPurchases(PurchasesDataTable $dataTable)
  {
    return $dataTable->render('pages.main.inventory.purchases');
  }


  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $purchases = Purchase::all();

    $stock = Stock::select(['id', 'item_code', 'item'])->get();
    $suppliers = Supplier::select(['id', 'name'])->get();

    $arr = $this->GetPurchaseDetails();
    $no_of_purchases = $arr['totl_no'];
    $totl_cost_of_purchases = $arr['totl_purchases'];

    return view(
      'pages.main.inventory.purchases',
      compact(
        'purchases',
        'stock',
        'suppliers',
        'no_of_purchases',
        'totl_cost_of_purchases'
      )
    );
  }


  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    return view('pages.main.inventory.purchases');
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {

    try {

      $purchase_id = $request->input('id');
      $serial_no = $request->input('serial_no');
      $receipt_no = $request->input('receipt_no');
      $item_id = $request->input('item');
      $quantity = Helper::Numberize($request->input('quantity'));
      $cost_price_per_item = Helper::Numberize($request->input('cost_price'));
      $retail_price = Helper::Numberize($request->input('retail_price'));
      $wholesale_price = Helper::Numberize($request->input('wholesale_price'));
      $supplier_id = $request->input('supplier');
      $recorded_by = $request->user()->id;
      $date_of_purchase = $request->input('date_of_purchase');

      $purchase_data = [
        'item_id' => $item_id,
        'quantity' => $quantity,
        'cost_price_per_item' => $cost_price_per_item,
        'retail_price' => $retail_price,
        'wholesale_price' => $wholesale_price,
        'supplier_id' => $supplier_id,
        'serial_no' => $serial_no,
        'receipt_no' => $receipt_no,
        'recorded_by' => $recorded_by,
        'date_of_purchase' => $date_of_purchase
      ];


      $isStored = Purchase::create($purchase_data);
      if ($isStored) {
        $stock = Stock::where('id', $item_id);
        if ($stock->exists()) {

          $qty = $stock->first()->quantity;
          $newQty = $qty + floatval($quantity);
          $isUpdated = Stock::where('id', $item_id)->update(['quantity' => $newQty]);

          if ($isUpdated) {
            $arr = $this->GetSumupDetails();
            $purchased_item = $stock->first()->item;
            return response()->json([
              'success' => 'You have successfully added purchased item ' . $purchased_item . '',
              'data' => $arr,
            ]);
          } else {
            return response()->json(['error' => 'System has failed to update quantity in stock']);
          }
        } else {
          return response()->json(['error' => 'Stock item does not exist in the system']);
        }
      } else {
        return response()->json(['error' => 'System has failed to record purchase']);
      }
    } catch (\Exception $ex) {

      $data = array(
        'username' => auth()->user()->username,
        'error_code' => $ex->getCode(),
        'error_message' => $ex->getMessage(),
        'error_severity' => Constant::$STATUS_ERROR_SEVERITY,
        'controller' => $this->controller,
        'method' => 'store'
      );

      Helper::logError($data);
      return response()->json(['error' => $ex->getMessage()]);
    }
  }

  protected function GetSumupDetails()
  {
    $totl = Purchase::count();
    $totalValue = Purchase::sum('total_cost_price');
    $data = array(
      'totl' => $totl,
      'value' => $totalValue,
    );
    return $data;
  }


  public function findPurchase($id)
  {
    try {

      $purchase = Purchase::find($id);
      $item = Stock::find($purchase->item_id);
      $purchase->item = $item->item;
      $purchase->item_code = $item->item_code;
      return $purchase;
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
    $purchase = $this->findPurchase($id);
    return response()->json($purchase);
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
    $purchase = $this->findPurchase($id);
    return response()->json($purchase);
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

    try {

      $purchase_id = $request->input('id');

      if ($purchase_id) {

        $serial_no = $request->input('serial_no');
        $receipt_no = $request->input('receipt_no');
        $item_id = $request->input('item');
        $quantity = Helper::Numberize($request->input('quantity'));
        $cost_price_per_item = Helper::Numberize($request->input('cost_price'));
        $retail_price = Helper::Numberize($request->input('retail_price'));
        $wholesale_price = Helper::Numberize($request->input('wholesale_price'));
        $supplier_id = $request->input('supplier');
        $recorded_by = $request->user()->id;
        $date_of_purchase = $request->input('date_of_purchase');

        $purchase_data = [
          'item_id' => $item_id,
          'quantity' => $quantity,
          'cost_price_per_item' => $cost_price_per_item,
          'retail_price' => $retail_price,
          'wholesale_price' => $wholesale_price,
          'supplier_id' => $supplier_id,
          'serial_no' => $serial_no,
          'receipt_no' => $receipt_no,
          'recorded_by' => $recorded_by,
          'date_of_purchase' => $date_of_purchase
        ];


        $purchase = Purchase::where('id', $purchase_id);
        $oldQty = $purchase->first()->quantity;

        if ($purchase->update($purchase_data)) {

          $stock = Stock::where('id', $item_id);
          if ($stock->exists()) {

            $qtyDiff = $quantity - $oldQty;
            $qty = $stock->first()->quantity;
            $newQty = $qty + floatval($qtyDiff);

            $isUpdated = Stock::where('id', $item_id)->update(['quantity' => $newQty]);

            if ($isUpdated) {
              $arr = $this->GetSumupDetails();
              $purchased_item = $stock->first()->item;
              return response()->json([
                'success' => 'You have successfully added purchased item ' . $purchased_item . '',
                'data' => $arr,
              ]);
            } else {
              return response()->json(['error' => 'System has failed to update quantity in stock']);
            }
          } else {
            return response()->json(['error' => 'Stock item does not exist in the system']);
          }
        } else {
          return response()->json(['error' => 'System has failed to record purchase']);
        }
      }
    } catch (\Exception $ex) {

      $data = array(
        'username' => auth()->user()->username,
        'error_code' => $ex->getCode(),
        'error_message' => $ex->getMessage(),
        'error_severity' => Constant::$STATUS_ERROR_SEVERITY,
        'controller' => $this->controller,
        'method' => 'store'
      );

      Helper::logError($data);
      return response()->json(['error' => $ex->getMessage()]);
    }
  }

  protected function GetPurchaseDetails()
  {
    $no_of_purchases = Purchase::count();
    $totl_cost_of_purchases = Purchase::sum('total_cost_price');
    $data = array(
      'totl_no' => $no_of_purchases,
      'totl_purchases' => $totl_cost_of_purchases,
    );
    return $data;
  }


  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy(Request $request, $id)
  {

    $purchase = Purchase::find($id);
    $method = "PurchaseController@destroy";

    $purchase_item = $purchase->item;
    $deleteResp = $purchase->delete();
    if ($deleteResp) {

      $action = "removed purchase item " . $purchase_item . " from list of purchased items";
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
      $messageErr = "item not removed!";
      $dataArr = array(
        "code" => '101',
        "message" => $messageErr,
        "method" => $method
      );
      LogAfterRequest::LogRequest($request, $dataArr);

      $sessionVariable = 'fail';
      $responseInfo = $this->FailedMessage($messageErr);
    }

    $arr = $this->GetPurchaseDetails();

    return response()
      ->json([
        $sessionVariable => $responseInfo,
        'totl_no' => $arr['totl_no'],
        'totl_purchases' => $arr['totl_purchases'],
      ]);
  }

  public function deleteAllPurchases(Request $request)
  {

    $method = "Purchases@deleteAllPurchases";
    $result = Purchase::truncate();
    if ($result) {

      $action = "deleted all purchased items from the system";
      LogsController::logger($request, $action, now());
      $dataArr = array(
        "code" => '200',
        "message" => $action,
        "method" => $method
      );
      LogAfterRequest::LogRequest($request, $dataArr);
      $sessionVariable = "success";
      $responseInfo = $this->SuccessMessage($action);
    } else {

      $messageErr = "Purchase items not deleted from the system!";
      $dataArr = array(
        "code" => '101',
        "message" => $messageErr,
        "method" => $method
      );
      LogAfterRequest::LogRequest($request, $dataArr);
      $sessionVariable = "fail";
      $responseInfo = $this->FailedMessage($messageErr);
    }

    $arr = $this->GetPurchaseDetails();
    return response()
      ->json([
        $sessionVariable => $responseInfo,
        'totl_no' => $arr['totl_no'],
        'totl_purchases' => $arr['totl_purchases'],
      ]);
  }

  public function RemoveSelected(Request $request)
  {
    try {
      $ids = $request->input('selected_rows');
      $deletedPurchases = array();

      if (count($ids) > 0) {
        foreach ($ids as $id) {
          $findId = Purchase::find($id);
          $findId->delete();
          array_push($deletedPurchases, $findId->item);
        }
      }
      $sessionVariable = 'success';
      $deletedPurchasesStr = implode(", ", $deletedPurchases);
      $action = "removed purchases " . $deletedPurchasesStr . " from the system";
      if (count($ids) == 1) {
        $action = Str::replaceFirst('purchases', 'purchased item', $action);
      }
      $response = $this->SuccessMessage($action);

      $dataArr = array(
        "code" => '200',
        "message" => $action,
        "method" => "" . $this->controller . "@RemoveSelected"
      );
      LogsController::logger($request, $action, now());
      LogAfterRequest::LogRequest($request, $dataArr);

      $arr = $this->GetPurchaseDetails();
      return response()
        ->json([
          $sessionVariable => $response,
          'totl_no' => $arr['totl_no'],
          'totl_purchases' => $arr['totl_purchases'],
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

  public function importPurchasedItems(Request $request)
  {

    $this->validate(
      $request,
      ['select_file' => 'required|mimes:xls,xlsx'],
      ['select_file.mimes' => 'Please select only excel files to import purchases']
    );

    $importSuccess = Excel::import(new ImportPurchases, request()->file('select_file'));

    if ($importSuccess) {

      $action = "imported an excel file of purchases into the system";
      LogsController::logger($request, $action, now());
      $dataArr = array(
        "code" => '200',
        "message" => $action,
        "method" => "PurchasesController@importStock"
      );
      LogAfterRequest::LogRequest($request, $dataArr);

      return back()->with('success', $this->ActionMessage($action));
    } else {
      $messageErr = "Excel file of purchases not imported!";
      $dataArr = array(
        "code" => '101',
        "message" => $messageErr,
        "method" => "PurchasesController@importStock"
      );
      LogAfterRequest::LogRequest($request, $dataArr);
      return back()->with('fail', $messageErr);
    }
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
} //end of class Purchases
