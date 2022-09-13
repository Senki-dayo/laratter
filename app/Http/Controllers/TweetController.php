<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Models\Tweet;
use Auth;
use App\Models\User;

class TweetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $tweets = Tweet::getAllOrderByUpdated_at();
        return view('tweet.index',compact('tweets'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('tweet.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    // バリデーション
    $validator = Validator::make($request->all(), [
        'tweet' => 'required | max:191',
        'description' => 'required',
    ]);
    // バリデーション:エラー
    if ($validator->fails()) {
        return redirect()
            ->route('tweet.create')
            ->withInput()
            ->withErrors($validator);
    }

    // フォームから送信されてきたデータとユーザIDをマージし，DBにinsertする
    $data = $request->merge(['user_id' => Auth::user()->id])->all();
    $result = Tweet::create($data);

    // // create()は最初から用意されている関数
    // // 戻り値は挿入されたレコードの情報
    // $result = Tweet::create($request->all());

    // ルーティング「todo.index」にリクエスト送信（一覧ページに移動）
    return redirect()->route('tweet.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $tweet = Tweet::find($id);
        // ddd($tweet->all());
        return view('tweet.show', compact('tweet'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $tweet = Tweet::find($id);
        return view('tweet.edit', compact('tweet'));
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
    //バリデーション
    $validator = Validator::make($request->all(), [
        'tweet' => 'required | max:191',
        'description' => 'required',
    ]);
    //バリデーション:エラー
    if ($validator->fails()) {
        return redirect()
        ->route('tweet.edit', $id)
        ->withInput()
        ->withErrors($validator);
    }
    //データ更新処理
    $result = Tweet::find($id)->update($request->all());
    return redirect()->route('tweet.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $result = Tweet::find($id)->delete();
        return redirect()->route('tweet.index');
    }

    public function mydata()
    {
      // Userモデルに定義したリレーションを使用してデータを取得する．
      $tweets = User::query()
        ->find(Auth::user()->id)
        ->userTweets()
        ->orderBy('created_at','desc')
        ->get();
      return view('tweet.index', compact('tweets'));
    }
}
