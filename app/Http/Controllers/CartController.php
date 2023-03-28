<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Stock;
use App\Models\Sale;
use App\Models\Customer;
use App\Models\Tax;
use App\Models\SalesTaxTracker;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\LogsController;
use App\Http\Controllers\LogAfterRequest;
use App\Services\ReceiptGenerator;
use App\Repositories\SaleRepository;

class CartController extends Controller
{

    private $total_amount_of_sales = 0;
    private $sold_items = array();
    protected $salesRepository;

    public function __construct(SaleRepository $salesRepository)
    {
        $this->salesRepository = $salesRepository;
    }

    public function GetCartData(Request $request)
    {

        if ($request->input('itemId')) {

            $itemId = $request->input('itemId');
            $isBarcode = $request->input('isBarcode');

            if ($isBarcode == 1) {
                $itemData = Stock::where('item_code', $itemId)->get();
            } else {
                $itemData = Stock::where('item', $itemId)->get();
            }
            return json_encode(array('data' => $itemData));
        }
    }


    protected function GetIndexOfArr($arr, $code)
    {
        for ($i = 0; $i < count($arr); $i++) {
            if ($arr[$i]["code"] == $code) {
                return $i;
            }
        }
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $customers = Customer::select(['id', 'name'])->get();
            return view('pages.main.pos.index')->with(compact('customers'));
        } catch (\Exception $ex) {
            dd($ex->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.main.pos.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $req)
    {

        $req->validate([
            'item-name' => 'required',
        ]);

        $cart = new Cart();
        $item = $req->input('item-name');
        $qty =  trim($req->input('qty'));
        $discount = trim($req->input('discount'));


        (empty($qty)) ? $quantity = 1 : $quantity =  floatval($qty);

        $dataCheck = $this->GetItemRef($item);
        $refId = $dataCheck['refId'];

        if ($refId != null) {


            //get quantity available before adding to cart
            $qty_available = $this->getQtyBeforeSale($item);

            if ($qty_available >= $quantity) {

                $priceArr = $this->getPrices($item);
                $price = floatval($priceArr["sprice"]); //method call for selling price of an item

                if (isset($discount)) {
                    $discount = floatval($discount);
                    $amount = $quantity * ($price - $discount);
                } else {
                    $discount = 0;
                    $amount = $quantity * $price;
                }


                if ($refId == 'name') {
                    $item_code = $dataCheck['ref'];
                    $item_name = $item;
                } else if ($refId == 'id') {
                    $item_code = $item;
                    $item_name =  $dataCheck['ref'];
                }


                $cart->item_code = $item_code;
                $cart->item = $item_name;
                $cart->quantity = $quantity;
                $cart->price = $price;
                $cart->discount = $discount;
                $cart->amount = $amount;

                $save_status = $cart->save();

                if ($save_status) {
                    $action = "added item " . $item . " to the cart";
                    LogsController::logger($req, $action, now());
                    $dataArr = array(
                        "code" => '200',
                        "message" => $action,
                        "method" => "CartController@store"
                    );
                    LogAfterRequest::LogRequest($req, $dataArr);
                    return back();
                } else {

                    $error_message = "cart item not added failed!";
                    $dataArr = array(
                        "code" => '101',
                        "message" => $error_message,
                        "method" => "CartController@store"
                    );
                    LogAfterRequest::LogRequest($req, $dataArr);
                    return back()->with('fail', $error_message);
                }
            } else if ($qty_available < $quantity && $qty_available != -1) {
                $error_message = "Quantity for item " . $item . " is not enough,Available is " . $qty_available . "";
                $dataArr = array(
                    "code" => '101',
                    "message" => $error_message,
                    "method" => "CartController@store"
                );
                LogAfterRequest::LogRequest($req, $dataArr);
                return back()->with("fail", $error_message);
            } else {
                $error_message = "couldn't find this product " . $item . "";
                $dataArr = array(
                    "code" => '404',
                    "message" => $error_message,
                    "method" => "CartController@store"
                );
                LogAfterRequest::LogRequest($req, $dataArr);
                return back()
                    ->with("fail", $error_message);
            }
        }
    } // end of method store

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $req, $id)
    {

        $req->validate([
            'qty' => 'required',
        ]);


        try {

            $cart = Cart::find($id);
            $item = $cart->item;
            $qty = trim($req->input('qty'));

            ($req->input('discount') && $req->filled('discount'))
                ? $discount = trim($req->input('discount'))
                : $discount = 0;


            (empty($qty)) ? $quantity = 1 : $quantity =  floatval($qty);

            $dataCheck = $this->GetItemRef($item);
            $refId = $dataCheck['refId'];

            if ($refId != null) {


                //get quantity available before adding to cart
                $qty_available = $this->getQtyBeforeSale($item);

                if ($qty_available >= $quantity) {

                    $priceArr = $this->getPrices($item);
                    $price = floatval($priceArr["sprice"]); //method call for selling price of an item

                    if (isset($discount)) {
                        $discount = floatval($discount);
                        $amount = $quantity * ($price - $discount);
                    } else {
                        $discount = 0;
                        $amount = $quantity * $price;
                    }


                    if ($refId == 'name') {
                        $item_code = $dataCheck['ref'];
                        $item_name = $item;
                    } else if ($refId == 'id') {
                        $item_code = $item;
                        $item_name =  $dataCheck['ref'];
                    }


                    $cart->item_code = $item_code;
                    $cart->item = $item_name;
                    $cart->quantity = $quantity;
                    $cart->price = $price;
                    $cart->discount = $discount;
                    $cart->amount = $amount;

                    $save_status = $cart->save();

                    if ($save_status) {
                        $action = "added item " . $item . " to the cart";
                        LogsController::logger($req, $action, now());
                        $dataArr = array(
                            "code" => '200',
                            "message" => $action,
                            "method" => "CartController@store"
                        );
                        LogAfterRequest::LogRequest($req, $dataArr);
                        return back();
                    } else {

                        $error_message = "cart item not added failed!";
                        $dataArr = array(
                            "code" => '101',
                            "message" => $error_message,
                            "method" => "CartController@store"
                        );
                        LogAfterRequest::LogRequest($req, $dataArr);
                        return back()->with('fail', $error_message);
                    }
                } else if ($qty_available < $quantity && $qty_available != -1) {
                    $error_message = "Quantity for item " . $item . " is not enough,Available is " . $qty_available . "";
                    $dataArr = array(
                        "code" => '101',
                        "message" => $error_message,
                        "method" => "CartController@store"
                    );
                    LogAfterRequest::LogRequest($req, $dataArr);
                    return back()->with("fail", $error_message);
                } else {
                    $error_message = "couldn't find this product " . $item . "";
                    $dataArr = array(
                        "code" => '404',
                        "message" => $error_message,
                        "method" => "CartController@store"
                    );
                    LogAfterRequest::LogRequest($req, $dataArr);
                    return back()
                        ->with("fail", $error_message);
                }
            }
        } catch (\Exception $exception) {
            parent::report($exception);
        }
    }


