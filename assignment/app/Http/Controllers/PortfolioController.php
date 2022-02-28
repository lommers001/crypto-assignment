<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Coin;
use App\Models\Portfolio;
use App\Models\PortfolioCoin;

class PortfolioController extends Controller
{

    public function index()
    {
		$coins = Coin::orderBy('marketcap', 'desc')->limit(10)->get();
		$portfolios = [];
		$portfolio_data = Portfolio::all();
		foreach($portfolio_data as $portfolio){
			$portfolio_coins = [];
			$new_portfolio = ["id" => $portfolio->id, "name" => $portfolio->name, "current_value" => $portfolio->current_value];
			$new_portfolio['coins'] = $portfolio->coins();
			array_push($portfolios, $new_portfolio);
		}
		$data = ["coins" => $coins, "portfolios" => $portfolios];
        return view('portfolio.index')->with(compact('data'));
    }
	
	public function create(Request $request)
    {
		//Create new portfolio object
		$portfolio = new Portfolio();
		$portfolio->name = $request->name;
		$portfolio->save();
		$total_value = 0;
		//Add coins to portfolio
		foreach($request->coins as $coin){
			$portfolio_coin = new PortfolioCoin();
			$portfolio_coin->coin_id = $coin['idname'];
			$portfolio_coin->portfolio_id = $portfolio->id;
			$portfolio_coin->amount = $coin['amount'];
			$portfolio_coin->save();
			$total_value += ($coin['price'] * $coin['amount']);
		}
		//Determine new value
		$portfolio->current_value = $total_value;
		$portfolio->save();
        return ["id" => $portfolio->id, "result" => 'success'];
    }
	
	public function edit(Request $request)
    {
		//Edit portfolio
		$portfolio = Portfolio::where('id', $request->id)->first();
		$portfolio->name = $request->name;
		$portfolio->save();
		$total_value = 0;
		//Delete old coins associated with the portfolio
		PortfolioCoin::where('portfolio_id', $request->id)->delete();
		//Add coins to portfolio
		foreach($request->coins as $coin){
			$coin_obj = $this->ObjectToJSON($coin);
			$portfolio_coin = new PortfolioCoin();
			$portfolio_coin->coin_id = $coin['idname'];
			$portfolio_coin->portfolio_id = $portfolio->id;
			$portfolio_coin->amount = $coin['amount'];
			$portfolio_coin->save();
			$total_value += ($coin['price'] * $coin['amount']);
		}
		//Determine new value
		$portfolio->current_value = $total_value;
		$portfolio->save();
        return ["result" => 'success'];
    }
	
	public function delete(Request $request)
    {
		//Delete portfolio
		PortfolioCoin::where('portfolio_id', $request->id)->delete();
		Portfolio::where('id', $request->id)->delete();
        return ["result" => 'success'];
    }
	
}
