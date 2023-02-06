<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\DataTables\CompanyDataTable;
use App\Http\Controllers\LogsController;
use App\Helpers\Helper;

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



    public function showCreateCoForm(){
        $company = Company::where('company_name', '!=', null)->first();
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



 public function addUpdateCompany(Request $request, $id){
   
    $this->validate($request, [
        'company_name' => 'required',
        'company_email' => 'required',
        'company_address' => 'required',
      ]);

      try{

      $company_name = $request->input('company_name');
      $company_abbrev = $request->input('company_abbrev');
      $company_email = $request->input('company_email');
      $company_address = $request->input('company_address');
      $company_motto = $request->input('company_motto');

    (isset($id) && $id != 0) ? $company = Company::find($id) : $company = new Company();
 
    $company->company_name = $company_name;
    $company->company_abbrev = $company_abbrev;
    $company->company_email = $company_email;
    $company->company_address = $company_address;
    $company->company_motto = $company_motto;
 

     if($request->hasfile('company_logo')){
        $this->validate($request, [
           'company_logo' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
       ]);

        $file = $request->file('company_logo');
            $extension = $file->getClientOriginalExtension();
            $filename = time().'.'.$extension;
            $file->move("uploads/images/company/logo",$filename);
            $company->company_logo = $filename;
     }
      
        $result = $company->save();

        if($result) {
            (isset($id) && $id != 0) ? $notice = 'updated' : $notice = 'registered';
            $action =  "".$notice." company ".$company_name." profile";
            LogsController::logger($request, $action, now());
            return back()->with('success', $this->ActionMessage($action));
        }
        else{
            (isset($id) && $id != 0) ? $fnotice = 'updating' : $fnotice = 'registering';
            return  back()->with('fail',''.$fnotice.' company profile failed');
        }

    }catch(\Exception $ex){
        dd($ex->getMessage());
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
      $message = "You have successfully ".$action."";
      return $message;
    }




}