    private function DoTaxMathTracking($data)
    {

        $itemId = $data[0];
        $item = $data[1];
        $qty = $data[2];
        $amount = $data[3];
        $tax = $data[4];
        $soldOn = $data[5];

        $tracker = new SalesTaxTracker;
        $tracker->item_code = $this->customCrypt($itemId);
        $tracker->item = $this->customCrypt($item);
        $tracker->quantity = $this->customCrypt($qty);
        $tracker->amount = $this->customCrypt($amount);
        $tracker->tax = $this->customCrypt($tax);
        $tracker->date_of_sale = $this->customCrypt($soldOn);

        $tracker->save();
    }


    private function customCrypt($str)
    {
        $customKey = config('app.cipherKey');
        $newEncrypter = new \Illuminate\Encryption\Encrypter($customKey, config('app.cipher'));
        return $newEncrypter->encrypt($str);
    }


    public function recordSale(Request $req)
    {

        try {

            $method = "CartController@recordSale";

            $data = $req->input('tabledata');
            $customer_id = $req->input('customer');
            $cashier_id = $req->user()->id;
            $extra_money = $req->input('extra_money');

            if (!empty($extra_money)) {
                $extra_money = floatval($extra_money);
            } else {
                $extra_money = 0;
            }
            $dataArr = json_decode($data, true);

            if (is_array($dataArr) && count($dataArr) > 0) {

                $order_number = $this->salesRepository->generateOrderNumber();

                foreach ($dataArr as $key) {

                    $item_code = $key['barcode'];
                    $item = $key['item'];
                    $this->sold_items[] = $item;
                    $quantity = floatval(str_replace(',', '', $key['quantity']));
                    $price = floatval(str_replace(',', '', $key['price']));
                    $subtotal = floatval(str_replace(',', '', $key['subtotal']));
                    $discount = floatval(str_replace(',', '', $key['discount']));
                    $total = floatval(str_replace(',', '', $key['total']));
                    $paid_amount = floatval(str_replace(',', '', $key['paid']));


                    $isCredit = filter_var($key['is_credit'], FILTER_VALIDATE_BOOLEAN);

                    if ($isCredit == true && $total == $paid_amount) {
                        $amount_paid = 0;
                        $balance = $paid_amount;
                    } else {
                        $amount_paid = $paid_amount;
                        $balance = $total - $paid_amount;
                    }

                    if ($isCredit == true) {
                        $is_credit = 1;
                        $fully_paid = 0;
                    } else {
                        $is_credit = 0;
                        $fully_paid = 1;
                    }

                    $date_of_sale = $key['date_of_sale'];

                    $arr = $this->getPrices($item);
                    $original_price = $arr['bprice'];
                    $this->total_amount_of_sales += floatval($subtotal);

                    // Get new quantity of item after sale
                    $qty_beforeSale = $this->getQtyBeforeSale($item);
                    $newqty = ($qty_beforeSale - $quantity);
                    $date = isset($date_of_sale) ? $date_of_sale : date('Y-m-d');
                    $time = date('H:i:s');

                    $taxAmount = $this->GetTax($total);

                    // insert cart data into database
                    $hasInsertedInSalesTbl = Sale::insert([
                        'order_number' => $order_number,
                        'item_code' => $item_code,
                        'item' => $item,
                        'quantity' => $quantity,
                        'original_price' => $original_price,
                        'selling_price' => $price,
                        'discount' => $discount,
                        'amount' => $total,
                        'paid_amount' => $amount_paid,
                        'is_credit' => $is_credit,
                        'fully_paid' => $fully_paid,
                        'balance' => $balance,
                        'extra_money' => $extra_money,
                        'tax' => $taxAmount,
                        'date' => $date,
                        'time' => $time,
                        'customer_id' => $customer_id,
                        'cashier_id' => $cashier_id
                    ]);

                    $datetime = $date . " " . $time;
                    $taxArr = array($item_code, $item, $quantity, $subtotal, $taxAmount, $datetime);
                    $this->DoTaxMathTracking($taxArr);

                    //If insertion is OK, reduce stock levels and clear cart
                    if ($hasInsertedInSalesTbl) {

                        $hasUpdatedStock = Stock::where('item', $item)
                            ->update(['quantity' => $newqty]);

                        //message the user about state of sale
                        if ($hasUpdatedStock) {

                            $action = "recorded a sale of items " . json_encode($this->sold_items) . " at
                            " . number_format($this->total_amount_of_sales) . " ";
                            LogsController::logger($req, $action, now());
                            $dataArr = array(
                                "code" => '200',
                                "message" => $action,
                                "method" => $method
                            );
                            LogAfterRequest::LogRequest($req, $dataArr);
                            $response = $this->ActionMessage($action); // back()->with('success', $this->ActionMessage($action));

                        } else {
                            $stockErr = "Failed to update stock after transaction";
                            $dataArr = array(
                                "code" => '101',
                                "message" => $stockErr,
                                "method" => $method
                            );
                            LogAfterRequest::LogRequest($req, $dataArr);
                            $response = $stockErr;
                        }
                    } else {

                        $cartInsertionErr = "Failed to insert sale details into sales table";
                        $dataArr = array(
                            "code" => '101',
                            "message" => $cartInsertionErr,
                            "method" => $method
                        );

                        LogAfterRequest::LogRequest($req, $dataArr);
                        $response = $cartInsertionErr;
                    }
                } // end of foreach

                return $response;
            }
        } catch (\Exception $ex) {
            return response()->json(['error' =>  $ex->getMessage()]);
        }
    }




