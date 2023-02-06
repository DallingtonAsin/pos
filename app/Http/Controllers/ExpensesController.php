<?php

namespace App\Http\Controllers;
use App\Models\Expense;
use App\Helpers\Helper;
use App\Imports\ImportExpenses;
use App\Exports\ExportExpenses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\LogsController;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\LogAfterRequest;
use App\DataTables\ExpensesDataTable;
use Illuminate\Support\Str;
use Constant;
use Excel;

class ExpensesController extends Controller
{
 public $date_of_action, $controller;

 public function __construct()
 {
  $this->date_of_action = now();
  $this->controller = 'ExpensesController';
}

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $expenses = Expense::All();
      $number_of_total_expenses = Expense::count();
      $total_expenses = DB::table('expenses')->sum('amount');
      return view('pages.main.expenses')->with(compact('expenses','total_expenses','number_of_total_expenses'));
    }

    public function GetExpenses(ExpensesDataTable $dataTable)
    {

        return $dataTable->render('pages.main.expenses');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      return view('pages.main.expenses');
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
       'expense' => 'required',
       'expenditure_amount' => 'required',
       'date_of_expense' => 'required'

     ]);



      $method = "ExpensesController@store";

      $expenseId = $request->input('id');
      $expenseType = $request->input('expense');
      $amount = Helper::Numberize($request->input('expenditure_amount'));
      $date_of_expenditure = $request->input('date_of_expense');

      if(isset($expenseId)){

         $expense = Expense::find($expenseId);
        $response = Expense::where('id', $expenseId)->update(
        ['expense_type' => $expenseType,
         'amount' =>  $amount,
         'date_of_expenditure' =>  $date_of_expenditure,
         ]);

      }else{

        $expense = new Expense;
        $expense->expense_type = $expenseType;
        $expense->amount = $amount;
        $expense->date_of_expenditure = $date_of_expenditure;
        $response = $expense->save();
      }

      if($response){

        $action = "recorded expense ".$expenseType."";
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
        $messageErr = 'Expense not recorded!';
        $dataArr = array("code" => '101',
        "message" => $messageErr,
        "method" => $method);
        LogAfterRequest::LogRequest($request, $dataArr);

        $sessionVariable = 'fail';
        $responseInfo = $this->FailedMessage($messageErr);

      }

      $arr = $this->GetTotlExpenses();
      return response()
       ->json([$sessionVariable => $responseInfo,
               'totl_no' => $arr['totl_no'],
               'totl_expenses' => $arr['totl_expenses'] ,
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
        $expense = Expense::find($id);
        return response()->json($expense);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $expense = Expense::find($id);
        return response()->json($expense);
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
       'expense' => 'required',
       'expenditure_amount' => 'required',
       'date_of_expense' => 'required'

     ]);

      $expense = Expense::find($id);

      $expense->expense_type = $expenseType = $request->input('expense');
      $expense->amount = Helper::Numberize($request->input('expenditure_amount'));
      $expense->date_of_expenditure = $request->input('date_of_expense');

      $expense_update_status = $expense->save();

      if($expense_update_status)
      {

       $action = "updated details of expense ".$expenseType."";
       LogsController::logger($request, $action, now());
       $dataArr = array("code" => '200',
       "message" => $action,
       "method" => "ExpensesController@update");
       LogAfterRequest::LogRequest($request, $dataArr);
       return back()->with('success', $this->SuccessMessage($action));
     }
     else
     {
       $messageErr = 'Expense Update failed!';
       $dataArr = array("code" => '101',
       "message" => $messageErr,
       "method" => "ExpensesController@update");
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


     $method = "ExpensesController@destroy";

     $expenseType = Expense::where('id', $id)->value('expense_type');
     $response = Expense::find($id)->delete();

     if($response){

       $action = "deleted expense ".$expenseType." from the system";
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
       $messageErr = 'expense not deleted!';
       $dataArr = array("code" => '101',
       "message" => $messageErr,
       "method" => $method);
       LogAfterRequest::LogRequest($request, $dataArr);

       $sessionVariable = 'fail';
       $responseInfo = $this->FailedMessage($messageErr);

     }

     $arr = $this->GetTotlExpenses();
     return response()
      ->json([$sessionVariable => $responseInfo,
              'totl_no' => $arr['totl_no'],
              'totl_expenses' => $arr['totl_expenses'] ,
      ]);
   }


   protected function GetTotlExpenses()
   {

    $totl_no = Expense::count();
    $totl_amt = Expense::sum('amount');
    $data = array(
           'totl_no' => $totl_no,
           'totl_expenses' => $totl_amt,
    );
    return $data;
 }


   public function deleteAllExpenses(Request $request)
   {

    $result = Expense::truncate();
    if($result){

     $action = "removed all expenses from the system";
     LogsController::logger($request, $action, now());
     $dataArr = array("code" => '200',
     "message" => $action,
     "method" => "ExpensesController@deleteAllExpenses");
     LogAfterRequest::LogRequest($request, $dataArr);
     $sessionVariable = 'success';
     $responseInfo = $this->SuccessMessage($action);
    // return back()->with("success", $this->SuccessMessage($action));
   }
   else
   {
     $messageErr = "Expenses not removed from the system!";
     $dataArr = array("code" => '101',
     "message" => $messageErr,
     "method" => "ExpensesController@deleteAllExpenses");
     LogAfterRequest::LogRequest($request, $dataArr);
     $sessionVariable = 'fail';
     $responseInfo = $messageErr;
    // return back()->with('fail', $messageErr);
   }

   $arr = $this->GetTotlExpenses();
   return response()
    ->json([$sessionVariable => $responseInfo,
            'totl_no' => $arr['totl_no'],
            'totl_expenses' => $arr['totl_expenses'] ,
    ]);

 }


 public function RemoveSelected(Request $request)
    {
        try {
            $ids =  $request->input('selected_rows');
            $deletedExpenses = array();

            if (count($ids) > 0) {
                foreach ($ids as $id) {
                    $findId = Expense::find($id);
                    $findId->delete();
                    array_push($deletedExpenses, $findId->expense_type);
                }
            }
            $sessionVariable = 'success';
            $deletedExpensesStr = implode(", ", $deletedExpenses);
            $action = "removed expenses ".$deletedExpensesStr." from the system";
            if (count($ids) == 1) {
                $action = Str::replaceFirst('expenses', 'expense', $action);
            }
            $response = $this->SuccessMessage($action);

            $dataArr = array("code" => '200',
                "message" => $action,
                "method" => "".$this->controller."@RemoveSelected"
            );
            LogsController::logger($request, $action, now());
            LogAfterRequest::LogRequest($request, $dataArr);

            $arr = $this->GetTotlExpenses();
            return response()
              ->json([$sessionVariable => $response,
                      'totl_no' => $arr['totl_no'],
                      'totl_expenses' => $arr['totl_expenses'] ,
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

 public function importExpenses(Request $request)
 {

  $this->validate($request,
   ['select_file' => 'required|mimes:xls,xlsx'],
   ['select_file.mimes' => 'Please select only excel files to import expenses data']
 );
 
  $importSuccess = Excel::import(new ImportExpenses, request()->file('select_file'));
  if($importSuccess)
  {

    $action = "imported an excel file of expenses into the system";
    LogsController::logger($request, $action, now());
    $dataArr = array("code" => '200',
    "message" => $action,
    "method" => "ExpensesController@importExpenses");
    LogAfterRequest::LogRequest($request, $dataArr);

    return back()->with('success', $this->SuccessMessage($action));
  }
  else
  {
    $messageErr = 'Expenses data not imported!';
    $dataArr = array("code" => '101',
    "message" => $messageErr,
    "method" => "ExpensesController@importExpenses");
    LogAfterRequest::LogRequest($request, $dataArr);
    return back()->with('fail', $messageErr);
  }


}


      /**
         * @return \Illuminate\Support\Collection
         */
      public function exportExpenses()
      {
       return Excel::download(new ExportExpenses, 'expenses.xlsx');
     }

     protected function SuccessMessage($action){
      $message = "You have successfully ".$action."";
       return $message;
     }

protected function FailedMessage($failmsg)
{
  return $failmsg;
}




   }
