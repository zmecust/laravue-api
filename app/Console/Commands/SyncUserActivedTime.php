<?php

namespace App\Console\Commands;

use App\User;
use Cache;
use Illuminate\Console\Command;

class SyncUserActivedTime extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laravue:sync-user-actived-time';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        // 注意这里获取的 Redis key 为 actived_time_for_update
        // 获取完以后立马删除，这样就只更新需要更新的用户数据,而不是全部用户
        $data = Cache::pull('actived_time_for_update');
        if (!$data) {
            $this->error('Error: No Data!');
            return false;
        }

        foreach ($data as $user_id => $last_actived_at) {
            User::query()->where('id', $user_id)
                ->update(['last_actived_at' => $last_actived_at]);
        }
        $this->info('Done!');
    }
}
