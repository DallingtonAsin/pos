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

//Auth::routes(['register' => false]);
// Auth::route(['login'])->middleware(CheckStatus::class)

Route::post('/user/validate', 'Auth\CustomLoginController@authenticate')->name('authenticate');
Route::get('/sign-out', 'Auth\CustomLoginController@logout')->name('signout');

Route::group(["middleware" => "OTPlayer"], function () {
	Route::post('login/verifyOTP', 'Auth\CustomLoginController@VerifyUserOTPRequest')->name('verifyOTP');
	Route::get('login/OTP', 'Auth\CustomLoginController@getOTPPage');
});



Route::get("/export/excel", "SalesController@GetSalesExcelFileReport");

Route::get("/sale/make-receipt", "CartController@getReceipt");


// Route::get("/users/active", "UserController@ActiveUsersIndex")->name('user-account.active');
// Route::get("/users/locked", "UserController@LockedUsersIndex")->name('user-account.locked');
Route::get('/fetch/company-details', 'SettingsController@GetCompanies')->name('companies.home');
Route::get('stock/fetch', 'StockController@fetchStockItemsAjax')->name('stock.ajax.fetch');
Route::get('roles/fetch', 'UserController@fetchRolesAjax')->name('roles.ajax.fetch');


Route::get('reports/ajax/monthly-sales', 'ReportsController@GetMonthlySalesDT')->name('monthly-sales.ajax');
Route::get('reports/ajax/low-running-stock/{qty?}', 'ReportsController@GetLowStockDT')->name('low-stock.ajax');
Route::get('reports/ajax/best-selling-items', 'ReportsController@GetBestSellingItemsDT')->name('best-selling-items.ajax');
Route::get('reports/ajax/cashiers-performance', 'ReportsController@GetCashiersReportDT')->name('top-cashiers.ajax');
Route::get('reports/ajax/debtors/suppliers', 'ReportsController@GetSupplierDebtorsDT')->name('debtors-suppliers.ajax');


Route::get("users/active", "UserController@ActiveUsersIndex")->name('user.account.active');
Route::get("users/locked", "UserController@LockedUsersIndex")->name('user.account.locked');
Route::get('users/active/fetch', 'UserController@ActiveUsersAjax')->name('active_user.ajax.fetch');
Route::get('users/locked/fetch', 'UserController@LockedUsersAjax')->name('locked_user.ajax.fetch');


Route::get('reports/ajax/top-customers', 'ReportsController@GetTopCustomersDT')->name('top-customers.ajax');
Route::get('reports/ajax/debtors/customers', 'ReportsController@GetCustomerDebtorsDT')->name('debtors-customers.ajax');
Route::get('/customers/with-debts/ajax', 'CustomersController@GetCustomersWithDebts')->name('customers.with.debts.ajax');

Route::get('/customers/debt-payments', 'CustomersController@customerDebtPaymentsIndex')->name('customers.debts.payments.index');
Route::get('/customers/debt-payments/ajax', 'CustomersController@GetCustomerDebtPayments')->name('customers.debts.payments.ajax');
Route::get('purchases/store', 'PurchasesController@store')->name('purchases.post');



Route::match(['get', 'post'], '/botman', 'ChatBotController@handle');

