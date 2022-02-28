<?php

namespace App\Console\Commands;

use App\Models\Coin;
use App\Models\Portfolio;
use App\Models\PortfolioCoin;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class GetCryptoCurrencyPrices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crypto:prices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get cryptocurrency prices for top 10 crypto currencies by market cap';

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
     * @return int
     */
    public function handle()
    {
		//Make HTTP Request
		$uri = config('endpoints.base_uri') . 'coins/markets';
		$response = Http::get($uri, [
			'vs_currency' => 'eur',
			'per_page' => 10,
			'page' => 1,
		]);
		
		//Catch errors
		if(!$response->successful()){
			$this->info('Error. HTTP Status: ' . $response->status());
			return 0;
		}
		
		//Send data to DB
		$result = $response->json();
		foreach($result as $coin){
			$image_uri_shortened = preg_replace("/^http[a-z:\/\.]+/", "", $coin['image']);
			Coin::updateOrInsert(
				['idname' => $coin['id']],
				['name' => $coin['name'], 'image' => $image_uri_shortened, 'price' => $coin['current_price'], 'marketcap' => $coin['market_cap']]
			);
		}
		
		//Update the value of portfolios
		$portfolios = Portfolio::all();
		foreach($portfolios as $portfolio){
			$coins = $portfolio->coins();
			$total_value = 0;
			foreach($coins as $coin){
				$total_value += ($coin->price * $coin->amount);
			}
			$portfolio->current_value = $total_value;
			$portfolio->save();
		}
        return 0;
    }
}
