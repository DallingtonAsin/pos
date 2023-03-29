<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use App\Models\Stock;
use App\Models\Customer;
use App\Models\Tax;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\LogsController;
use App\Http\Controllers\LogAfterRequest;
use App\Repositories\SaleRepository;
use App\Repositories\CreditSaleRepository;
use App\Repositories\StockRepository;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{

    private $sold_items = array();
    protected $salesRepository, $creditSaleRepository, $stockRepository;

    public function __construct(SaleRepository $salesRepository, CreditSaleRepository $creditSaleRepository, StockRepository $stockRepository)
    {
        $this->salesRepository = $salesRepository;
        $this->stockRepository = $stockRepository;
        $this->creditSaleRepository = $creditSaleRepository;
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
            throw $ex;
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


    public function recordSale(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'tabledata' => 'required',
            'total_cost' => 'required',
            'amount_paid' => 'required',
            'customer' => 'sometimes|nullable',
            'is_credit' => 'required'
        ]);

        try {
            if ($validator->fails()) {
                $message = $validator->errors()->all();
                return response()->json(['error' => $message]);
            } else {

                $data = $request->input('tabledata');
                $cost = Helper::Numberize($request->input('total_cost'));
                $amount_paid = Helper::Numberize($request->input('amount_paid'));
                $customer_id = $request->input('customer');
                $cashier_id = $request->user()->id;
                $itemArr = json_decode($data, true);
                $on_credit = $cost > $amount_paid;
                if ($on_credit && empty($customer_id)) {
                    return response()->json(['error' => 'Please select customer since items are being taken on credit']);
                }

                if (is_array($itemArr) && count($itemArr) > 0) {

                    $order_number = $this->salesRepository->generateOrderNumber();
                    $total_cost  = 0;

                    foreach ($itemArr as $item) {

                        $item_code = $item['barcode'];
                        $item_name = $item['item'];
                        $this->sold_items[] = $item_name;
                        $quantity = Helper::Numberize($item['quantity']);
                        $price = Helper::Numberize($item['price']);
                        $discount = Helper::Numberize($item['discount']);
                        $total = Helper::Numberize($item['total']);
                        $date_of_sale = $item['date_of_sale'];
                        $arr = $this->getPrices($item_name);
                        $original_price = $arr['bprice'];
                        $total_cost += $total;

                        // Get new quantity of item after sale
                        $qty_beforeSale = $this->getQtyBeforeSale($item_name);
                        $newqty = ($qty_beforeSale - $quantity);
                        $date = !empty($date_of_sale) ? date('Y-m-d', strtotime($date_of_sale)) : date('Y-m-d');
                        $time = date('H:i:s');

                        $taxAmount = $this->GetTax($total);

                        $sale_data = [
                            'order_number' => $order_number,
                            'item_code' => $item_code,
                            'item' => $item_name,
                            'quantity' => $quantity,
                            'original_price' => $original_price,
                            'selling_price' => $price,
                            'discount' => $discount,
                            'amount' => $total,
                            'tax' => $taxAmount,
                            'date' => $date,
                            'time' => $time,
                            'customer_id' => $customer_id,
                            'cashier_id' => $cashier_id
                        ];

                        $this->salesRepository->create($sale_data); // insert into sales
                        $this->stockRepository->updateByItemName($item_name, ['quantity' => $newqty]); // update stock
                    }

                    $is_credit = $total_cost > $amount_paid;
                    if ($is_credit) {

                        $amount_due = $total_cost - $amount_paid;
                        $credit_sale = [
                            'sale_order_number' => $order_number,
                            'customer_id' => $customer_id,
                            'date' =>  date('Y-m-d'),
                            'total_cost' => $total_cost,
                            'amount_paid' => $amount_paid,
                            'amount_due' => $amount_due
                        ];

                        $this->creditSaleRepository->create($credit_sale);
                    }

                    $action = "recorded a sale of items " . json_encode($this->sold_items) . " at  " . number_format($total_cost) . " ";
                    LogsController::logger($request, $action, now());
                    $dataArr = [
                        "code" => '200',
                        "message" => $action,
                        "method" => "CartController@recordSale"
                    ];
                    LogAfterRequest::LogRequest($request, $dataArr);
                    $message = $this->ActionMessage($action);

                    return response()->json(['success' => 'Sale transaction recorded successfully']);
                }
            }
        } catch (\Exception $ex) {
            return response()->json(['error' =>  $ex->getMessage()]);
        }
    }

    protected function getPrices($item)
    {
        try {

            $data = DB::select('select buying_price, selling_price from stock where item = ? or item_code = ?', [$item, $item]);
            foreach ($data as $value) {
                $bprice = $value->buying_price;
                $sprice = $value->selling_price;
            }

            return ["bprice" => $bprice,  "sprice" => $sprice];
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    protected function getQtyBeforeSale($item)
    {
        try {
            $arr = $this->getListOfStockItemsData();
            $stockArr = $arr['items'];
            $stockIdArr = $arr['itemsIds'];

            if (in_array($item, $stockArr) || in_array($item, $stockIdArr)) {
                $data = Stock::where("item_code", "like", "%" . $item . "%")->orWhere("item", "like", "%" . $item . "%")->get();
                foreach ($data as $value) {
                    $qty = $value->quantity;
                }
            } else {
                $qty = -1;
            }
            return $qty;
        } catch (\Exception $ex) {
            throw $ex;
        }
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


    public function getItemData(Request $request)
    {

        if ($request->input('itemId')) {

            $query_str = $request->input('itemId');
            $isBarcode = $request->input('isBarcode');

            if ($isBarcode == 1) {
                $itemData = $this->stockRepository->getItemByCode($query_str);
            } else {
                $itemData = $this->stockRepository->getItemByName($query_str);
            }

            return response()->json(['data' => $itemData]);
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