Route::group(["middleware" => "restricted"], function () {

	Route::get('/customers/with-debts/{id}', 'CustomersController@showCustomerWithDebt');
	Route::post('update/customer/debts', 'CustomersController@updateCustomerDebts')->name('customer.debt.update');


	Route::get('/stock/get-data', 'StockController@GetStock')->name('get-stock');
	Route::get('/suppliers/home', 'SuppliersController@GetSuppliers')->name('suppliers.home');
	Route::get('/expenses/get-data', 'ExpensesController@GetExpenses')->name('get-expenses');
	Route::get('/logs/get-data', 'LogsController@GetLogs')->name('get-logs');
	Route::get('/damages/get-data', 'DamagesController@GetDamages')->name('get-damages');


	Route::get('/sales/get-data', 'SalesController@GetSales')->name('get-sales');
	Route::get('/sales/fetch/today', 'SalesController@GetTodaySales')->name('get-daily-sales');
	Route::get('/sales/today', 'SalesController@salesForToday')->name('dailysales.index');
	Route::get('/sales/item/{id}', 'SalesController@GetItem')->name('getItemName');

	Route::get('/sales/debts', 'SalesController@salesWithDebtsIndex')->name('sales.debts');
	Route::get('/sales/debts/ajax', 'SalesController@GetSalesWithDebts')->name('get-sales-with-debts');
	Route::get('/sales/today/debts/ajax', 'SalesController@GetTodaySalesWithDebts')->name('get-daily-sales-with-debts');


	Route::get('/events/get', 'EventsController@GetEvents')->name('get-events');
	Route::get('/events/getTitle/{id}', 'EventsController@GetEventTitle')->name('getEventTitle');
	Route::get('purchases/get/', 'PurchasesController@GetPurchases')->name('get-purchases');
	Route::get('StockCats/load/', 'StockCatsController@StockCatAjaxIndex')->name('get-stockItems');



	Route::get('/company/register', 'SettingsController@showCreateCoForm')->name('company.register');
	Route::post('/register/company/{id}', 'SettingsController@addUpdateCompany')->name('companies.register');
	Route::get('/users/managers', 'UserController@fetchManagers')->name('managers.home');
	Route::get('/users/managers/ajax', 'UserController@GetManagers')->name('managers.index.ajax');
	Route::get('/users/cashiers', 'UserController@fetchCashiers')->name('cashiers.home');
	Route::get('/users/cashiers/ajax', 'UserController@GetCashiers')->name('cashiers.index.ajax');
	Route::get('/users/fetch/ajax', 'UserController@GetUsers')->name('users.index.ajax');

	Route::resources([
		'stock' => 'StockController',
		'pos' => 'CartController',
		'sales' => 'SalesController',
		'product-categories' => 'StockCatsController',
		'cashiers' => 'CashiersController',
		'damaged-stock-items' => 'DamagesController',
		'suppliers' => 'SuppliersController',
		'purchases' => 'PurchasesController',
		'events' => 'EventsController',
		'expenses' => 'ExpensesController',
		'profile' => 'ProfileController',
		'mail' => 'MailController',
		'logs' => 'LogsController',
		'users' => 'UserController',
		'calendar' => 'CalendarController',
		'command' => 'ChatBotController',
		'company' => 'SettingsController',
	]);

	Route::get('/email', 'MailController@MailWelcome');
	Route::get('/home', 'HomeController@index')->name('home');
	Route::get('/overview', 'HomeController@overview')->name('overview');
	Route::get('/reports', 'ReportsController@index')->name('reports');
	Route::get('/reports/charts/purchases', 'ReportsController@purchaseReports')->name('reports.charts.purchases');


	Route::post('pos/session/update', 'CartController@updateItemInSession')->name('session.update');
	Route::post('pos/record', 'CartController@MakeSaleGateway')->name('sale.transact');
	Route::post('sale/transact', 'CartController@recordSale')->name('sale.record');

	Route::post('pos/barcode/getItem', 'CartController@GetCartData')->name('item.get');
	Route::post('pos/search', 'CartController@searchItem')->name('item.search');
	Route::post('pos/searchprice', 'CartController@getItemPrice')->name('cart.searchprice');
	Route::post('users/search/role', 'UserController@searchRole')->name('user.searchrole');

	Route::post('/sales/filtered-sales', 'SalesController@filterSales')->name('filtersales');
	Route::post('/sales/debts/search', 'SalesController@filterSalesWithDebts')->name('sales.debts.filter');



	Route::put('/sales/records/update/', 'SalesController@updateSaleRecord')
		->name('sales.records.update');
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


	Route::get('/customers/home', 'CustomersController@GetCustomers')->name('customers.home');
	Route::post('/customers/import-customers', 'CustomersController@importCustomers')->name('customers.import');
	Route::get('/customers/export-customers', 'CustomersController@exportCustomers')->name('customers.export');
	Route::get('/customers/with-debts', 'CustomersController@customersWithDebtsIndex')->name('customers.with.debts');


	Route::post('suppliers/import-suppliers', 'SuppliersController@importSuppliers')->name('suppliers.import');
	Route::get('suppliers/export-suppliers', 'SuppliersController@exportSuppliers')->name('suppliers.export');
	Route::get('suppliers/getSuppliers4DT', 'SuppliersController@GetSuppliersData')->name('getSuppliers4DT');


	Route::post('product-categories/import-categories', 'StockCatsController@importCategories')->name('categories.import');
	Route::get('product-categories/export-categories', 'StockCatsController@exportCategories')->name('categories.export');

	Route::post('damaged-stock-items/import-damages', 'DamagesController@importDamages')->name('damages.import');
	Route::get('damaged-stock-items/export-damages', 'DamagesController@exportDamages')->name('damages.export');
	Route::post('stock/search/item', 'DamagesController@searchItem')->name('stock-item.search');

	Route::get('payments', 'PaymentsController@index')->name('payments');
	Route::get('payments/paypal', 'PaymentsController@paypalIndex')->name('paypal');
	Route::post('payments/paypal/post', 'PaymentsController@PayPalPayment')->name('paypal-payment-form-submit');
	Route::get('payment/paypay/cancel', 'PaymentsController@cancel')->name('payment.cancel');
	Route::get('payment/paypal/success', 'PaymentsController@success')->name('payment.success');
	Route::post('payments', 'PaymentsController@MoMoPayment')->name('payments.request');

	Route::get('get-chartdata', 'ReportsController@getMonthlySalesData')->name('chartdata');
	Route::resource('customers', 'CustomersController');
	Route::get('account-settings', 'ProfileController@accountSettings')->name('account-settings');


	Route::get('reports/low-running-stock/{qty?}', 'ReportsController@lowRunningStock')->name('low-stock');
	Route::get('reports/monthly-sales', 'ReportsController@MonthlySales')->name('m-sales');
	Route::get('reports/best-selling-items', 'ReportsController@BestSellingItems')->name('best-selling-items');
	Route::get('reports/top-customers', 'ReportsController@topCustomers')->name('top-customers');
	Route::get('reports/cashiers-performance', 'ReportsController@topCashiers')->name('top-cashiers');
	Route::get('reports/debtors/customers', 'ReportsController@debtorsCustomersList')->name('debtors-customers');
	Route::get('reports/debtors/suppliers', 'ReportsController@debtorsSuppliersList')->name('debtors-suppliers');


	Route::post('notifications/get', 'NotificationController@GetOtherNotifications')->name('unreadNotifications');
	Route::post('notification/unreadEmailNotifications', 'NotificationController@GetUnReadEmailNotifications')->name('unreadEmailNotifications');
	Route::get('notifications', 'NotificationController@markAllRead')->name('readAll');


	Route::get('user-guide', 'DocumentationController@index')->name('userguide');
	Route::get('about-CodeSolutionTech', 'DocumentationController@CompanyDetails')->name('aboutCST');

	Route::get('sms', 'SmsController@index')->name('sms');
	Route::post('send-sms', 'SmsController@SendSMS')->name('sms.store');
	Route::post('pos/handler', 'CartController@PopulateCart')->name('cart.handle');
	Route::get('events/event-form', 'EventsController@ShowEventForm')->name('events.showForm');

	Route::post('purchases/deleteAll', 'PurchasesController@deleteAllPurchases')->name('purchases.truncate');
	Route::post('pos/clear', 'CartController@ClearCart')->middleware('password.confirm');
	Route::post('stock/deleteAll', 'StockController@deleteAllStockItems')->name('stock.truncate');
	Route::post('suppliers/deleteAll', 'SuppliersController@deleteAllSuppliers')->name('suppliers.truncate');
	Route::post('expenses/deleteAll', 'ExpensesController@deleteAllExpenses')->name('expenses.truncate');
	Route::post('/cashiers/deleteAll', 'CashiersController@deleteAllCashiers')->name('cashiers.truncate');
	Route::post('/customers/deleteAll', 'CustomersController@deleteAllCustomers')->name('customers.truncate');
	Route::post('product-categories/deleteAll', 'StockCatsController@deleteAllStockCategories')->name('categories.truncate');
	Route::post('damaged-stock-items/deleteAll', 'DamagesController@deleteAllDamages')->name('damages.truncate');
	Route::post('logs/truncate', 'LogsController@truncateLogs')->name('logs.truncate');


	Route::get('users/active/remove', 'UserController@RemoveAllActiveUsers')->name('active-users.remove')->middleware('password.confirm');
	Route::get('users/locked/remove', 'UserController@RemoveAllLockedUsers')->name('locked-users.remove')->middleware('password.confirm');

	Route::get('/user/lockunlock/{id}/{status}/{name}', 'UserController@LockUnlockAccount')->name('user.lockunlock');

	Route::post('/user/lockunlock/', 'UserController@LockUnlockUserAccount')->name('account.change');

	Route::get('/cashier/change-account/{id}/{status}/{name}', 'CashiersController@ChangeAccountStatus')->name('cashiers.changestatus');

	Route::post('chatbox/commands/import', "ChatBotController@importChatBotCommands")->name("command.import");
	Route::get('chatbox/commands/truncate', "ChatBotController@truncateChatBotCommands")->name("command.truncate")->middleware("password.confirm");

	// Route::put('/profile/update/{id}', 'ProfileController@update')->name('profile.update');


});
