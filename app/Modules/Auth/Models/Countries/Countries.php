<?php

namespace App\Modules\Auth\Models\Countries;
use App\Bll\Utility;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Countries extends Model
{
	protected $table = 'countries';
	protected $guarded = [];
	public $timestamps = true;


    public function Data(): HasMany
    {
        return $this->hasMany(CountriesData::class, 'country_id', 'id')->where('lang_id', Utility::lang_id());
    }




}
