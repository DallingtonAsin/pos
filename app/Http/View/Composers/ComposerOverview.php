<?php


namespace App\Http\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use App\Models\Stock;
use App\Models\Sale;
use App\Models\Damage;
use App\Models\Supplier;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\Role;
use App\Models\DebtorsSupplier;
use App\Models\Company;
use App\User;


class ComposerOverview
{

  public function compose(View $view)
  {

    $items_in_stock = Stock::count();
    $total_sales = Sale::count();
    $total_damages = Damage::count();
    $total_suppliers = Supplier::count();
    $total_customers = Customer::count();
    $total_expenses = Expense::count();
    $top_cashiers = Customer::paginate(5);
    $debtorsCustomers = Customer::paginate(4);
    $total_customersDebts = 1; // DebtorsCustomer::sum('debts');
    $total_suppliersDebts = DebtorsSupplier::sum('amount');
    $totlSystemUsers = User::count();
    $totlActiveUsers = User::where('isActive', true)->count();
    $totlLockedUsers = User::where('isActive', false)->count();
    $fiveSuperAdmin = User::limit(5)->get();
    $topItems = $this->getTopItemsByQuantity();
    $salesByCashier = $this->getSalesByCashierReport();




    $company = Company::where('id', '!=', null)->first();
    if (empty($company)) {

      $company = new Company();
      $company->name = env('COMPANY_NAME', 'Point of Sale');
      $company->abbrev = env('COMPANY_ABBREV', 'POS');
      $company->email = env('COMPANY_EMAIL', 'info@pivosoft.com');
      $company->phone_number = env('COMPANY_PHONE_NUMBER', '+256 700477421');
      $company->address = env('COMPANY_ADDRESS', 'Ntinda, Kampala');
      $company->logo = "";
      $company->is_registered = false;
    }

    $view->with('company', $company);

    $data = array(
      'num_of_stockItems' => $items_in_stock,
      'total_sales' => $total_sales,
      'total_damages' => $total_damages,
      'total_suppliers' => $total_suppliers,
      'total_customers' => $total_customers,
      'total_expenses' => $total_expenses,
      'top_cashiers' => $top_cashiers,
      'debtorsCustomers' => $debtorsCustomers,
      'totalCustomerDebts' => $total_customersDebts,
      'totalSupplierDebts' => $total_suppliersDebts,
      'totlSystemUsers' => $totlSystemUsers,
      'totlActiveUsers' => $totlActiveUsers,
      'totlLockedUsers' => $totlLockedUsers,
      'topItemsByQty' =>  $topItems,
      'topItemsByRevenue' => $this->getTopItemsByRevenue(),
      'topItemsByProfit' => $this->getTopItemsByProfit(),
      'totlSuperAdmin' => $this->getNumberofSuperAdmin(),
      'salesByCashier' => $salesByCashier,
      'superAdminArr' => $fiveSuperAdmin,
    );

    $response = Gate::inspect('isSuperAdmin');
    if ($response->allowed()) {
      $view->with('registeredRoles', $this->getRoles());
    }


    if (Auth::check()) {
      $view->with('user_role', $this->getUserRole());
      $view->with('data', $data);
    } {
      return redirect('/home');
    }
  }

  private function getTopItemsByQuantity()
  {
    try {

      $top_items_data = Sale::select('item',  DB::raw('SUM(quantity) as total_quantity'))
        ->groupBy('item')
        ->orderBy('total_quantity', 'desc')
        ->take(10)
        ->get();

      $top_items = $quantity = array();
      foreach ($top_items_data as $item) {
        array_push($top_items, $item->item);
        array_push($quantity, $item->total_quantity);
      }

      $topItems = [
        'items' => $top_items,
        'quantity' => $quantity
      ];
      return $topItems;
    } catch (\Exception $ex) {
      throw $ex;
    }
  }

  private function getTopItemsByRevenue()
  {
    try {

      $top_items_data = Sale::select('item',  DB::raw('SUM(amount) as total_amount'))
        ->groupBy('item')
        ->orderBy('total_amount', 'desc')
        ->take(10)
        ->get();

      $topItems = $row = array();
      foreach ($top_items_data as $item) {
        $row['name'] = $item->item;
        $row['value'] = $item->total_amount;
        array_push($topItems, $row);
      }

      return $topItems;
    } catch (\Exception $ex) {
      throw $ex;
    }
  }

  private function getTopItemsByProfit()
  {

    try {

      $top_items_data = Sale::select('item', DB::raw('SUM((selling_price - original_price) * quantity) as total_profit'))
        ->groupBy('item')
        ->orderBy('total_profit', 'asc')
        ->take(10)
        ->get();

      $top_items = $profit = array();
      foreach ($top_items_data as $item) {
        array_push($top_items, $item->item);
        array_push($profit, $item->total_profit);
      }

      $topItems = [
        'items' => $top_items,
        'profit' => $profit
      ];
      return $topItems;
    } catch (\Exception $ex) {
      throw $ex;
    }
  }

  private function getSalesByCashierReport()
  {
    try {

      $salesByCashier = Sale::join('users', 'sales.cashier_id', '=', 'users.id')
        ->select(DB::raw('users.first_name as cashier, sum(sales.amount) as total_sales'))
        ->groupBy('sales.cashier_id')
        ->orderByDesc('total_sales')
        ->get();

      $data = $row = array();
      foreach ($salesByCashier as $item) {
        $row['name'] = $item->cashier;
        $row['value'] = $item->total_sales;
        array_push($data, $row);
      }

      return $data;

    } catch (\Exception $ex) {
      throw $ex;
    }
  }

  private function getRoles()
  {
    $roles = Role::get();
    return $roles;
  }


  private function getUserRole()
  {
    $userRole = Role::where('id', Auth::user()->role_id)->value('name');
    return $userRole;
  }

  private function getRoleId($role)
  {
    $role_id = Role::where("name", $role)->value("id");
    return $role_id;
  }

  private function getNumberofSuperAdmin()
  {
    $role = "SuperAdministrator";
    $userRoleId = $this->getRoleId($role);
    $totl = User::where("role_id", $userRoleId)->count();
    return $totl;
  }
}
