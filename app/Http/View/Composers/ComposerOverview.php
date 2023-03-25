<?php


namespace App\Http\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Models\Stock;
use App\Models\Sale;
use App\Models\Damage;
use App\Models\Supplier;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\Role;
use App\Models\TopCashier;
use App\Models\DebtorsCustomer;
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
    $total_suppliersDebts = DebtorsSupplier::sum('debts');
    $totlSystemUsers = User::count();
    $totlActiveUsers = User::where('isActive', true)->count();
    $totlLockedUsers = User::where('isActive', false)->count();
    $fiveSuperAdmin = User::limit(5)->get();


    $company = Company::where('id', '!=', null)->first();
    if (empty($company)) {

      $company = new Company();
      $company->name = env('COMPANY_NAME', 'Point of Sale');
      $company->abbrev = env('COMPANY_ABBREV', 'POS');
      $company->email = env('COMPANY_EMAIL', 'info@pivosoftltd.com');
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
      'totlSuperAdmin' => $this->getNumberofSuperAdmin(),
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



  public function getRoles()
  {
    $roles = Role::get();
    return $roles;
  }


  public function getUserRole()
  {
    $userRole = Role::where('id', Auth::user()->role_id)->value('name');
    return $userRole;
  }

  public function getRoleId($role)
  {
    $role_id = Role::where("name", $role)->value("id");
    return $role_id;
  }

  public function getNumberofSuperAdmin()
  {
    $role = "SuperAdministrator";
    $userRoleId = $this->getRoleId($role);
    $totl = User::where("role_id", $userRoleId)->count();
    return $totl;
  }
}
