<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Sale;
use App\Models\Supplier;
use App\Models\Damage;
use App\Models\Expense;
use App\Models\Customer;
use App\Models\Role;
use App\Jobs\MailDailySalesReport;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Helpers\Helper;
use Constant;

class SendSalesMade extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:send-sales-made';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'sends amount of sales made a day to the manager';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    
       
    

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(){
        $data = $this->GetReportData();
        MailDailySalesReport::dispatch($data);  
    }



     protected function GetReportData(){

        $controller = 'Command::SendSalesMade';
         try{

        $subject = "Daily Sales Report for today ".date('d-M-Y')."";
         
        $emails = $this->GetManagerEmails();
        $arr = $this->GetSalesReview();

        $totl_sold = $arr['totl_no'];
        $totlSales = $arr['totl_sales'];
        $netValue = $arr['todayNetWorth'];
        
        $data = array(
                 'subject' => $subject,
                 'amount' => $totlSales,
                 'email' => $emails,
                 'totl_no' => $totl_sold,
                 'netValue' => $netValue
        );
        return $data;
    }catch(\Exception $ex){
        \Log::info($ex->getMessage());
        $data = array(
            'username' => auth()->user()->username,
            'error_code' => $ex->getCode(),
            'error_message' => $ex->getMessage(),
            'error_severity' => Constant::$STATUS_ERROR_SEVERITY,
            'controller' => $controller,
            'method' => 'GetReportData'
        );
              Helper::logError($data);
              abort(409, $ex->getMessage());
    }
    }

    protected function GetSalesReview()
    {

    try{
      $total_number_of_sales = Sale::where('date', date('Y-m-d'))->count(); 
      $value1 = Sale::where('date', date('Y-m-d'))->sum('total_buying_cost');
      $value2 = Sale::where('date', date('Y-m-d'))->sum('amount');

      $netValue = ($value2 - $value1);

      $data = array(
          'totl_no' => $total_number_of_sales,
          'totl_sales' => $value2,
          'todayNetWorth' => $netValue
      );

      return $data;
    }catch(\Exception $ex){
        throw $ex;
    }

    }

   
    protected function GetManagerEmails(){
        $roleId = Role::where('is_admin', 1)->where('is_SuperAdmin', 0)
        ->value('role_id');
        $rows = User::where('user_role', $roleId)->get();
        $managersEmails = array();
        foreach ($rows as $row) {
            array_push($managersEmails, $row->email);
        }
        return $managersEmails;
      }


    

  
}
