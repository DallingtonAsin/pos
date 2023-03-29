<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
	return view('auth/login');
})->name('re-login');

Auth::routes();
Route::post('/user/validate', 'Auth\CustomLoginController@authenticate')->name('authenticate');
Route::get('/sign-out', 'Auth\CustomLoginController@logout')->name('signout');

Route::group(["middleware" => "OTPlayer"], function () {
	Route::post('login/verifyOTP', 'Auth\CustomLoginController@VerifyUserOTPRequest')->name('verifyOTP');
	Route::get('login/OTP', 'Auth\CustomLoginController@getOTPPage');
});


Route::get("/export/excel", "SalesController@GetSalesExcelFileReport");
Route::get('/fetch/company-details', 'SettingsController@GetCompanies')->name('companies.home');
Route::get('stock/fetch', 'StockController@fetchStockItemsAjax')->name('stock.ajax.fetch');
Route::get('roles/fetch', 'UserController@fetchRolesAjax')->name('roles.ajax.fetch');


Route::get('reports/ajax/monthly-sales', 'ReportsController@GetMonthlySalesDT')->name('monthly-sales.ajax');
Route::get('reports/ajax/low-running-stock/{qty?}', 'ReportsController@GetLowStockDT')->name('low-stock.ajax');
Route::get('reports/ajax/best-selling-items', 'ReportsController@GetBestSellingItemsDT')->name('best-selling-items.ajax');
Route::get('reports/ajax/cashiers-performance', 'ReportsController@GetCashiersReportDT')->name('top-cashiers.ajax');

Route::get("users/active", "UserController@ActiveUsersIndex")->name('user.account.active');
Route::get("users/locked", "UserController@LockedUsersIndex")->name('user.account.locked');
Route::get('users/active/fetch', 'UserController@ActiveUsersAjax')->name('active_user.ajax.fetch');
Route::get('users/locked/fetch', 'UserController@LockedUsersAjax')->name('locked_user.ajax.fetch');


Route::get('reports/ajax/top-customers', 'ReportsController@GetTopCustomersDT')->name('top-customers.ajax');
Route::get('reports/ajax/debtors/customers', 'ReportsController@GetCustomerDebtorsDT')->name('debtors-customers.ajax');

Route::get('/customers/with-debts/ajax', 'CustomerDebtPaymentController@GetCustomersWithDebts')->name('customers.with.debts.ajax');
Route::get('/customers/debt-payments', 'CustomerDebtPaymentController@index')->name('customers.debts.payments.index');
Route::get('/customers/debt-payments/ajax', 'CustomerDebtPaymentController@getCustomerDebtPayments')->name('customers.debts.payments.ajax');
Route::get('/customers/debt/{customer_id}', 'CustomerDebtPaymentController@getCustomerDebt')->name('customer.debt.ajax');
Route::get('/customers/with-debts', 'CustomerDebtPaymentController@customersWithDebtsIndex')->name('customers.with.debts');


Route::get('purchases/store', 'PurchasesController@store')->name('purchases.post');

