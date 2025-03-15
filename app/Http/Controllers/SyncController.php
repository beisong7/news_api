<?php

namespace App\Http\Controllers;

use App\Services\NewsSyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SyncController extends Controller
{

    protected $syncService;

    public function __construct(NewsSyncService $syncService){
        $this->syncService = $syncService;
    }

    public function syncKey(){
        return $this->successResponse(
            [
                "key"=>Str::random(36),
                "instruction"=>"Add this key to the 'SYNC_KEY' value of your .env file"
            ],
            "key generated"
        );
    }

    public function syncNews(){
        $this->syncService->syncAllSources();
        return $this->successResponse(["news"=>"synced"], "successful");
    }
}
