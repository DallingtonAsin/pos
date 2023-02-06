<?php

namespace App\Http\Controllers;

use App\Models\StockCat;
use App\Imports\ImportStockCats;
use App\Exports\ExportStockCats;
use Illuminate\Http\Request;
use App\Http\Controllers\LogsController;
use App\Http\Controllers\LogAfterRequest;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use App\DataTables\StockCatsDataTable;
use Illuminate\Support\Str;
use  App\Helpers\Constants as Constant;
use Excel;
use App\Helpers\Helper;

class StockCatsController extends Controller
{

  public $controller;
  public function __construct()
  {
    $this->controller = 'StockCatsController';

  }


  public function StockCatAjaxIndex(StockCatsDataTable $dataTable)
  {
      return $dataTable->render('pages.main.product-categories');
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $pdt_categories = StockCat::all();
    $no_of_categories = StockCat::count();
    return view('pages.main.product-categories')
                ->with(compact('pdt_categories', 'no_of_categories'));
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    return view('pages.main.product-categories');
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
      'item-category' => 'required',

    ]);

    $pdt_category = new StockCat;
    $pdt_category->item_category = $itemCategory = request('item-category');


    $item_category_update_status = $pdt_category->save();
    if ($item_category_update_status) {

      $action = "recorded stock category " . $itemCategory . " into the system";
      LogsController::logger($request, $action, now());
      $dataArr = array(
        "code" => '200',
        "message" => $action,
        "method" => "StockCatsController@store"
      );
      LogAfterRequest::LogRequest($request, $dataArr);
      $sessionVariable = 'success';
      $responseInfo = $this->SuccessMessage($action);


    } else {
      $messageErr = 'Item category not recorded!';
      $dataArr = array(
        "code" => '101',
        "message" => $messageErr,
        "method" => "StockCatsController@store"
      );
      LogAfterRequest::LogRequest($request, $dataArr);

       $sessionVariable = 'fail';
       $responseInfo = $this->FailedMessage($messageErr);

    }

    $totl = $this->GetStockCatStats();

    return response()
    ->json([$sessionVariable => $responseInfo,
            'totl_no' => $totl,
    ]);



  }


  protected function GetStockCatStats()
  {
      $totl_catItems = StockCat::count();
      return $totl_catItems;
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    $itemCat = StockCat::find($id);
    return response()->json($itemCat);
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
    $itemCat = StockCat::find($id);
    return response()->json($itemCat);
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
      'item-category' => 'required',

    ]);

    $pdt_category = StockCat::find($id);

    $pdt_category->item_category = $itemCategory = request('item-category');

    $pdt_category_update_status = $pdt_category->save();

    if ($pdt_category_update_status) {

      $action = "updated details of stock category " . $itemCategory . "";
      LogsController::logger($request, $action, now());
      $dataArr = array(
        "code" => '200',
        "message" => $action,
        "method" => "StockCatsController@update"
      );
      LogAfterRequest::LogRequest($request, $dataArr);
      $sessionVariable = 'success';
      $responseInfo = $this->SuccessMessage($action);

    } else {
      $messageErr = 'Item categoryUpdate failed!';
      $dataArr = array(
        "code" => '101',
        "message" => $messageErr,
        "method" => "StockCatsController@update"
      );
      LogAfterRequest::LogRequest($request, $dataArr);

      $sessionVariable = 'fail';
      $responseInfo = $this->FailedMessage($messageErr);

    }

    $totl = $this->GetStockCatStats();

    return response()
    ->json([$sessionVariable => $responseInfo,
            'totl_no' => $totl,
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
    $pdt_category = StockCat::find($id);
    $itemCategory = $pdt_category->item_category;

    $pdt_category_delete_status = $pdt_category->delete();

    if ($pdt_category_delete_status) {

      $action = "removed stock category " . $itemCategory . "";
      LogsController::logger($request, $action, now());
      $dataArr = array(
        "code" => '200',
        "message" => $action,
        "method" => "StockCatsController@destroy"
      );
      LogAfterRequest::LogRequest($request, $dataArr);

      $sessionVariable = 'success';
      $responseInfo = $this->SuccessMessage($action);
    } else {

      $messageErr = 'Item category not deleted!!';
      $dataArr = array(
        "code" => '101',
        "message" => $messageErr,
        "method" => "StockCatsController@destroy"
      );
      LogAfterRequest::LogRequest($request, $dataArr);
      $sessionVariable = 'fail';
      $responseInfo = $this->FailedMessage($messageErr);
    }

    $totl = $this->GetStockCatStats();

    return response()
    ->json([$sessionVariable => $responseInfo,
            'totl_no' => $totl,
    ]);

  }


  public function deleteAllStockCategories(Request $request)
  {

    $result = StockCat::truncate();
    if ($result) {
      $action = "deleted all stock categories from the system";
      LogsController::logger($request, $action, now());
      $dataArr = array(
        "code" => '200',
        "message" => $action,
        "method" => "StockCatsController@deleteAllStockCategories"
      );
      LogAfterRequest::LogRequest($request, $dataArr);
      $sessionVariable = 'success';
      $responseInfo = $this->SuccessMessage($action);


    } else {
      $messageErr = 'stock item categories not deleted from the system!';
      $dataArr = array(
        "code" => '101',
        "message" => $messageErr,
        "method" => "StockCatsController@deleteAllStockCategories"
      );
      LogAfterRequest::LogRequest($request, $dataArr);
      $sessionVariable = 'fail';
      $responseInfo = $messageErr;
    }

    $totl = $this->GetStockCatStats();

    return response()
    ->json([$sessionVariable => $responseInfo,
            'totl_no' => $totl,
    ]);

  }


  public function RemoveSelected(Request $request)
    {
        try {
            $ids =  $request->input('selected_rows');
            $deletedStockCats = array();

            if (count($ids) > 0) {
                foreach ($ids as $id) {
                    $findId = StockCat::find($id);
                    $findId->delete();
                    array_push($deletedStockCats, $findId->item_category);
                }
            }
            $sessionVariable = 'success';
            $deletedStockCatsStr = implode(", ", $deletedStockCats);
            $action = "removed stock categories ".$deletedStockCatsStr." from the system";
            if (count($ids) == 1) {
                $action = Str::replaceFirst('categories', 'category', $action);
            }
            $response = $this->SuccessMessage($action);

            $dataArr = array("code" => '200',
                "message" => $action,
                "method" => "".$this->controller."@RemoveSelected"
            );
            LogsController::logger($request, $action, now());
            LogAfterRequest::LogRequest($request, $dataArr);

              $totl = $this->GetStockCatStats();
              return response()
              ->json([$sessionVariable => $response,
                      'totl_no' => $totl,
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


  public function importCategories(Request $request)
  {
    $this->validate(
      $request,
      ['select_file' => 'required|mimes:xls,xlsx'],
      ['select_file.mimes' => 'Please select only excel files to import stock categories data']
    );
    $importSuccess = Excel::import(new ImportStockCats, request()->file('select_file'));
    if ($importSuccess) {
      $action = "imported an excel file of stock categories into the system";
      LogsController::logger($request, $action, now());
      $dataArr = array(
        "code" => '200',
        "message" => $action,
        "method" => "StockCatsController@importCategories"
      );
      LogAfterRequest::LogRequest($request, $dataArr);
      return back()
        ->with('success', $this->SuccessMessage($action));
    } else {
      $messageErr = 'Categories data not imported!';
      $dataArr = array(
        "code" => '101',
        "message" => $messageErr,
        "method" => "StockCatsController@importCategories"
      );
      LogAfterRequest::LogRequest($request, $dataArr);
      return back()
        ->with('fail', $messageErr);
    }
  }


  /**
   * @return \Illuminate\Support\Collection
   */
  public function exportCategories()
  {
    return Excel::download(new ExportStockCats, 'stock-categories.xlsx');
  }


  protected function SuccessMessage($action)
  {
    $message = "You have successfully ".$action."";
    return $message;
  }

  protected function FailedMessage($failmsg)
  {
    return $failmsg;
  }



}
