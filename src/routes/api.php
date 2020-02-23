<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

// 会員登録
Route::post('/register', 'Auth\RegisterController@register');

// ログイン
Route::post('/login', 'Auth\LoginController@login');

// ログアウト
Route::post('/logout', 'Auth\LoginController@logout');

// 記事投稿
Route::post('/post', 'Api\PostController@post');

// 記事編集
Route::post('/edit', 'Api\PostController@edit');

// 記事お気に入り
Route::post('/fav', 'Api\FavoriteController@fav');

// フォロウィング
Route::post('/following', 'Api\FollowingController@following');

// ユーザ
Route::get('/user/{id}', 'Api\UserController@getUser');

// 認証チェック
Route::get('/auth_check', 'Api\UserController@checkAuth');

// タイムライン取得
Route::get('/timeline', 'Api\UserController@getTimeline');

// ポスト詳細
Route::get('/post/{id}', 'Api\PostController@getPost');

// お気に入りされてるか
Route::get('/post/fav/{id}', 'Api\FavoriteController@isFavorite');

// プロフィール
Route::post('/profile/{id}', 'Api\UserController@profile');

// フォロイー
Route::get('/followee/{id}', 'Api\FollowingController@getFollowees');

// フォロワー
Route::get('/follower/{id}', 'Api\FollowingController@getFollowers');

// フォローしているか
Route::get('/following/{id}', 'Api\FollowingController@isFollowing');

// お気に入り一覧
Route::get('/favorite/{id}', 'Api\UserController@getFavoritePosts');

// パスワード変更
Route::post('/password/change/', 'Api\UserController@changePassword');
