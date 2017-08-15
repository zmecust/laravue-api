<?php

namespace App\Http\Controllers;

use Cache;
use App\Tag;
use DB;

class TagsController extends Controller
{
    public function index()
    {
        if (empty($tags = Cache::get('Tags_cache'))) {
            $tags = DB::table('tags')->select('id', 'name')->get();
            Cache::put('Tags_cache', $tags, 10);
        }
        return $this->responseSuccess('查询成功', $tags);
    }

    public function hotTags()
    {
        if (empty($hotTags = Cache::get('hotTags_cache'))) {
            $hotTags = Tag::where([])->orderBy('articles_count', 'desc')->take(20)->get();
            Cache::put('hotTags_cache', $hotTags, 10);
        }
        return $this->responseSuccess('查询成功', $hotTags);
    }

    /*public function index(Request $request)
    {
        $service_name = $request->get('service_name');
        $name = $request->get('name');

        $admins = Admin::where('name', 'like', "%$name%")->with(['services' => function ($query) {
            $query->select('name');
        }])->whereHas('services', function ($query) use ($service_name) {
            $query->where('name', 'like', "%$service_name%");
        })->get()->toArray();

        $admins = collect($this->adminTransformer->transformCollection($admins));
        $paged = $this->paginate($admins, $request);

        return $this->responseSuccess('查询成功', $paged);
    }

    // 手动分页
    private function paginate($admins, $request)
    {
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $per_page = $request->get('per_page') ? $request->get('per_page') : 15;
        $currentPageSearchResults = $admins->slice(($currentPage-1) * $per_page, $per_page)->all();
        $paged = new LengthAwarePaginator($currentPageSearchResults, count($admins), $per_page);

        return $paged->setPath(env('APP_URL') . 'admin/admins');
    }*/
}