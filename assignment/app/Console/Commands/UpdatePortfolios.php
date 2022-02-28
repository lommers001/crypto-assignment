<?php

namespace App\Console\Commands;

use App\Models\Coin;
use App\Models\Portfolio;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class UpdatePortfolios extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crypto:portfolio';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send e-mail if its value has significantly increased or decreased within 24 hours';

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
		//Get all portfolio data
		$result = Portfolio::get();
		foreach($result as $portfolio){
			//Check if the value of the portfolio has significantly changed (+/- 5%)
			if($portfolio->value_24h_ago != 0){
				$change = $portfolio->current_value / $portfolio->value_24h_ago;
				if($change > 1.05 || $change < 0.95){
					//Send E-mail if there is a significant change in value
					$this->sendMail($portfolio->current_value, $change);
					$this->info('E-mail sent');
				}
			}
			//Update the value to compare to 24 hours into the future
			$portfolio->value_24h_ago = $portfolio->current_value;
			$portfolio->save();
		}
		
		
        return 0;
    }
	
	private function sendMail($new_value, $percentage){
		$mail_body = "Actuele waarde: " $new_value . "; Percentage verandering (t.o.v. 24 uur geleden): " . $percentage;
		Mail::raw($mail_body, function ($mail) {
			$mail->to('sean@ca.netvibes.nl')
				->subject('Waarde portfolio sterk gewijzigd');
		});
	}
}
