<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\News;
use Illuminate\Support\Facades\Log;

class NewsController extends Controller
{
    //
    public function list(Request $request)
    {
        $validator = Validator::make($request->all(), [            
            'page' => 'required|numeric|min:1',
            'results' => 'required|numeric|min:10',
            'start_date' => 'string|date_format:Y-m-d|before_or_equal:end_date|nullable',
            'end_date' => 'string|date_format:Y-m-d|after_or_equal:start_date|nullable',
            'timezone' => 'required_with_all:start_date,end_date|string',
            'category' => 'string|max:10',
            'keyword' => 'string|max:100|nullable',
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => false,
                'validator' => $validator->errors() 
            ], 400);
        }

        $queryBuilder = News::when(isset($request->start_date) && isset($request->end_date) && isset($request->timezone), function ($query) use ($request) {
                // 取得使用者 timezone 並轉成 UTC 查詢
                $timezone = new \DateTimeZone($request->timezone);
                $UTC = new \DateTimeZone('UTC');
                $datetime = new \DateTime("$request->start_date 00:00:00", $timezone);
                $datetime->setTimezone($UTC);
                $start_date = $datetime->format('Y-m-d H:i:s');
                $datetime = new \DateTime("$request->end_date 23:59:59", $timezone);
                $datetime->setTimezone($UTC);
                $end_date = $datetime->format('Y-m-d H:i:s');
                return $query->whereBetween('gmdate', [$start_date, $end_date]);
            })
            ->when(isset($request->category), function ($query) use ($request) {
                return $query->where('category', '=', $request->category);
            })
            ->when(isset($request->keyword), function ($query) use ($request) {
                return $query->where(function ($query) use ($request) {                    
                    $query->whereRaw('match(title, content) against (? IN BOOLEAN MODE)', [$request->keyword]);
                });
            });
        $total = $queryBuilder->count('id');
        // $sql = $queryBuilder->skip(($request->page-1) * $request->results)->take($request->results)->orderBy('gmdate', 'DESC')->toSql();
        // Log::debug($sql);
        $result = $queryBuilder->skip(($request->page-1) * $request->results)->take($request->results)->orderBy('gmdate', 'DESC')->get();
        return response()->json([
            "results" => $result,
            "total" => $total
        ]);
    }
}
