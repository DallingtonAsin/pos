<?php

namespace App\Helpers;

use Illuminate\Http\Request;
use App\Models\ErrorLog;
use App\Models\Stock;
use App\Models\Purchase;
use App\Models\Role;
use App\Models\Sale;
use App\Models\Expense;
use App\Models\Damage;
use App\Models\CustomerDebtPayment;
use App\Models\Supplier;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\LogsController;
use Illuminate\Support\Facades\Auth;
use App\Jobs\ProcessSendSms;
use App\User;
use  App\Helpers\Constants as Constant;

class Helper
{

  public static function logError($data)
  {

    try {
      $username = $data['username'];
      $error_code = $data['error_code'];
      $error_message = $data['error_message'];
      $error_severity = $data['error_severity'];
      $controller = $data['controller'];
      $method = $data['method'];

      $error = new ErrorLog();
      $error->username = $username;
      $error->error_code = $error_code;
      $error->error_message = $error_message;
      $error->error_severity = $error_severity;
      $error->controller = $controller;
      $error->method = $method;

      $resp = $error->save();
    } catch (\Exception $ex) {
    }
  }

  public static function GetItemName($stockId)
  {
    try {

      $item = Stock::where('id', $stockId)->value('item');
      return $item;
    } catch (\Exception $ex) {
      dd($ex->getMessage());
    }
  }

  public static function Numberize($input)
  {
    try {
      $result = floatval(preg_replace('/[^\d.]/', '', $input));
      return $result;
    } catch (\Exception $ex) {
      dd($ex->getMessage());
    }
  }

  public static function getRoleId($role)
  {
    try {
      $roleId = Role::where('name', 'like', '%' . $role . '%')->value('id');
      return $roleId;
    } catch (\Exception $ex) {
      dd($ex->getMessage());
    }
  }

  public static function getRole($roleId)
  {
    try {
      $role = Role::where('id', $roleId)->value('name');
      return $role;
    } catch (\Exception $ex) {
      dd($ex->getMessage());
    }
  }

  public static function getRoles()
  {
    try {
      $roles = Role::get();
      return $roles;
    } catch (\Exception $ex) {
      dd($ex->getMessage());
    }
  }


  public static function SendTextMessage(Request $request, $to, $text_message)
  {
    $from = config('app.name');
    try {
      $arr = array(
        'from' => $from,
        'to' => $to,
        'message' => $text_message,
        'created_at' => now(),
      );
      //put a method to log this request before API call
      Helper::EnqueueSms($arr); // $this->SendTextMessage($from, $to, $text_message);
      //	Log::info('app.requests', ['request' => $request->all(), 'response' => $response]);

      // if($res){
      $action = "sent a text message to " . $to . "";
      //Log this transactional request
      LogsController::logger($request, $action, now());

      return 1;
      // }
      // else{
      //     return back()->with("fail", "Sorry, message has not been sent!");

      // }
    } catch (\Exception $ex) {
      echo ('Problems thhh');
      return back()->with("fail", "Sorry, message has not been sent!");
    }
  }

  protected static function EnqueueSms($data)
  {
    dispatch(new ProcessSendSms($data))->onQueue('sms');
  }


  public static function insertPurchaseAndUpdateStock($row)
  {

    try {
      //  dd($row);
      if (Helper::array_key_isset('selling_price', $row)) {
        $sellingPrice = Helper::Numberize($row['selling_price']);
      } else {
        $sellingPrice = Helper::array_key_isset('retail_price', $row) ? Helper::Numberize($row['retail_price']) : null;
      }

      if (Helper::array_key_isset('quantity', $row)) {
        $quantity = Helper::Numberize($row['quantity']);
      } else {
        $quantity = Helper::array_key_isset('qty', $row) ? Helper::Numberize($row['qty']) : null;
      }

      $purchase = new Purchase();
      $purchase->serial_no =  Helper::array_key_isset('sno', $row) ? $row['sno'] : null;
      $purchase->receipt_no = Helper::array_key_isset('receipt_no', $row) ? $row['receipt_no'] : null;
      $purchase->item_code = $row['item_code'];
      $purchase->item = $row['item'];
      $purchase->quantity = $quantity;
      $purchase->cost_price_per_item = Helper::Numberize($row['buying_price']);
      $purchase->retail_price = $sellingPrice;
      $purchase->wholesale_price = Helper::array_key_isset('wholesale_price', $row) ? Helper::Numberize($row['wholesale_price']) : null;
      $purchase->supplier =  Helper::array_key_isset('supplier', $row) ? $row['supplier'] : null;
      $purchase->supplier_contact = Helper::array_key_isset('suppliers_contact', $row) ? $row['suppliers_contact'] : null;
      $purchase->recorded_by = Auth::user()->name;
      $purchase->date_of_purchase = Helper::array_key_isset('date_of_purchase', $row) ? $row['date_of_purchase'] : date('Y-m-d');

      $isSaved = $purchase->save();
      if ($isSaved) {
        Helper::insertOrUpdateStock($row);
      } else {
        dd("What is not right?");
      }
      return true;
    } catch (\Exception $ex) {
      throw $ex;
    }
  }

