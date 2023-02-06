<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\DataTables\CompanyDataTable;
use App\Http\Controllers\LogsController;
use Illuminate\Support\Facades\Validator;


class SettingsController extends Controller
{


    public function GetCompanies(CompanyDataTable $dataTable)
    {
        return $dataTable->render('pages.main.company-details');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $companies = Company::all();
        $number_of_companies = Company::count();
        return view('pages.main.company-details')->with(compact('companies', 'number_of_companies'));
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
        //
    }



    public function showCreateCoForm()
    {
        $company = Company::where('name', '!=', null)->first();
        return view('pages.main.add-edit-company')->with(compact('company'));
    }

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



    public function addUpdateCompany(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'abbrev' => 'sometimes|nullable',
            'email' => 'required',
            'address' => 'required',
            'phone_number' => 'required'
        ]);

        try {
            if ($validator->fails()) {

                return back()
                    ->withErrors($validator)
                    ->withInput();

            } else {

                $name = $request->input('name');
                $abbrev = $request->input('abbrev');
                $email = $request->input('email');
                $address = $request->input('address');
                $phone_number = $request->input('phone_number');

                (isset($id) && $id != 0) ? $company = Company::find($id) : $company = new Company();

                $company->name = $name;
                $company->abbrev = $abbrev;
                $company->phone_number = $phone_number;
                $company->email = $email;
                $company->address = $address;

                if ($request->hasfile('logo')) {

                    $this->validate($request, [
                        'logo' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                    ]);

                    $file = $request->file('logo');
                    $extension = $file->getClientOriginalExtension();
                    $filename = time() . '.' . $extension;
                    $file->move("uploads/images/company/logo", $filename);
                    $company->logo = $filename;
                }

                if ($company->save()) {
                    (isset($id) && $id != 0) ? $notice = 'updated' : $notice = 'registered';
                    $action =  "" . $notice . " company " . $name . " profile";
                    LogsController::logger($request, $action, now());
                    return back()->with('success', $this->ActionMessage($action));
                } else {
                    (isset($id) && $id != 0) ? $fnotice = 'updating' : $fnotice = 'registering';
                    return  back()->withInput()->with('fail', '' . $fnotice . ' company profile failed');
                }
            }
        } catch (\Exception $ex) {
            return  back()->withInput()->with('fail', $ex->getMessage());
        }
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
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    protected function ActionMessage($action)
    {
        $message = "You have successfully " . $action . "";
        return $message;
    }
}
