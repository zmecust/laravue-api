<?php

namespace App\Http\Controllers;

use App\Article;
use Cache;
use Auth;
use Validator;
use App\Repositories\ArticleRepository;
use Illuminate\Http\Request;

class ArticlesController extends Controller
{
    protected $articleRepository;

    public function __construct(ArticleRepository $articleRepository)
    {
        $this->articleRepository = $articleRepository;
        $this->middleware('jwt.auth', [
            'only' => ['store', 'update', 'destroy']
        ]);
    }

    public function index(Request $request)
    {
        $page = 1;

        if ($request->input('page')) {
            $page = $request->input('page');
        }

        $articles = $this->articleRepository->getArticles($page, $request);

        if (! empty($articles)) {
            return $this->responseSuccess('OK', $articles);
        }

        return $this->responseError('查询失败');
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
        $validator = Validator::make($request->all(), [
            'title' => 'required|between:4,100',
            'body' => 'required|min:10',
            'tag' => 'required',
            'is_hidden' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->responseError('表单验证失败', $validator->errors()->toArray());
        }

        $tags = $this->articleRepository->normalizeTopics($request->get('tag'));
        $data = [
            'title' => $request->get('title'),
            'body' => $request->get('body'),
            'user_id' => Auth::id(),
            'is_hidden' => $request->get('is_hidden'),
        ];
        $article = $this->articleRepository->create($data);
        Auth::user()->increment('articles_count');
        $article->tags()->attach($tags);
        Cache::tags('articles')->flush();
        return $this->responseSuccess('OK', $article);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $article = $this->articleRepository->getArticle($id);

        if (! empty($article)) {
            return $this->responseSuccess('OK', $article->toArray());
        }

        return $this->responseError('查询失败');
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
        $validator = Validator::make($request->all(), [
            'title' => 'required|between:4,100',
            'body' => 'required|min:10',
            'tag' => 'required',
            'is_hidden' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->responseError('表单验证失败', $validator->errors()->toArray());
        }

        $tags = $this->articleRepository->normalizeTopics($request->get('tag'));
        $data = [
            'title' => $request->get('title'),
            'body' => $request->get('body'),
            'is_hidden' => $request->get('is_hidden'),
        ];
        $article = $this->articleRepository->byId($id);
        $article->update($data);
        $article->tags()->sync($tags);
        Cache::tags('articles')->flush();
        return $this->responseSuccess('OK', $article);
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

    public function hotArticles()
    {
        if (empty($hotArticles = Cache::get('hotArticles_cache'))) {
            $hotArticles = Article::where([])->orderBy('comments_count', 'desc')->take(10)->get();
            Cache::put('hotArticles_cache', $hotArticles, 10);
        }
        return $this->responseSuccess('查询成功', $hotArticles);
    }

    function contentImage(Request $request)
    {
        return $this->responseSuccess('查询成功', $request->all());
        $file = $request->file('image');
        $filename = md5(time()) . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('articleImage'), $filename);
        $article_image = env('APP_URL') . '/articleImage/'.$filename;
        return $this->responseSuccess('查询成功', ['url' => $article_image]);
    }

}