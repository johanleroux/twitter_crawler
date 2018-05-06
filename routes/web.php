<?php

use App\User;
use Illuminate\Support\Facades\Cache;

Route::get('/', function () {
    $info = Cache::remember('users', 0.25, function () {
        return Twitter::getAppRateLimit();
    });

    $leader = request()->user;
    $search = request()->search;
    if($leader)
        $leader = User::where('id', $leader)->firstOrFail();

    $users = User::orderBy('should_crawl', 'desc')
                    ->orderBy('updated_at', 'desc')
                    ->orderBy('crawled_at', 'desc')
                    ->when($leader, function ($query) use ($leader) {
                        $followers = DB::table('followers')
                                        ->where('leader_id', $leader->id)
                                        ->pluck('follower_id')
                                        ->toArray();

                        return $query->whereIn('id', $followers);
                    })
                    ->when(request()->search, function($query) {
                        return $query->where('description', 'LIKE', '%' . request()->search . '%');
                    })
                    ->paginate(250);

    return view('welcome', compact('leader', 'search', 'users', 'info'));
});

Route::post('/', function() {
    $post = request()->validate([
        'screen_name' => 'required'
    ]);

    $post = array_merge($post, ['should_crawl' => true]);

    $user = User::updateOrCreate([
        'screen_name' => $post['screen_name']
    ], $post);

    return redirect()->to('/');
});

Route::patch('/', function() {
    $crawl = array_keys(request()->crawl ?: []);
    
    User::where('crawled_at', null)
        ->where('should_crawl', true)
        ->update(['should_crawl' => false]);

    User::where('should_crawl', false)
        ->whereIn('id', $crawl)
        ->update(['should_crawl' => true]);

    return redirect()->back();
});