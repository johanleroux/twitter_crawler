<?php

use App\User;
use Illuminate\Support\Facades\Cache;

Route::get('/', function () {
    $info = Cache::remember('users', 0.25, function () {
        return Twitter::getAppRateLimit();
    });

    $leader = request()->user;
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
                    ->paginate(250);

    return view('welcome', compact('leader', 'users', 'info'));
});

Route::post('/', function() {
    $post = request()->validate([
        'screen_name' => 'required'
    ]);

    $post = array_merge($post, ['should_crawl' => true]);

    $user = User::create($post);

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