<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Controllers\LogsController;
use App\Http\Controllers\LogAfterRequest;
use App\Models\Supplier;
use App\Imports\ImportSuppliers;
use App\Exports\ExportSuppliers;
use App\DataTables\SuppliersDataTable;
use Illuminate\Support\Str;
use  App\Helpers\Constants as Constant;
use DataTable;
use Excel;
use App\Helpers\Helper;

class SuppliersController extends Controller
{


    public $controller;
    public function __construct()
    {
        $this->controller = 'SuppliersController';
    }



    public function GetSuppliers(SuppliersDataTable $dataTable)
    {
        return $dataTable->render('pages.main.suppliers');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $table ="suppliers";
        $primaryKey = "id";

        try{

        $arr = $this->GetSumupDetails();
        $number_of_suppliers = $arr['totl_no'];
        $total_credit = $arr['totl_credit'];
        $total_debts = $arr['totl_debt'];

        return view('pages.main.suppliers')->with([
        'number_of_suppliers' => $number_of_suppliers,
        'total_credit' => $total_credit,
        'total_debts' => $total_debts,

       ]);

    }
        catch(ModelNotFoundException $ex){
            throw new ModelNotFoundException("Not found what you are looking for");
        }
        catch(\Exception $ex){
            return abort("405", "We have caught exception ".$ex->getMesage()." for you");
        }

    }

    protected function GetSumupDetails()
    {
        $number_of_suppliers = Supplier::count();
        $total_credit = DB::table('suppliers')->sum('credit');
        $total_debts = DB::table('suppliers')->sum('debt');
        $data = array(
               'totl_no' => $number_of_suppliers,
               'totl_credit' => $total_credit,
               'totl_debt' => $total_debts
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
        return view('pages.main.suppliers');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // $request->validate([
        //     'name' => 'required',
        //     'address' => 'required',
        //     'contact' => 'required'
        // ]);

        $supplierId = $request->input('id');
        $supplier_name = $request->input('name');
        $address = $request->input('address');
        $contact = $request->input('contact');
        $email = $request->input('email');
        $debt = Helper::Numberize($request->input('debt'));
        $credit = Helper::Numberize($request->input('credit'));

        empty($email)? $email = null : $email = $email;
        empty($debt)? $debt = null : $debt = $debt;
        empty($credit)? $credit = null : $credit = $credit;

        (empty($supplierId)) ? $keyAction = 'registered' : $keyAction = 'updated';

        if(isset($supplierId)){

        $response = Supplier::where('id', $supplierId)
        ->update(['name' => $supplier_name,
         'address' => $address,
         'contact' => $contact,
         'email' => $email,
         'debt' => $debt,
         'credit' => $credit,
         ]);

        }else{

         $supplier = new Supplier();
         $supplier->name = $supplier_name;
         $supplier->address = $address;
         $supplier->contact = $contact;
         $supplier->email = $email;
         $supplier->debt = $debt;
         $supplier->credit = $credit;
         $response = $supplier->save();

        }


        
        if($response){

           $action = "".$keyAction." supplier ".$supplier_name."";
           LogsController::logger($request, $action, now());
           $dataArr = array("code" => '200',
            "message" => $action,
            "method" => "SuppliersController@store");
           LogAfterRequest::LogRequest($request, $dataArr);
           $sessionVariable = 'success';
           $responseInfo = $this->SuccessMessage($action);

       }
       else{
          $messageErr = "registering of supplier details not failed!";
          $dataArr = array("code" => '101',
          "message" => $messageErr,
          "method" => "SuppliersController@store");
          LogAfterRequest::LogRequest($request, $dataArr);
          $sessionVariable = 'fail';
          $responseInfo = $this->FailedMessage($messageErr);

      }

        $arr = $this->GetSumupDetails();

         return response()
         ->json([$sessionVariable => $responseInfo,
                 'totl_no' => $arr['totl_no'],
                 'totl_credit' => $arr['totl_credit'],
                 'totl_debt' => $arr['totl_debt'],
         ]);

    //   return back()
    //   ->with($sessionVariable, $responseInfo);

  }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $supplier = Supplier::find($id);
        return response()->json($supplier);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $supplier = Supplier::find($id);
        return response()->json($supplier);
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
            'name' => 'required',
            'address' => 'required',
            'contact' => 'required',
        ]);

        $supplier = Supplier::find($id);

        $supplier->name = $supplier_name = $request->input('name');
        $supplier->address = $request->input('address');
        $supplier->contact = $request->input('contact');
        $email = $request->input('email');
        $debt = Helper::Numberize($request->input('debt'));
        $credit = Helper::Numberize($request->input('credit'));

        empty($email)? $supplier->email = "" : $supplier->email = $email;
        empty($debt)? $supplier->debt = 0 : $supplier->debt = $debt;
        empty($credit)? $supplier->credit = 0 : $supplier->credit = $credit;

        $save_status = $supplier->save();

        if($save_status){

          $action = "updated details of supplier ".$supplier_name."";
          LogsController::logger($request, $action, now());
          $dataArr = array("code" => '200',
          "message" => $action,
          "method" => "SuppliersController@update");
          LogAfterRequest::LogRequest($request, $dataArr);

          return back()->with("success", $this->SuccessMessage($action));
      }
      else
      {
          $messageErr = "supplier details not updated!";
          $dataArr = array("code" => '101',
          "message" => $messageErr,
          "method" => "SuppliersController@update");
          LogAfterRequest::LogRequest($request, $dataArr);
          return back()->with('fail', $messageErr);
      }
  }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {


        $method = "SuppliersController@destroy";

        $supplier_name = Supplier::where('id', $id)->value('name');
        $response = Supplier::find($id)->delete();

        if($response){

          $action = "removed supplier ".$supplier_name." from the system";
          LogsController::logger($request, $action, now());
          $dataArr = array("code" => '200',
          "message" => $action,
          "method" => $method);
          LogAfterRequest::LogRequest($request, $dataArr);
          $sessionVariable = 'success';
          $responseInfo = $this->SuccessMessage($action);

      }
      else
      {

          $messageErr = "supplier not removed";
          $dataArr = array("code" => '101',
          "message" => $messageErr,
          "method" => $method);
          LogAfterRequest::LogRequest($request, $dataArr);
          $sessionVariable = 'fail';
          $responseInfo = $this->FailedMessage($messageErr);

   }

   $arr = $this->GetSumupDetails();

   return response()
   ->json([$sessionVariable => $responseInfo,
           'totl_no' => $arr['totl_no'],
           'totl_credit' => $arr['totl_credit'],
           'totl_debt' => $arr['totl_debt'],
   ]);

}