Route::group(["middleware" => "restricted"], function () {

	Route::get('/customers/with-debts/{id}', 'CustomersController@showCustomerWithDebt');
	Route::post('update/customer/debts', 'CustomersController@updateCustomerDebts')->name('customer.debt.update');


	Route::get('/stock/get-data', 'StockController@GetStock')->name('get-stock');
	Route::get('/suppliers/home', 'SuppliersController@GetSuppliers')->name('suppliers.home');
	Route::get('/expenses/get-data', 'ExpensesController@GetExpenses')->name('get-expenses');
	Route::get('/logs/get-data', 'LogsController@GetLogs')->name('get-logs');
	Route::get('/damages/get-data', 'DamagesController@GetDamages')->name('get-damages');


	Route::get('/sales/get-data', 'SalesController@GetSales')->name('get-sales');
	Route::get('/sales/fetch/today', 'SalesController@getTodaySales')->name('get-daily-sales');
	Route::get('/sales/today', 'SalesController@salesForToday')->name('dailysales.index');
	Route::get('/sales/item/{id}', 'SalesController@GetItem')->name('getItemName');

	Route::get('/sales/debts', 'SalesController@salesWithDebtsIndex')->name('sales.debts');
	Route::get('/sales/debts/ajax', 'SalesController@GetSalesWithDebts')->name('get-sales-with-debts');
	Route::get('/sales/today/debts/ajax', 'SalesController@getTodaySalesWithDebts')->name('get-daily-sales-with-debts');

	Route::get('/events/get', 'EventsController@GetEvents')->name('get-events');
	Route::get('/events/getTitle/{id}', 'EventsController@GetEventTitle')->name('getEventTitle');
	Route::get('purchases/get/', 'PurchasesController@GetPurchases')->name('get-purchases');
	Route::get('StockCats/load/', 'StockCatsController@StockCatAjaxIndex')->name('get-stockItems');


	Route::get('/company/register', 'SettingsController@showCreateCoForm')->name('company.register');
	Route::post('/register/company/{id}', 'SettingsController@addUpdateCompany')->name('company.add_or_update');
	Route::get('/users/managers', 'UserController@fetchManagers')->name('managers.home');
	Route::get('/users/managers/ajax', 'UserController@GetManagers')->name('managers.index.ajax');
	Route::get('/users/cashiers', 'UserController@fetchCashiers')->name('cashiers.home');
	Route::get('/users/cashiers/ajax', 'UserController@GetCashiers')->name('cashiers.index.ajax');
	Route::get('/users/fetch/ajax', 'UserController@GetUsers')->name('users.index.ajax');

	Route::get('stores/fetch/ajax', 'StoreController@getStoresDataTable')->name('stores.index.ajax');
	Route::get('taken-bottles/fetch/ajax', 'TakenBottleController@getTakenBottleDataTable')->name('taken-bottles.index.ajax');


	Route::resources([
		'stock' => 'StockController',
		'taken-bottles' => 'TakenBottleController',
		'pos' => 'CartController',
		'sales' => 'SalesController',
		'product-categories' => 'StockCatsController',
		'cashiers' => 'CashiersController',
		'damaged-stock-items' => 'DamagesController',
		'suppliers' => 'SuppliersController',
		'supplier-credits' => 'SupplierCreditController',
		'supplier-debts' => 'SupplierDebtController',
		'purchases' => 'PurchasesController',
		'events' => 'EventsController',
		'expenses' => 'ExpensesController',
		'profile' => 'ProfileController',
		'mail' => 'MailController',
		'logs' => 'LogsController',
		'users' => 'UserController',
		'stores' => 'StoreController',
		'calendar' => 'CalendarController',
		'company' => 'SettingsController',
		'customer-debt-payments' => 'CustomerDebtPaymentController',
	]);

	Route::get('/email', 'MailController@MailWelcome');
	Route::get('/home', 'HomeController@index')->name('home');
	Route::get('/reports', 'ReportsController@index')->name('reports');
	Route::get('/reports/charts/purchases', 'ReportsController@purchaseReports')->name('reports.charts.purchases');


	Route::post('pos/barcode/getItem', 'CartController@getItemData')->name('item.get');
	Route::post('sale/transact', 'CartController@recordSale')->name('sale.record');
	Route::post('pos/search', 'CartController@searchItem')->name('item.search');
	Route::post('pos/searchprice', 'CartController@getItemPrice')->name('cart.searchprice');


	Route::post('/sales/filtered-sales', 'SalesController@filterSales')->name('filtersales');
	Route::post('/sales/debts/search', 'SalesController@filterSalesWithDebts')->name('sales.debts.filter');
	Route::put('/sales/records/update/', 'SalesController@updateSaleRecord')->name('sales.records.update');
	Route::get('sales/export-sales', 'SalesController@exportSales')->name('sales.export');

	Route::post('stock/import-stock', 'StockController@importStock')->name('stock.import');
	Route::post('purchases/import-purchases', 'PurchasesController@importPurchasedItems')->name('purchases.import');
	Route::get('stock/export-stock', 'StockController@exportStock')->name('stock.export');


	Route::post('/expenses/import-expenses', 'ExpensesController@importExpenses')->name('expenses.import');
	Route::get('/expenses/export-expenses', 'ExpensesController@exportExpenses')->name('expenses.export');


	Route::post('/cashiers/import-cashiers', 'CashiersController@importCashiers')->name('cashiers.import');
	Route::get('/cashiers/export-cashiers', 'CashiersController@exportCashiers')->name('cashiers.export');

	Route::post("/cashiers/remove/selected", "CashiersController@RemoveSelectedCashiers")->name("selected-cashiers.remove");
	Route::post("/suppliers/remove/selected", "SuppliersController@RemoveSelected")->name("selected-suppliers.remove");
	Route::post("/stock/remove/selected", "StockController@RemoveSelected")->name("selected-stock.remove");
	Route::post("/expenses/remove/selected", "ExpensesController@RemoveSelected")->name("selected-expenses.remove");
	Route::post("/damages/remove/selected", "DamagesController@RemoveSelected")->name("selected-damages.remove");

	Route::post("/purchases/remove/selected", "PurchasesController@RemoveSelected")->name("selected-purchases.remove");
	Route::post("/customers/remove/selected", "CustomersController@RemoveSelected")->name("selected-customers.remove");
	Route::post("/stockcat/remove/selected", "StockCatsController@RemoveSelected")->name("selected-stockcats.remove");
	Route::post("/sales/remove/selected", "SalesController@RemoveSelected")->name("selected-sales.remove");


	Route::post("/users/remove/selected", "UserController@RemoveSelected")->name("selected-users.remove");
	Route::post('users/search/role', 'UserController@searchRole')->name('user.searchrole');
	Route::get('/customers/home', 'CustomersController@GetCustomers')->name('customers.home');
	Route::post('suppliers/import-suppliers', 'SuppliersController@importSuppliers')->name('suppliers.import');
	Route::get('suppliers/export-suppliers', 'SuppliersController@exportSuppliers')->name('suppliers.export');
	Route::get('suppliers/getSuppliers4DT', 'SuppliersController@GetSuppliersData')->name('getSuppliers4DT');
	Route::get('suppliers/credits/ajax', 'SupplierCreditController@getSupplierCreditDataTable')->name('suppliers.credits.ajax');
	Route::get('suppliers/debts/ajax', 'SupplierDebtController@getSupplierDebtDataTable')->name('suppliers.debts.ajax');


	Route::post('product-categories/import-categories', 'StockCatsController@importCategories')->name('categories.import');
	Route::get('product-categories/export-categories', 'StockCatsController@exportCategories')->name('categories.export');

	Route::post('damaged-stock-items/import-damages', 'DamagesController@importDamages')->name('damages.import');
	Route::get('damaged-stock-items/export-damages', 'DamagesController@exportDamages')->name('damages.export');
	Route::post('stock/search/item', 'DamagesController@searchItem')->name('stock-item.search');

	Route::get('get-chartdata', 'ReportsController@getMonthlySalesData')->name('chartdata');
	Route::resource('customers', 'CustomersController');
	Route::get('account-settings', 'ProfileController@accountSettings')->name('account-settings');

	Route::get('reports/low-running-stock/{qty?}', 'ReportsController@lowRunningStock')->name('low-stock');
	Route::get('reports/monthly-sales', 'ReportsController@MonthlySales')->name('m-sales');
	Route::get('reports/best-selling-items', 'ReportsController@BestSellingItems')->name('best-selling-items');
	Route::get('reports/top-customers', 'ReportsController@topCustomers')->name('top-customers');
	Route::get('reports/cashiers-performance', 'ReportsController@topCashiers')->name('top-cashiers');
	Route::get('reports/debtors/customers', 'ReportsController@debtorsCustomersList')->name('debtors-customers');

	Route::post('notifications/get', 'NotificationController@GetOtherNotifications')->name('unreadNotifications');
	Route::post('notification/unreadEmailNotifications', 'NotificationController@GetUnReadEmailNotifications')->name('unreadEmailNotifications');
	Route::get('notifications', 'NotificationController@markAllRead')->name('readAll');


	Route::get('user-guide', 'DocumentationController@index')->name('userguide');
	Route::get('about-CodeSolutionTech', 'DocumentationController@CompanyDetails')->name('aboutCST');

	Route::get('sms', 'SmsController@index')->name('sms');
	Route::post('send-sms', 'SmsController@SendSMS')->name('sms.store');
	Route::get('events/event-form', 'EventsController@ShowEventForm')->name('events.showForm');

	Route::post('purchases/deleteAll', 'PurchasesController@deleteAllPurchases')->name('purchases.truncate');
	Route::post('stock/deleteAll', 'StockController@deleteAllStockItems')->name('stock.truncate');
	Route::post('suppliers/deleteAll', 'SuppliersController@deleteAllSuppliers')->name('suppliers.truncate');
	Route::post('expenses/deleteAll', 'ExpensesController@deleteAllExpenses')->name('expenses.truncate');
	Route::post('/cashiers/deleteAll', 'CashiersController@deleteAllCashiers')->name('cashiers.truncate');
	Route::post('/customers/deleteAll', 'CustomersController@deleteAllCustomers')->name('customers.truncate');
	Route::post('product-categories/deleteAll', 'StockCatsController@deleteAllStockCategories')->name('categories.truncate');
	Route::post('damaged-stock-items/deleteAll', 'DamagesController@deleteAllDamages')->name('damages.truncate');
	Route::post('logs/truncate', 'LogsController@truncateLogs')->name('logs.truncate');

	Route::get('stock/find/{id}', 'StockController@findStockItem')->name('stock.item.find');
	Route::get('users/active/remove', 'UserController@RemoveAllActiveUsers')->name('active-users.remove')->middleware('password.confirm');
	Route::get('users/locked/remove', 'UserController@RemoveAllLockedUsers')->name('locked-users.remove')->middleware('password.confirm');
	Route::get('/user/lockunlock/{id}/{status}/{name}', 'UserController@LockUnlockAccount')->name('user.lockunlock');
	Route::post('/user/lockunlock/', 'UserController@LockUnlockUserAccount')->name('account.change');
	Route::get('/cashier/change-account/{id}/{status}/{name}', 'CashiersController@ChangeAccountStatus')->name('cashiers.changestatus');
});
