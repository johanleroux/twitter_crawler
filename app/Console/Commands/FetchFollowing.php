<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Thujohn\Twitter\Facades\Twitter;
use App\User;

class FetchFollowing extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'twitter:fetchFollowing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch all users a specific user is following';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $users = User::where('should_crawl', true)
                    ->where('crawled_at', null)
                    ->get();

        $users->each(function($user) {

            $tmp = Twitter::getUsersLookup(['screen_name' => $user->screen_name])[0];

            $user = User::updateOrCreate([
                'screen_name' => $tmp->screen_name,
            ], [
                'twitter_id'  => $tmp->id,
                'name'        => $tmp->name,
                'description' => $tmp->description,
                'location'    => $tmp->location,
            ]);

            // Get Followings
            $userFollowings = [];
            $nextCursor = -1;

            do {
                $jsonData = Twitter::getFriends([
                        'screen_name'           => $tmp->screen_name,
                        'skip_status'           => false,
                        'include_user_entities' => false,
                        'cursor'                => $nextCursor
                    ]);
                
                $nextCursor = $jsonData->next_cursor;

                $userFollowings = array_merge(
                    $userFollowings, 
                    $jsonData->users
                );

            } while ($nextCursor != 0);
            
            // Add Followings
            foreach($userFollowings as $tmp)
            {
                $userFollowing = User::updateOrCreate([
                    'twitter_id' => $tmp->id
                ], [
                    'twitter_id'  => $tmp->id,
                    'name'        => $tmp->name,
                    'screen_name' => $tmp->screen_name,
                    'description' => $tmp->description,
                    'location'    => $tmp->location,
                ]);

                $userFollowing->follow($user->id);
            }

            $user->update(['crawled_at' => now()]);
        });
    }
}