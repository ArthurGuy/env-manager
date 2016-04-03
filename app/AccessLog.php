<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class AccessLog extends Model
{
    public $timestamps = false;
    protected $dates = ['created_at'];

    public static function recordAccess($siteId, $action, $userId = null, $ipAddress = null)
    {
        $log = new self();

        $log->site_id    = $siteId;
        $log->action     = $action;
        $log->user_id    = $userId;
        $log->ip_address = $ipAddress;
        $log->created_at = Carbon::now();

        $log->save();
    }

    public static function siteHistory($siteId)
    {
        return self::where('site_id', $siteId)->orderBy('created_at', 'desc')->take(20)->get();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
