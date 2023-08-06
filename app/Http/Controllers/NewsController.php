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
            'start_date' => 'required|string|date_format:Y-m-d|before_or_equal:end_date',
            'end_date' => 'required|string|date_format:Y-m-d|after_or_equal:start_date',
            'category' => 'string|max:10',
            'keyword' => 'string|max:100|nullable',
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => false,
                'validator' => $validator->errors() 
            ], 400);
        }

        $start_date = new \DateTime("$request->start_date 00:00:00");
        $end_date = new \DateTime("$request->end_date 23:59:59");    

        $result = News::whereBetween('gmdate', [$start_date, $end_date])
            ->when(isset($request->category), function ($query) use ($request) {
                return $query->where('category', '=', $request->category);
            })
            ->when(isset($request->keyword), function ($query) use ($request) {
                return $query->where(function ($query) use ($request) {
                    $query->where('title', 'LIKE', "%$request->keyword%")
                            ->orWhere('content', 'LIKE', "%$request->keyword%");
                });
            })
            ->orderBy('gmdate', 'DESC')
            ->get();        

        return response()->json($result);
    }
}
