<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coin extends Model
{
    use HasFactory;
	
	public $timestamps = false;
	protected $fillable = ['name', 'idname', 'price', 'marketcap', 'image'];
	
	//public function portfolio_coins()
    //{
    //    return $this->hasMany(PortfolioCoin::class, 'coin_id', 'idname');
    //}
	
	public function portfolios()
    {
        return $this->belongsToMany(Post::class)->withPivot('portfolio_coins', 'portfolio_id', 'id');
    }
}
