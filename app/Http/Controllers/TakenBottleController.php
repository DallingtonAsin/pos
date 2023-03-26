<?php

namespace App\Http\Controllers;

use App\DataTables\TakenBottleDataTable;
use App\Helpers\Helper;
use Illuminate\Http\Request;
use App\Repositories\CustomerRepository;
use App\Repositories\TakenBottleRepository;
use App\Repositories\StockRepository;
use Illuminate\Support\Facades\Validator;

class TakenBottleController extends Controller
{
    protected $customerRepository, $takenBottleRepository, $stockRepository;

    public function __construct(CustomerRepository $customerRepository, TakenBottleRepository $takenBottleRepository, StockRepository $stockRepository)
    {
        $this->customerRepository = $customerRepository;
        $this->takenBottleRepository = $takenBottleRepository;
        $this->stockRepository = $stockRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $no_of_bottles = $this->takenBottleRepository->count();
        $customers = $this->customerRepository->get();
        $stock =  $this->stockRepository->get();

        return view('pages.main.inventory.taken_bottles')->with(compact('customers', 'stock', 'no_of_bottles'));
    }

    public function getTakenBottleDataTable(TakenBottleDataTable $dataTable)
    {
        return $dataTable->render('pages.main.inventory.taken_bottles');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer' => 'required',
            'bottle' => 'required',
            'quantity' => 'required',
            'taken_on' => 'required',
            'status' => 'required',
            'returned_on' => 'sometimes|nullable',
        ]);

        try {
            if ($validator->fails()) {
                $message = $validator->errors()->all();
                return response()->json(['error' => $message]);
            } else {

                $customer_id = $request->input('customer');
                $bottle_id = $request->input('bottle');
                $quantity = Helper::Numberize($request->input('quantity'));
                $taken_on = $request->input('taken_on');
                $status = boolval($request->input('status'));
                $returned_on = $status == 1 ? $request->input('returned_on') : null;

                $created_by = $request->user()->id;

                $data = [
                    'customer_id' => $customer_id,
                    'bottle_id' => $bottle_id,
                    'quantity' => $quantity,
                    'taken_on' => $taken_on,
                    'is_returned' => $status,
                    'returned_on' => $returned_on,
                    'added_by' => $created_by
                ];

                if ($this->takenBottleRepository->create($data)) {
                    $customer = $this->customerRepository->find($customer_id);
                    $message = "Empty taken by customer " . $customer->name . " has been added successfully";
                    $stats = $this->getTakenBottleStats();
                    $data = [
                        'success' => $message,
                        'data' => $stats,
                    ];
                } else {
                    $message = "Technical error in adding taken bottle";
                    $data = [
                        'error' => $message
                    ];
                }
                return response()->json($data);
            }
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->sendJson($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return $this->sendJson($id);
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
        if (!empty($id)) {

            $validator = Validator::make($request->all(), [
                'customer' => 'required',
                'bottle' => 'required',
                'quantity' => 'required',
                'taken_on' => 'required',
                'status' => 'required',
                'returned_on' => 'sometimes|nullable'
            ]);

            try {
                if ($validator->fails()) {
                    $message = $validator->errors()->all();
                    return response()->json(['error' => $message]);
                } else {

                    $customer_id = $request->input('customer');
                    $bottle_id = $request->input('bottle');
                    $quantity = Helper::Numberize($request->input('quantity'));
                    $taken_on = $request->input('taken_on');
                    $status = boolval($request->input('status'));
                    $returned_on = $status == 1 ? $request->input('returned_on') : null;
                    $created_by = $request->user()->id;

                   

                    $data = [
                        'customer_id' => $customer_id,
                        'bottle_id' => $bottle_id,
                        'quantity' => $quantity,
                        'taken_on' => $taken_on,
                        'is_returned' => $status,
                        'returned_on' => $returned_on,
                        'added_by' => $created_by
                    ];

                    if ($this->takenBottleRepository->update($id, $data)) {
                        $customer = $this->customerRepository->find($customer_id);
                        $message = "Empty taken by customer " . $customer->name . " has been updated successfully";

                        $stats = $this->getTakenBottleStats();
                        $data = [
                            'success' => $message,
                            'data' => $stats
                        ];
                    } else {
                        $message = "Technical error in updating taken bottle details";
                        $data = [
                            'error' => $message
                        ];
                    }
                    return response()->json($data);
                }
            } catch (\Exception $ex) {
                return response()->json(['error' => $ex->getMessage()]);
            }
        } else {
            return response()->json(['error' => 'System is unable to get taken bottle id']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!empty($id)) {

            try {

                $id = intval($id);
                $takenBottle = $this->takenBottleRepository->find($id);
                $data = [
                    'is_deleted' => !$takenBottle->is_deleted
                ];

                if ($this->takenBottleRepository->update($id, $data)) {
                    $customer = $this->customerRepository->find($takenBottle->customer_id);
                    $message = "Empty taken by customer " . $customer->name . " has been deleted successfully";

                    $stats = $this->getTakenBottleStats();
                    $data = [
                        'success' => $message,
                        'data' => $stats
                    ];
                } else {
                    $message = "Technical error in deleting taken bottle details";
                    $data = [
                        'error' => $message
                    ];
                }
                return response()->json($data);
            } catch (\Exception $ex) {
                return response()->json(['error' => $ex->getMessage()]);
            }
        } else {
            return response()->json(['error' => 'System is unable to get supplier credit id']);
        }
    }

    private function sendJson($id)
    {
        try {
            $data = $this->takenBottleRepository->find($id);
            $data->bottle = $this->stockRepository->find($data->bottle_id)->item;
            $data->customer = $this->customerRepository->find($data->customer_id)->name;
            return response()->json(['success' => 'Ok', 'data' => $data]);
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }

    private function getTakenBottleStats()
    {
        try {

            $credits = $this->takenBottleRepository->get();
            $no_of_bottles = $this->takenBottleRepository->count();

            $data = array(
                'data' => $credits,
                'no_of_bottles' => $no_of_bottles
            );

            return $data;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}