    public function getReceipt(ReceiptGenerator $rg)
    {
        try {
            $rg = new ReceiptGenerator();
            return $rg->generateReceipt();
        } catch (\Exception $ex) {
            throw $ex;
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

        $cart = Cart::find($id);
        $cart = $cart->item;
        $delete_status = $cart->delete();
        if ($delete_status) {

            $action = "removed " . $cart . " from the list of items in cart";
            LogsController::logger($request, $action, now());
            $dataArr = array(
                "code" => '200',
                "message" => $action,
                "method" => "CartController@destroy"
            );
            LogAfterRequest::LogRequest($request, $dataArr);
            return back()->with("success", $this->ActionMessage($action));
        } else {
            $failErr = "item in cart not deleted!";
            $dataArr = array(
                "code" => '101',
                "message" => $failErr,
                "method" => "CartController@destroy"
            );
            LogAfterRequest::LogRequest($request, $dataArr);
            return back()->with('fail', $failErr);
        }
    }

    protected function getPrices($item)
    {

        $data = DB::select('select buying_price, selling_price
                            from stock where item = ? or item_code = ?', [$item, $item]);
        foreach ($data as $value) {
            $bprice = $value->buying_price;
            $sprice = $value->selling_price;
        }
        return array(
            "bprice" => $bprice,
            "sprice" => $sprice,
        );
    }

    protected function getQtyBeforeSale($item)
    {
        $arr = $this->getListOfStockItemsData();
        $stockArr = $arr['items'];
        $stockIdArr = $arr['itemsIds'];

        if (in_array($item, $stockArr) || in_array($item, $stockIdArr)) {
            $data = Stock::where("item_code", "like", "%" . $item . "%")
                ->orWhere("item", "like", "%" . $item . "%")
                ->get();

            foreach ($data as $value) {
                $qty = $value->quantity;
            }
        } else {
            $qty = -1;
            // return back()
            //        ->with("fail", "couldn't find this product");
        }
        return $qty;
    }


    protected function getCartItems()
    {
        $items =  Cart::get();
        return $items;
    }



    public function ClearCart()
    {
        Cart::truncate();
        return back();
    }

    protected function searchItem(Request $request)
    {

        if ($request->input('query')) {
            $query = $request->input('query');
            $data = array();
            $items = Stock::where("item_code", "like", "%" . $query . "%")
                ->orWhere("item", "like", "%" . $query . "%")
                ->get();

            foreach ($items as $item) {
                $data[] = $item->item;
                $data[] = $item->item_code;
            }
            echo json_encode($data);
        }
    }

    protected function getItemPrice(Request $request)
    {
        if ($request->input('item')) {
            $query = $request->input('item');
            $data = array();
            $items = Stock::where("item_code", "like", "%" . $query . "%")
                ->orWhere("item", "like", "%" . $query . "%")
                ->get();
            foreach ($items as $item) {
                $data[] = $item->selling_price;
            }
            echo json_encode($data);
        }
    }

    protected function ActionMessage($action)
    {
        $message = "You have successfully " . $action . "";
        return $message;
    }

    protected function getListOfStockItemsData()
    {

        $items = Stock::get();
        $itemsArr = $itemsIdArr = array();
        foreach ($items as $item) {
            array_push($itemsArr, $item->item);
            array_push($itemsIdArr, $item->item_code);
        }

        return array(
            'items' => $itemsArr,
            'itemsIds' => $itemsIdArr
        );
    }


    protected function GetItemRef($item)
    {
        $arr = $this->getListOfStockItemsData();
        $stockList = $arr['items'];
        $stockIdsList = $arr['itemsIds'];

        if (in_array($item, $stockList)) {
            $ref = Stock::where('item', $item)->value('item_code');
            $refId = 'name';
        } else if (in_array($item, $stockIdsList)) {
            $ref = Stock::where('item_code', $item)->value('item');
            $refId = 'id';
        } else {
            $ref = null;
            $refId = null;
        }

        $dataArr = array(
            "item" => $item,
            "ref" => $ref,
            "refId" => $refId,

        );

        return $dataArr;
    }


    private function GetTax($amount)
    {
        $sale = Tax::where('tax_name', 'sales')->value('tax_percentage');
        $salesPercent = floatval($sale);
        $taxCharge = 0.01 * $salesPercent * $amount;
        return $taxCharge;
    }
}
