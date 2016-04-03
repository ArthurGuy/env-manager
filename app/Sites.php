<?php

namespace App;

use Crypt;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * @property int    id
 * @property string name
 * @property string env
 * @property int    created_by
 * @property int    edited_by
 * @property int    viewed_by
 * @property Carbon viewed_at
 * @property Carbon edited_at
 * @property Carbon updated_at
 * @property Carbon created_at
 */
class Sites extends Model
{

    protected $dates = ['viewed_at', 'edited_at', 'accessed_at'];
    protected $with = ['edited_by', 'created_by', 'viewed_by'];
    protected $fillable = ['name', 'created_by', 'env'];

    public static function recordNew($name, $userId)
    {
        $encrypter = new KMSEncrypter();

        return self::create([
            'name'       => $name,
            'created_by' => $userId,
            'env'        => base64_encode($encrypter->encryptString('-')),
        ]);
    }

    /**
     * @param $name
     *
     * @return Sites
     */
    public static function findByName($name)
    {
        $site = self::where('name', $name)->first();
        if (!$site) {
            throw new ModelNotFoundException();
        }
        return $site;
    }

    public function created_by()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function edited_by()
    {
        return $this->belongsTo(User::class, 'edited_by');
    }

    public function viewed_by()
    {
        return $this->belongsTo(User::class, 'viewed_by');
    }

    public function recordViewedBy($id)
    {
        $this->viewed_by = $id;
        $this->viewed_at = Carbon::now();
        $this->save();
    }

    public function edit($name, $env, $updatedBy)
    {
        $encrypter = new KMSEncrypter();

        $this->name      = $name;
        $this->env       = base64_encode($encrypter->encryptString($env));
        $this->edited_by = $updatedBy;
        $this->edited_at = Carbon::now();
        $this->save();
    }

    public function getDecryptedEnvAttribute()
    {
        $encrypter = new KMSEncrypter();
        return $encrypter->decryptString(base64_decode($this->attributes['env']));
    }

}
