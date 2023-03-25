<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\StoreRepository;
use Illuminate\Support\Facades\Validator;
use App\DataTables\StoreDataTable;

class StoreController extends Controller
{

    public function __construct(StoreRepository $storeRepository)
    {
        $this->storeRepository = $storeRepository;
    }

    protected $storeRepository;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $total_stores = $this->storeRepository->count();
        return view('pages.main.stores')->with(compact('total_stores'));
    }

    public function getStoresDataTable(StoreDataTable $dataTable)
    {
        return $dataTable->render('pages.main.stores');
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
            'name' => 'required',
        ]);

        try {
            if ($validator->fails()) {
                $message = $validator->errors()->all();
                return response()->json(['error' => $message]);
            } else {

                $name = ucfirst($request->input('name'));
                $exists = $this->storeRepository->exists($name);

                if ($exists) {
                    return response()->json(['error' => 'Store ' . $name . ' already exists']);
                } else {

                    $created_by = $request->user()->id;
                    $data = [
                        'name' => $name,
                        'added_by' => $created_by
                    ];

                    if ($this->storeRepository->create($data)) {
                        $message = "Store " . $name . " has been added successfully";
                        $stats = $this->getStoreStats();
                        $data = [
                            'success' => $message,
                            'data' => $stats,
                        ];
                    } else {
                        $message = "Technical error in adding store";
                        $data = [
                            'error' => $message
                        ];
                    }
                    return response()->json($data);
                }
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
                'name' => 'required',
            ]);

            try {
                if ($validator->fails()) {
                    $message = $validator->errors()->all();
                    return response()->json(['error' => $message]);
                } else {

                    $name = ucfirst($request->input('name'));
                    $exists = $this->storeRepository->existsonUpdate($id, $name);

                    if ($exists) {
                        return response()->json(['error' => 'Store ' . $name . ' already exists']);
                    } else {

                        $data = [
                            'name' => $name,
                        ];

                        if ($this->storeRepository->update($id, $data)) {
                            $message = "Store " . $name . " has been updated successfully";
                            $stats = $this->getStoreStats();
                            $data = [
                                'success' => $message,
                                'data' => $stats
                            ];
                        } else {
                            $message = "Technical error in updating store";
                            $data = [
                                'error' => $message
                            ];
                        }
                        return response()->json($data);
                    }
                }
            } catch (\Exception $ex) {
                return response()->json(['error' => $ex->getMessage()]);
            }
        } else {
            return response()->json(['error' => 'System is unable to get store id']);
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
                $store = $this->storeRepository->find($id);
                $data = [
                    'is_deleted' => !$store->is_deleted
                ];

                if ($this->storeRepository->update($id, $data)) {
                    $message = "Store " . $store->name . " has been deleted successfully";
                    $stats = $this->getStoreStats();
                    $data = [
                        'success' => $message,
                        'data' => $stats
                    ];
                } else {
                    $message = "Technical error in deleting store";
                    $data = [
                        'error' => $message
                    ];
                }
                return response()->json($data);
            } catch (\Exception $ex) {
                return response()->json(['error' => $ex->getMessage()]);
            }
        } else {
            return response()->json(['error' => 'System is unable to get store id']);
        }
    }

    private function sendJson($id)
    {
        try {
            $data = $this->storeRepository->find($id);
            return response()->json(['success' => 'Ok', 'data' => $data]);
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }

    private function getStoreStats()
    {
        try {

            $stores = $this->storeRepository->get();
            $total_stores = $this->storeRepository->count();

            $data = array(
                'data' => $stores,
                'total' => $total_stores
            );

            return $data;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}
