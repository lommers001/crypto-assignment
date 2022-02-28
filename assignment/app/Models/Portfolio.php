<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Portfolio extends Model
{
    use HasFactory;
	
	public $timestamps = false;
	protected $fillable = ['name', 'current_value', 'value_24h_ago'];
	
	public function coins(){
		//return $this->belongsToMany(Coin::class)->withPivot('portfolio_coins', 'coin_id', 'idname');
		
		return $this->join('portfolio_coins', 'portfolio_coins.portfolio_id', '=', 'portfolios.id')
            ->join('coins', 'portfolio_coins.coin_id', '=', 'coins.idname')
            ->select('portfolios.*', 'portfolio_coins.amount', 'coins.price', 'coins.name as coin_name')
            ->get();
		
	}
}
