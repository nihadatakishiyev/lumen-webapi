<?php

namespace App\Http\Controllers;

use App\Office;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;

class ApiController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param $link
     * @return void
     */
    public function asanFinance()
    {
            try {
                    $res = array(); 
                    $query1 = DB::select("select top 5 su.consumer_id, c.name, sum(su.cost) as total_cost
                            from rel.ServiceUsage su
                            left join list.Consumers c on su.consumer_id = c.id
                            where cast(su.create_date as date) = cast(getdate() as date)
                            and service_usage_status_id = 1
                            group by consumer_id, c.name
                            order by total_cost desc");
                    $res[] = $query1;
                    $res[0]['header'] = 'Top 5 consumer profits'; 


                    $query2 = DB::select('select top 5  su.service_id, s.name, sum(su.cost) as total_cost
                                            from rel.ServiceUsage su
                                            left join list.Services s on su.service_id = s.ID
                                            where cast(su.create_date as date) = cast(getdate() as date)
                                            and service_usage_status_id = 1
                                            group by service_id, name
                                            order by total_cost desc');
                    $res[] = $query2;
                    $res[1]['header'] = 'Top 5 service profits'; 


                    $query3 = DB::select('select top 5  c.name, s.name, sum(su.cost) as total_cost
                                            from rel.ServiceUsage su
                                            left join list.Services s on su.service_id = s.ID
                                            left join list.Consumers c on c.id = su.consumer_id
                                            where cast(su.create_date as date) = cast(getdate() as date)
                                            and service_usage_status_id = 1
                                            group by service_id, s.name, consumer_id, c.name
                                            order by total_cost desc');
                    $res[] = $query3;
                    $res[2]['header'] = 'Top rewarding services( within company scope'; 


                    $query4 = DB::select('select top 5 s.name, sum(su.cost) as total_cost,
                                            sum(case when service_usage_status_id = 1 then 1 else 0 end) as scsfl,
                                            sum(case when service_usage_status_id = 2 then 1 else 0 end) as unscsfl,
                                            sum(case when service_usage_status_id = 1 then 1 else 0 end) * 100 / sum(case when service_usage_status_id = 1 then 1 else 1 end) as success_rate
                                            from rel.ServiceUsage su
                                            left join list.Services s on su.service_id = s.ID
                                            where cast(su.create_date as date) = cast(getdate() as date)
                                            group by service_id, s.name
                                            order by unscsfl desc,success_rate');
                    $res[] = $query4;
                    $res[3]['header'] = 'Services\' success rate';                                           


                    $query5 = DB::select('select top 5  consumer_id, c.name, s.name, sum(su.cost) as total_cost,
                                            sum(case when service_usage_status_id = 1 then 1 else 0 end) as scsfl,
                                            sum(case when service_usage_status_id = 2 then 1 else 0 end) as unscsfl,
                                            sum(case when service_usage_status_id = 1 then 1 else 0 end) * 100 / sum(case when service_usage_status_id = 1 then 1 else 1 end) as success_rate
                                            from rel.ServiceUsage su
                                            left join list.Services s on su.service_id = s.ID
                                            left join list.Consumers c on c.id = su.consumer_id
                                            where cast(su.create_date as date) = cast(getdate() as date)
                                            group by service_id, s.name, consumer_id, c.name
                                            order by unscsfl desc,success_rate');

                    $res[] = $query5;
                    $res[4]['header'] = 'Consumerler services success_rate top list'; 


                    $query6 = DB::select(';with cte as (
                                                select
                                                ROW_NUMBER() over(partition by service_id order by sum(case when service_usage_status_id = 1 then 1 else 0 end) * 100 / sum(case when service_usage_status_id = 1 then 1 else 1 end)) as rn,
                                                consumer_id, c.name cons_name, s.name serv_name, sum(su.cost) as total_cost,
                                                sum(case when service_usage_status_id = 1 then 1 else 0 end) as scsfl,
                                                sum(case when service_usage_status_id = 2 then 1 else 0 end) as unscsfl,
                                                sum(case when service_usage_status_id = 1 then 1 else 0 end) * 100 / sum(case when service_usage_status_id = 1 then 1 else 1 end) as success_rate
                                                from rel.ServiceUsage su
                                                left join list.Services s on su.service_id = s.ID
                                                left join list.Consumers c on c.id = su.consumer_id
                                                where cast(su.create_date as date) = cast(getdate() as date)
                                                group by service_id, s.name, consumer_id, c.name
                                                ) select * from cte where rn = 1 and success_rate <> 100
                                                order by success_rate');

                    $res[] = $query6;
                    $res[5]['header'] = 'success rate overall'; 
                

                return response(($res), 200); 
            }
            catch (\Exception $e){
                return Response($e->getMessage(),  404); //status code to be updated later
            }
        }


}