  public static function insertOrUpdateStock($row)
  {

    try {

      $item_code = $row['item_code'];
      $category = Helper::array_key_isset('category', $row) ? $row['category'] : null;
      $supplier = Helper::array_key_isset('supplier', $row) ? $row['supplier'] : null;
      $thresholdQty = Helper::array_key_isset('thresholdQty', $row) ? Helper::Numberize($row['thresholdQty']) : null;
      $expiryDate = Helper::array_key_isset('expiry_date', $row) ? $row['expiry_date'] : null;
      $wholeSalePrice = Helper::array_key_isset('wholesale_price', $row) ? Helper::Numberize($row['wholesale_price']) : null;

      if (Helper::array_key_isset('selling_price', $row)) {
        $sellingPrice = Helper::Numberize($row['selling_price']);
      } else {
        $sellingPrice = Helper::array_key_isset('retail_price', $row) ? Helper::Numberize($row['retail_price']) : null;
      }

      if (Helper::array_key_isset('quantity', $row)) {
        $quantity = Helper::Numberize($row['quantity']);
      } else {
        $quantity = Helper::array_key_isset('qty', $row) ? Helper::Numberize($row['qty']) : null;
      }

      $findStock = Stock::where('item_code', $item_code);
      if (isset($item_code) && $findStock->exists()) {

        $newQuantity = Helper::Numberize($findStock->value('quantity')) + $quantity;
        $stock = [
          'item_code' => $item_code,
          'item' => $row['item'],
          'category' => $category,
          'supplier' => $supplier,
          'quantity' => $newQuantity,
          'threshold_qty' => $thresholdQty,
          'expiry_date' => $expiryDate,
          'buying_price' => $row['buying_price'],
          'selling_price' => $sellingPrice,
          'wholesale_price' => $wholeSalePrice
        ];

        Stock::where('item_code', $item_code)
          ->update($stock);
      } else {

        $stock = new Stock();

        $stock->item_code = $item_code;
        $stock->item = $row['item'];
        $stock->category = $category;
        $stock->supplier =  $supplier;
        $stock->quantity = $quantity;
        $stock->threshold_qty = $thresholdQty;
        $stock->expiry_date = $expiryDate;
        $stock->buying_price = $row['buying_price'];
        $stock->selling_price = $sellingPrice;
        $stock->wholesale_price = $wholeSalePrice;
        $stock->save();
      }
      return true;
    } catch (\Exception $ex) {
      throw $ex;
    }
  }

  private static function array_key_isset($k, $a)
  {
    return isset($a[$k]); // || array_key_exists($k, $a);
  }


  public static function createStock($row)
  {
    try {

      $insertStock =  Stock::create([
        'item_code' => $row['item_code'],
        'item' => $row['item'],
        'quantity' => floatval(Helper::Numberize($row['qty'])),
        'buying_price' => floatval(Helper::Numberize($row['price_per_item'])),
        'selling_price' => floatval(Helper::Numberize($row['retail_price'])),
        'wholesale_price' => floatval(Helper::Numberize($row['wholesale_price'])),
        'supplier' => $row['supplier']
      ]);

      return $insertStock;
    } catch (\Exception $ex) {
      throw $ex;
    }
  }


  public static function getStock()
  {
    $items = Stock::all();
    $itemsArr = array();
    foreach ($items as $item) {
      array_push($itemsArr, $item->item);
    }
    return $itemsArr;
  }

  public static function getItemQty($item)
  {
    $qty = Stock::where('item', $item)
      ->value('quantity');
    return $qty;
  }

  public static function isItemInStock($item)
  {
    try {

      $stock = Helper::getStock();
      return in_array($item, $stock);
    } catch (\Exception $ex) {
      throw $ex;
    }
  }


  public static function getUserRoleId($role)
  {
    try {
      $roleId = Role::where('name', 'like', '%' . $role . '%')
        ->value('id');
      return $roleId;
    } catch (\Exception $ex) {
      $data = array(
        'username' => auth()->user()->username,
        'error_code' => $ex->getCode(),
        'error_message' => $ex->getMessage(),
        'error_severity' => Constant::$STATUS_ERROR_SEVERITY,
        'controller' => 'Helper',
        'method' => 'getRole'
      );
      Helper::logError($data);
      abort(409, $ex->getMessage());
    }
  }


  public static function GetUserStats($role)
  {
    try {
      $role_id = Helper::getUserRoleId($role);
      $users_list = User::where('role_id', $role_id)->get();
      $number_of_users = User::where('role_id', $role_id)->count();
      $data = array(
        'totl' => $number_of_users,
        'list' => $users_list
      );

      return $data;
    } catch (\Exception $ex) {
      $data = array(
        'username' => auth()->user()->username,
        'error_code' => $ex->getCode(),
        'error_message' => $ex->getMessage(),
        'error_severity' => Constant::$STATUS_ERROR_SEVERITY,
        'controller' => 'Helper',
        'method' => 'GetUserStats'
      );
      Helper::logError($data);
      abort(409, $ex->getMessage());
    }
  }


