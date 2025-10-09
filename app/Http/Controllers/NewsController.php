<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\News;
use App\Http\Requests\ListNewsRequest;
use Carbon\Carbon;

class NewsController extends Controller
{
    public function list(ListNewsRequest $request)
    {
        $validated = $request->validated();
        $user = auth('api')->user();

        $queryBuilder = News::query()
            ->select(['news.id', 'news.category', 'news.gmdate', 'news.image', 'news.title', 'news.content', 'news.link'])
            ->when($user, function ($query) use ($user) {
                // 如果使用者登入，就 leftJoin news_focus 來取得關注狀態
                // 並且只 join 屬於該 user 的紀錄
                $query->addSelect('news_focus.user_id')
                      ->leftJoin('news_focus', function ($join) use ($user) {
                          $join->on('news.id', '=', 'news_focus.id')
                               ->where('news_focus.user_id', '=', $user->id);
                      });
            })
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
}
