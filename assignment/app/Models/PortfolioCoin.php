<?php

namespace App\Models;

//use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PortfolioCoin extends Model
{
	
	public $timestamps = false;
	protected $fillable = ['coin_id', 'portfolio_id', 'amount'];
	
}
