<?php

namespace App\Http\Controllers;

use App\Office;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;

class OfficeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param $link
     * @return void
     */
    public function index($link)
    {
        if ($link =='1'){
            try {
                $res = DB::select("select top 5 su.consumer_id, c.name, sum(su.cost) as total_cost
                from rel.ServiceUsage su
                left join list.Consumers c on su.consumer_id = c.id
                where cast(su.create_date as date) = cast(getdate() as date)
                and service_usage_status_id = 1
                group by consumer_id, c.name
                order by total_cost desc");
                return response($res, 200);
            }
            catch (\Exception $e){
                return Response($e->getMessage(),  404);
            }
        }

    }


}
