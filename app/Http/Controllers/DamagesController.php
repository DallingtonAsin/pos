<?php

namespace App\Http\Controllers;
use App\Models\Damage;
use App\Models\Stock;
use App\Imports\ImportDamages;
use App\Exports\ExportDamages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\LogsController;
use App\Http\Controllers\LogAfterRequest;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use App\DataTables\DamagesDataTable;
use Illuminate\Support\Str;
use  App\Helpers\Constants as Constant;
use Excel;
use App\Helpers\Helper;

class DamagesController extends Controller
{

  public $controller;
  public function __construct()
  {
    $this->controller = 'DamagesController';

  }

  public function GetDamages(DamagesDataTable $dataTable){

    return  $dataTable->render('pages.main.damages');
   }


  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $damages = Damage::all();
    $number_of_damages = Damage::count();
    $cost_of_damages = DB::table('damages')->sum('total_cost');

    return view('pages.main.damages')->with(compact('damages', 'cost_of_damages', 'number_of_damages'));
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    return view('pages.main.damages');
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
      'damage-item' => 'required',
      'quantity' => 'required',
    ]);

    $damage = new Damage;
    $item = $request->input('damage-item');
    $quantity = Helper::Numberize($request->input('quantity'));
    $method = "DamagesController@store";

    $stockObj = $this->getItems_in_Stock();

    $inventory = array();
    foreach ($stockObj->toArray() as $value) {
      $product = $value->item;
      array_push($inventory, $product);
    }

    if (in_array($item, $inventory)) {
      $data = $this->getdetailsofDamagedItem($item);
      $available_qty = $this->getQtyBeforeAddingDamage($item);
      $new_quantity = ($available_qty - $quantity);

      $damage->item = $item;
      $damage->quantity = $quantity;

      foreach ($data as $key) {
        $damage->item_id = $key->item_code;
        $damage->category = $key->category;
        $damage->buying_price = $key->buying_price;
      }

      $save = $damage->save();

      if ($save) {
        $res = DB::table('stock')
                      ->where('item', $item)
                      ->update(['quantity' => $new_quantity]);
        if ($res) {

          $action = "recorded damaged item " . $item . "";
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
          $messageErr = "Damaged item not recorded!";
          $dataArr = array(
            "code" => '101',
            "message" => $messageErr,
            "method" => $method
          );
          LogAfterRequest::LogRequest($request, $dataArr);
          $sessionVariable = 'fail';
          $responseInfo = $this->FailedMessage($messageErr);

        }

      }
      else{

        $messageErr = "Unable to update stock";
        $dataArr = array(
          "code" => '101',
          "message" => $messageErr,
          "method" => $method
        );
        LogAfterRequest::LogRequest($request, $dataArr);
        $sessionVariable = 'fail';
        $responseInfo = $this->FailedMessage($messageErr);

      }


    } else {

      $messageErr = "Item " . $item . " not found in stock";
      $dataArr = array(
        "code" => '404',
        "message" => $messageErr,
        "method" => $method
      );
      LogAfterRequest::LogRequest($request, $dataArr);
      $sessionVariable = 'fail';
      $responseInfo = $this->FailedMessage($messageErr);

    }

          $arr = $this->GetDamagesStats();
          $totl_no = $arr['totl_no'];
          $totl_amt = $arr['totl_amt'];

          return response()
          ->json([$sessionVariable => $responseInfo,
                  'totl_no' => $totl_no,
                  'totl_amt' => $totl_amt,
          ]);


  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    $damage = Damage::find($id);
    return response()->json($damage);
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
    $damage = Damage::find($id);
    return response()->json($damage);
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
      'damage-item' => 'required',
      'quantity' => 'required',
    ]);

    $uniqueId =  $request->input('id');
    $damage = Damage::find($uniqueId);

    $item = $request->input('damage-item');
    $quantity = Helper::Numberize($request->input('quantity'));
    $method = "DamagesController@store";

    $stockObj = $this->getItems_in_Stock();

    $inventory = array();
    foreach ($stockObj->toArray() as $value) {
      $product = $value->item;
      array_push($inventory, $product);
    }

    if (in_array($item, $inventory)) {
      $data = $this->getdetailsofDamagedItem($item);
      $available_qty = $this->getQtyBeforeAddingDamage($item);
      $new_quantity = ($available_qty - $quantity);

      $damage->item = $item;
      $damage->quantity = $quantity;

      foreach ($data as $key) {
        $damage->item_id  = $key->item_code;
        $damage->category  = $key->category;
        $damage->buying_price  = $key->buying_price;
      }

      $hasSaved = Damage::where('id',  $uniqueId)->update([
                   'quantity' => $quantity,
      ]);


      if ($hasSaved) {

        $res = DB::table('stock')
                      ->where('item', $item)
                      ->update(['quantity' => $new_quantity]);
        if ($res) {

          $action = "recorded damaged item " . $item . "";
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
          $messageErr = "Damaged item not recorded!";
          $dataArr = array(
            "code" => '101',
            "message" => $messageErr,
            "method" => $method
          );
          LogAfterRequest::LogRequest($request, $dataArr);
          $sessionVariable = 'fail';
          $responseInfo = $this->FailedMessage($messageErr);

        }

      }
      else{

        $messageErr = "Unable to update stock";
        $dataArr = array(
          "code" => '101',
          "message" => $messageErr,
          "method" => $method
        );
        LogAfterRequest::LogRequest($request, $dataArr);
        $sessionVariable = 'fail';
        $responseInfo = $this->FailedMessage($messageErr);

      }


    } else {

      $messageErr = "Item " . $item . " not found in stock";
      $dataArr = array(
        "code" => '404',
        "message" => $messageErr,
        "method" => $method
      );
      LogAfterRequest::LogRequest($request, $dataArr);
      $sessionVariable = 'fail';
      $responseInfo = $this->FailedMessage($messageErr);

    }

          $arr = $this->GetDamagesStats();
          $totl_no = $arr['totl_no'];
          $totl_amt = $arr['totl_amt'];

          return response()
          ->json([$sessionVariable => $responseInfo,
                  'totl_no' => $totl_no,
                  'totl_amt' => $totl_amt,
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

    $damage = Damage::find($id);
    $method = "DamagesController@destroy";
    $item = $damage->item;
    $damage_delete_status = $damage->delete();
    if ($damage_delete_status) {

      $action = "deleted damaged item " . $item . " from the system";
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

      $messageErr = "Damaged item not deleted!";
      $dataArr = array(
        "code" => '101',
        "message" => $messageErr,
        "method" => $method
      );
      LogAfterRequest::LogRequest($request, $dataArr);
      $sessionVariable = 'fail';
      $responseInfo = $this->FailedMessage($messageErr);

    }

    $arr = $this->GetDamagesStats();

    return response()
    ->json([$sessionVariable => $responseInfo,
            'totl_no' => $arr['totl_no'],
            'totl_amt' => $arr['totl_amt'],
    ]);

  }

  protected function GetDamagesStats(){
    $totl_no = Damage::count();
    $totl_cost = Damage::sum('total_cost');
    $data = array(
            'totl_no' => $totl_no,
            'totl_amt' => $totl_cost,
    );
    return $data;
}


  protected function searchItem(Request $request){

  if($request->input('query')){
    $query = $request->input('query');
    $data = array();
    $items = DB::table("stock")
                  ->where("item_code", "like", "%".$query."%")
                  ->orWhere("item", "like", "%".$query."%")
                  ->get();

    foreach($items as $item){
      $data[] = $item->item;
      $data[] = $item->item_code;
    }
    echo json_encode($data);
  }

}


  public function deleteAllDamages(Request $request)
  {

    $result = Damage::truncate();

    if ($result) {

      $action = "deleted all damaged items from the system";
      LogsController::logger($request, $action, now());
      $dataArr = array(
        "code" => '200',
        "message" => $action,
        "method" => "DamagesController@deleteAllDamages"
      );
      LogAfterRequest::LogRequest($request, $dataArr);
      $sessionVariable = 'success';
      $responseInfo = $this->SuccessMessage($action);

    } else {
      $messageErr = "Damaged items not deleted from the system!";
      $dataArr = array(
        "code" => '101',
        "message" => $messageErr,
        "method" => "DamagesController@deleteAllDamages"
      );
      LogAfterRequest::LogRequest($request, $dataArr);
      $sessionVariable = 'fail';
      $responseInfo = $messageErr;

    }

    $arr = $this->GetDamagesStats();
    $totl_no = $arr['totl_no'];
    $totl_amt = $arr['totl_amt'];

    return response()
    ->json([$sessionVariable => $responseInfo,
            'totl_no' => $totl_no,
            'totl_amt' => $totl_amt,
    ]);

  }

 public function RemoveSelected(Request $request)
    {
        try {
            $ids =  $request->input('selected_rows');
            $deletedDamagedItems = array();

            if (count($ids) > 0) {
                foreach ($ids as $id) {
                    $findId = Damage::find($id);
                    $findId->delete();
                    array_push($deletedDamagedItems, $findId->item);
                }
            }
            $sessionVariable = 'success';
            $deleteddamagedStockStr = implode(", ", $deletedDamagedItems);
            $action = "removed damaged items ".$deleteddamagedStockStr." from the system";
            if (count($ids) == 1) {
                $action = Str::replaceFirst('items', 'item', $action);
            }
            $response = $this->SuccessMessage($action);

            $dataArr = array("code" => '200',
                "message" => $action,
                "method" => "".$this->controller."@RemoveSelected"
            );
            LogsController::logger($request, $action, now());
            LogAfterRequest::LogRequest($request, $dataArr);

            $arr = $this->GetDamagesStats();
            $totl_no = $arr['totl_no'];
            $totl_amt = $arr['totl_amt'];

            return response()
            ->json([$sessionVariable => $response,
                    'totl_no' => $totl_no,
                    'totl_amt' => $totl_amt,
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



  public function importDamages(Request $request)
  {
    $this->validate(
      $request,
      ['select_file' => 'required|mimes:xls,xlsx'],
      ['select_file.mimes' => 'Please select only excel files to import damages']
    );

    $importSuccess = Excel::import(new ImportDamages, request()->file('select_file'));
    if ($importSuccess) {

      $action = "imported an excel file of damaged items into the system";
      LogsController::logger($request, $action, now());
      $dataArr = array(
        "code" => '200',
        "message" => $action,
        "method" => "DamagesController@importDamages"
      );
      LogAfterRequest::LogRequest($request, $dataArr);
      return back()->with('success', $this->SuccessMessage($action));
    } else {
      $messageErr = "Damages data not imported!!";
      $dataArr = array(
        "code" => '101',
        "message" => $messageErr,
        "method" => "DamagesController@importDamages"
      );
      LogAfterRequest::LogRequest($request, $dataArr);
      return back()->with('fail', $messageErr);
    }
  }

  //method that gets details of damaged item from stock
  public function getdetailsofDamagedItem($item)
  {
    $data_obj = DB::select('select item_code, category, buying_price from stock where item = ?', [$item]);
    return $data_obj;
  }

  //method that gets list of items in stock
  public function getItems_in_Stock()
  {
    $items = DB::table('stock')->select('item')->get();
    return $items;
  }

  //Get Available quantity of a product before recording a damage
  public function getQtyBeforeAddingDamage($item)
  {
    $data = DB::select('select quantity from stock where item = ?', [$item]);
    foreach ($data as $value) {
      $qty = $value->quantity;
    }
    return $qty;
  }

  //Get recorded quantity of a damage before making update
  public function getRecordedQtyBeforeUpdatingDamage($item)
  {
    $data = DB::select('select quantity from damages where item = ?', [$item]);
    foreach ($data as $value) {
      $qty = $value->quantity;
    }
    return $qty;
  }





  /**
   * @return \Illuminate\Support\Collection
   */
  public function exportDamages()
  {
    return Excel::download(new ExportDamages, 'damages.xlsx');
  }

  protected function SuccessMessage($msg)
  {
    $message = "You have successfully ".$msg."";
    return $message;
  }


  protected function FailedMessage($failmsg)
  {
    return $failmsg;
  }




} //end of the class
