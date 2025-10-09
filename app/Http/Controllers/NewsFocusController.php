<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NewsFocus;
use App\Http\Requests\ListNewsFocusRequest;
use Carbon\Carbon;

class NewsFocusController extends Controller
{
    public function list(ListNewsFocusRequest $request)
    {
        $validated = $request->validated();
        $user = auth('api')->user();

        $queryBuilder = NewsFocus::query()
            ->where('user_id', $user->id)
            ->join('news', 'news_focus.id', '=', 'news.id')
            ->select(['news.id', 'news.category', 'news.gmdate', 'news.image', 'news.title', 'news.content', 'news.link'])
            ->when($request->filled(['start_date', 'end_date', 'timezone']), function ($query) use ($validated) {
                $startDate = Carbon::parse($validated['start_date'] . ' 00:00:00', $validated['timezone'])->utc();
                $endDate = Carbon::parse($validated['end_date'] . ' 23:59:59', $validated['timezone'])->utc();
                return $query->whereBetween('news.gmdate', [$startDate, $endDate]);
            })
            ->when($request->filled('category'), function ($query) use ($validated) {
                return $query->where('news.category', $validated['category']);
            })
            ->when($request->filled('keyword'), function ($query) use ($validated) {
                return $query->where(function ($query) use ($validated) {
                    $query->whereRaw('match(news.title, news.content) against (? IN BOOLEAN MODE)', [$validated['keyword']]);
                });
            });

        $paginator = $queryBuilder->orderBy('news.gmdate', 'DESC')
            ->paginate($validated['results'], ['*'], 'page', $validated['page']);

        return response()->json($paginator);
    }

    public function add(Request $request)
    {
        $validator = validator($request->all(), [
            'id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'validator' => $validator->errors()], 400);
        }

        $user = auth('api')->user();

        $focus = NewsFocus::firstOrCreate([
            'id' => $request->id,
            'user_id' => $user->id
        ]);

        if (!$focus->wasRecentlyCreated) {
            return response()->json(['status' => true, 'result' => '已經關注過了']);
        }

        return response()->json(['status' => true, 'result' => '關注成功'], 201);
    }

    public function remove(string $id)
    {
        $user = auth('api')->user();

        $deleted = NewsFocus::where([
            'id' => $id,
            'user_id' => $user->id
        ])->delete();

        if ($deleted) {
            return response()->json(['status' => true, 'result' => '取消關注成功']);
        }

        return response()->json(['status' => false, 'result' => '尚未關注此新聞或新聞不存在'], 404);
    }
}