public function deleteAllSuppliers(Request $request)
{
    $result = Supplier::truncate();
    if($result){

        $action = "deleted all suppliers from the system";
        LogsController::logger($request, $action, now());
        $dataArr = array("code" => '200',
        "message" => $action,
        "method" => "SuppliersController@deleteAllSuppliers");
        LogAfterRequest::LogRequest($request, $dataArr);
        $sessionVariable = 'success';
        $responseInfo = $this->SuccessMessage($action);
      //  return back()->with("success", $this->SuccessMessage($action));
    }
    else
    {
       $messageErr = "suppliers not removed from the system!";
       $dataArr = array("code" => '101',
       "message" => $messageErr,
       "method" => "SuppliersController@deleteAllSuppliers");
       LogAfterRequest::LogRequest($request, $dataArr);
       $sessionVariable = 'fail';
       $responseInfo = $messageErr;
      // return back()->with('fail', $messageErr);
   }

   $arr = $this->GetSumupDetails();

    return response()
    ->json([$sessionVariable => $responseInfo,
            'totl_no' => $arr['totl_no'],
            'totl_credit' => $arr['totl_credit'],
            'totl_debt' => $arr['totl_debt'],
    ]);

}

public function importSuppliers(Request $request)
{

   $this->validate($request,
      ['select_file' => 'required|mimes:xls,xlsx'],
      ['select_file.mimes' => 'Please select only excel files to import suppliers']
  );

   $importSuccess = Excel::import(new ImportSuppliers, request()->file('select_file'));

   if($importSuccess){

     $action = "imported an excel file of suppliers into the system";
     LogsController::logger($request, $action, now());
     $dataArr = array("code" => '200',
     "message" => $action,
     "method" => "SuppliersController@importSuppliers");
     LogAfterRequest::LogRequest($request, $dataArr);
     return back()->with('success', $this->SuccessMessage($action));
 }
 else
 {
   $messageErr = "Excel suppliers data not imported!";
   $dataArr = array("code" => '101',
   "message" => $messageErr,
   "method" => "SuppliersController@importSuppliers");
   LogAfterRequest::LogRequest($request, $dataArr);
   return back()->with('fail', $messageErr);
}


}


 public function RemoveSelected(Request $request)
    {
        try {
            $ids =  $request->input('selected_rows');
            $DeletedSuppliers = array();

            if (count($ids) > 0) {
                foreach ($ids as $id) {
                    $findId = Supplier::find($id);
                    $findId->delete();
                    array_push($DeletedSuppliers, $findId->name);
                }
            }
            $sessionVariable = 'success';
            $deletedSuppliersStr = implode(", ", $DeletedSuppliers);
            $action = "removed suppliers ".$deletedSuppliersStr." from the system";
            if (count($ids) == 1) {
                $action = Str::replaceFirst('suppliers', 'supplier', $action);
            }
            $response = $this->SuccessMessage($action);

            $dataArr = array("code" => '200',
                "message" => $action,
                "method" => "SuppliersController@RemoveSelected"
            );
            LogsController::logger($request, $action, now());
            LogAfterRequest::LogRequest($request, $dataArr);

            $arr = $this->GetSumupDetails();
            $number_of_suppliers = $arr['totl_no'];

            $arr = $this->GetSumupDetails();

            return response()
            ->json([$sessionVariable => $response,
                    'totl_no' => $arr['totl_no'],
                    'totl_credit' => $arr['totl_credit'],
                    'totl_debt' => $arr['totl_debt'],
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


   /**
      * @return \Illuminate\Support\Collection
      */
   public function exportSuppliers()
   {
      return Excel::download(new ExportSuppliers, 'suppliers.xlsx');
  }




  protected function SuccessMessage($msg)
{
  $message = "You have successfully ".$msg."";
  return $message;
}


protected function FailedMessage($failmsg)
{
  $message = "".$failmsg."";
  return $message;
}






}