  public static function getProfitsForAGivenMonth($year, $month)
  {
    try {

      $totalSales = Sale::whereYear('date', $year)
        ->whereMonth('date', $month)
        ->sum('paid_amount');

      $totalBuyingCost = Sale::whereYear('date', $year)
        ->whereMonth('date', $month)
        ->sum('total_buying_cost');


      $totalExpenses = Expense::whereYear('date_of_expenditure', $year)
        ->whereMonth('date_of_expenditure', $month)
        ->sum('amount');

      $totalDamages = Damage::whereYear('recordedOn', $year)
        ->whereMonth('recordedOn', $month)
        ->sum('total_cost');

      $supplierDebts = (Supplier::whereYear('created_at', $year)
        ->whereMonth('created_at', $month)
        ->sum('credit')) -  (Supplier::whereYear('created_at', $year)
        ->whereMonth('created_at', $month)
        ->sum('debt'));

      $customerDebts = Sale::where('fully_paid', 0)
        ->where('balance', '>', 0)
        ->whereYear('date', $year)
        ->whereMonth('date', $month)->sum('balance');

      $netProfitPerMonth = (($totalSales - $totalBuyingCost) - ($totalExpenses + $totalDamages) + ($supplierDebts + $customerDebts));

      return $netProfitPerMonth;
    } catch (\Exception $ex) {
      throw $ex;
    }
  }

  public static function getMonthlySalesData()
  {
    $year = date('Y');
    $result = DB::table('monthlysales')
      ->where('SalesYear', $year)
      ->orderBy('month_int', 'asc')
      ->get();
    $max_sale_value = DB::table('monthlysales')->max('TotalSales');
    $data = $months = $years = $sales = $profits = array();
    $totalProfits = 0;
    foreach ($result as $row) {
      $profitForEachMonth = Helper::getProfitsForAGivenMonth($row->SalesYear, $row->month_int);
      $totalProfits += $profitForEachMonth;
      array_push($months, date("F", mktime(0, 0, 0, $row->month_int, 10)));
      array_push($profits, $profitForEachMonth);
      array_push($years, $row->SalesYear);
      array_push($sales, $row->TotalSales);
    }
    $data = array(
      'months' => $months, 'years' => $years, 'sales' => $sales, 'profits' => $profits,
      'max' => $max_sale_value
    );
    // dd($totalProfits);
    if (!empty($data)) {
      return $data;
    }
  }


  public static function getMonthlyPurchasesData()
  {
    $year = date('Y');

    $result  = DB::table('monthly_purchases')
      ->where('purchase_year', $year)
      ->orderBy('month_int', 'asc')
      ->get();
    $data = $months = $purchases = array();

    foreach ($result as $row) {

      array_push($months, $row->month_name);
      array_push($purchases, $row->total_purchases);
    }
    $data = array(
      'months' => $months,
      'purchases' => $purchases
    );

    return $data;
  }

  public static function convertNumber($number)
  {
    Helper::is_decimal($number)
      ? $number = number_format($number, 2)
      : $number =  number_format($number);
    return $number;
  }

  public static function is_decimal($n)
  {
    return is_numeric($n) && floor($n) != $n;
  }

  public static function getUser($user_id)
  {
    return User::find($user_id);
  }

  public static function getCustomer($customer_id)
  {
    return Customer::find($customer_id);
  }

  public static function isCashier()
  {

    $cashier_role_id = Role::where('name', 'like', '%cashier%')->first()->id;
    return Auth::user()->role_id === $cashier_role_id;
  }

  public static function customerDebt($customer_id)
  {
    try {
      $debt = Sale::where('customer_id', $customer_id)->sum('amount')
        - Sale::where('customer_id', $customer_id)->sum('paid_amount')
        - CustomerDebtPayment::where('customer_id', $customer_id)->sum('paid_amount');
      return $debt;
    } catch (\Exception $ex) {
      throw $ex;
    }
  }

  public static function totalCustomerDebt($customer_id)
  {
    try {
       return  Sale::where('customer_id', $customer_id)->sum('amount')
       - Sale::where('customer_id', $customer_id)->sum('paid_amount');
    } catch (\Exception $ex) {
      throw $ex;
    }
  }
  
  public static function totalCustomerPayments($customer_id)
  {
    try {
       return CustomerDebtPayment::where('customer_id', $customer_id)->sum('paid_amount');
    } catch (\Exception $ex) {
      throw $ex;
    }
  }

  public static function getTotalCustomerDebt()
  {
    try {
      $total_debt = Sale::sum('amount')
        - Sale::sum('paid_amount')
        - CustomerDebtPayment::sum('paid_amount');
      return $total_debt;
    } catch (\Exception $ex) {
      throw $ex;
    }
  }

  public static function getDamageCost(){
    $damaged_items = Damage::all();
    $total_damage_cost = 0;
    
    foreach ($damaged_items as $damaged_item) {
        $stock = Stock::findOrFail($damaged_item->item_id);
        $damage_cost = $stock->buying_price * $damaged_item->quantity;
        $total_damage_cost += $damage_cost;
    }
    
    return $total_damage_cost;
  }


}
