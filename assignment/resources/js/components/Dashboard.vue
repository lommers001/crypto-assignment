<template>
    <div class="container">
		<div v-show="alertInfo.visible" :class="'p-fixed mt-2 alert alert-'+alertInfo.type" role="alert">{{ alertInfo.message }}</div>
        <div class="row justify-content-center pt-2">
            <div class="w-50 p-2">
                <section class="card" id="prizes-list-static">
                    <div class="card-header"><h4>Koersen</h4></div>

                    <div class="card-body">
						<div class="crypto-table d-flex flex-column w-100">
							<div class="row thead">
									<div class="col"></div>
									<div class="col">Naam</div>
									<div class="col">Koers</div>
									<div class="col">Market Cap</div>
							</div>
							<div class="row coin-row" v-for="(coin, index) in coins" :key="index" @click="addCoin(coin)">
								<div class="table-img col" :style="'background-image:url(https://assets.coingecko.com/coins/images/' + coin.image + ')'"></div>
								<div class="col">{{coin.name}}</div>
								<div class="col">€{{coin.price}}</div>
								<div class="col">€{{coin.marketcap}}</div>
							</div>
						</div>
                    </div>
                </section>
            </div>
			<div class="w-50 p-2">
                <section v-show="activeTab == 'home'" class="card">
                    <div class="card-header d-flex justify-content-between">
						<h4>Jouw portfolio's</h4>
						<button class="btn btn-success" @click="switchTab('create')">+ Nieuw</button>
					</div>

                    <div class="card-body">
						<div class="crypto-table d-flex flex-column w-100">
							<div class="row thead">
								<div class="col">Naam</div>
								<div class="col">Waarde</div>
								<div class="col">Acties</div>
							</div>
							<div class="row" v-for="(portfolio, index) in portfolios" :key="index">
								<div class="col">{{portfolio.name}}</div>
								<div class="col">€{{portfolio.current_value}}</div>
								<div class="col">
									<button class="btn btn-primary me-2" @click="switchTab('edit', index)">Wijzig</button>
									<button class="btn btn-danger" @click="saveDelete(index, portfolio)">Verwijder</button>
								</div>
							</div>
						</div>
                    </div>
                </section>
				<section v-show="activeTab == 'create'" class="card">
                    <div class="card-header d-flex justify-content-between">
						<h4>Nieuw portfolio</h4>
						<button class="btn btn-link" @click="switchTab('home')">Ga terug</button>
					</div>

                    <div class="card-body">
						<div class="crypto-table d-flex flex-column w-100">
							<label for="portCreateName" class="form-label">Naam portfolio</label>
							<input id="portCreateName" placeholder="Naam portfolio" v-model="createPortfolio.name" class="form-control w-100"/>
							<hr>
							<h5>Munten in portfolio</h5>
							<p>Voeg nieuwe munten toe door er één in het linkervak te klikken.</p>
							<div class="crypto-table d-flex flex-column w-100">
								<div class="row thead">
									<div class="col">Naam</div>
									<div class="col">Aantal</div>
									<div class="col">Acties</div>
								</div>
								<div class="row" v-for="(coin, index) in createPortfolio.coins" :key="index">
									<div class="col">{{coin.coin_name}}</div>
									<div class="col"><input :id="'ei-'+index" @input="validateNumber" class="form-control" type="number" v-model="createPortfolio.coins[index].amount" /></div>
									<div class="col">
										<button class="btn btn-danger" @click="removeCoin(index)">Verwijder</button>
									</div>
								</div>
							</div>
							<hr>
							<div class="d-flex justify-content-end">
								<button class="btn btn-primary" @click="saveCreate" :disabled="disableCreate">{{ displayButtonText }}</button>
							</div>
						</div>
                    </div>
                </section>
				<section v-show="activeTab == 'edit'" class="card">
                    <div class="card-header d-flex justify-content-between">
						<h4>Wijzig portfolio</h4>
						<button class="btn btn-link" @click="switchTab('home')">Ga terug</button>
					</div>

                    <div class="card-body">
						<div class="crypto-table d-flex flex-column w-100">
							<label for="portEditName" class="form-label">Naam portfolio</label>
							<input id="portEditName" placeholder="Naam portfolio" v-model="editPortfolio.name" class="form-control w-100"/>
							<hr>
							<h5>Munten in portfolio</h5>
							<p>Voeg nieuwe munten toe door er één in het linkervak te klikken.</p>
							<div class="crypto-table d-flex flex-column w-100">
								<div class="row thead">
									<div class="col">Naam</div>
									<div class="col">Aantal</div>
									<div class="col">Acties</div>
								</div>
								<div class="row" v-for="(coin, index) in editPortfolio.coins" :key="index">
									<div class="col">{{coin.coin_name}}</div>
									<div class="col"><input :id="'ei-'+index" @input="validateNumber" class="form-control" type="number" v-model="editPortfolio.coins[index].amount" /></div>
									<div class="col">
										<button class="btn btn-danger" @click="removeCoin(index)">Verwijder</button>
									</div>
								</div>
							</div>
							<hr>
							<div class="d-flex justify-content-end">
								<button class="btn btn-primary" @click="saveEdit" :disabled="disableEdit">{{ displayButtonText }}</button>
							</div>
						</div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</template>

<script>
export default {
	name: 'Dashboard',
	props: ['data'],
	data() {
		return {
			coins: this.data.coins,
			portfolios: this.data.portfolios,
			defaultPortfolio: {'id': 0, 'name': '', 'current_value': 0, 'coins': []},
			createPortfolio: {'id': 0, 'name': '', 'current_value': 0, 'coins': []},
			editPortfolio: {'id': 0, 'name': '', 'current_value': 0, 'coins': []},
			originalIndex: 0,
			activeTab: "home",
			axiosHeader: {},
			alertInfo: {"visible": false, "type": "error", "message": "Error"},
			isLoading: false,
			invalidFields: [],
			isInvalidInput: false
		}
	},
	computed: {
		disableCreate() { return this.createPortfolio.coins.length == 0 || this.createPortfolio.name == "" || this.isLoading || this.isInvalidInput },
		disableEdit() { return this.editPortfolio.coins.length == 0 || this.editPortfolio.name == "" || this.isLoading || this.isInvalidInput },
		displayButtonText() { return this.isLoading ? 'Even wachten...' : 'Opslaan' }
	},
	methods: {
		addCoin(coin) {
			if(this.activeTab == 'home') return;
			let portfolio = (this.activeTab == 'create' ? this.createPortfolio : this.editPortfolio);
			//Don't add the same coin a second time
			let coinAlreadyIncluded = false;
			portfolio.coins.forEach(el => {
				if(el.id == coin.id){
					coinAlreadyIncluded = true;
				}
			});
			if(!coinAlreadyIncluded){
				portfolio.coins.push({'id': coin.id, 'coin_name': coin.name, 'idname': coin.idname, 'amount': 1, 'price': coin.price});
			}
		},
		calculateValue(portfolio) {
			let value = 0;
			portfolio.coins.forEach(el => {value += (el.price * el.amount)});
			return value;
		},
		hardCopyObject(obj){
			return JSON.parse(JSON.stringify(obj));
		},
		removeCoin(index) {
			if(this.activeTab == 'home') return;
			let portfolio = (this.activeTab == 'create' ? this.createPortfolio : this.editPortfolio);
			portfolio.coins.splice(index, 1);
		},
		saveCreate() {
			var Dashboard = this;
			this.isLoading = true;
			this.createPortfolio.current_value = this.calculateValue(this.createPortfolio);
			axios.post("./create", this.createPortfolio, this.axiosHeader)
				.then(response => {
					Dashboard.createPortfolio.id = response.data.id;
					Dashboard.portfolios.push(this.hardCopyObject(this.createPortfolio));
					Dashboard.switchTab('home');
					Dashboard.showAlert('success', 'Portfolio aangemaakt');
					Dashboard.createPortfolio = this.hardCopyObject(this.defaultPortfolio);
					this.isLoading = false;
				})
				.catch(error => {
					Dashboard.showAlert('danger', 'Er is iets misgegaan: '+error.message, 8000);
					console.log(error);
					this.isLoading = false;
				});
		},
		saveDelete(index, portfolio) {
			if(confirm('Weet je zeker dat je portfolio "'+portfolio.name+'" wilt verwijderen?')){
				var Dashboard = this;
				axios.post("./delete", portfolio, this.axiosHeader)
					.then(response => {
						Dashboard.portfolios.splice(index, 1);
						Dashboard.showAlert('success', 'Portfolio verwijderd');
					})
					.catch(error => {
						Dashboard.showAlert('danger', 'Er is iets misgegaan: '+error.message, 8000);
						console.log(error);
						this.isLoading = false;
					});
			}
		},
		saveEdit() {
			var Dashboard = this;
			this.isLoading = true;
			this.editPortfolio.current_value = this.calculateValue(this.editPortfolio);
			axios.post("./edit", this.editPortfolio, this.axiosHeader)
				.then(response => {
					Dashboard.portfolios[Dashboard.originalIndex] = this.hardCopyObject(Dashboard.editPortfolio);
					Dashboard.switchTab('home');
					Dashboard.showAlert('success', 'Portfolio gewijzigd');
					Dashboard.editPortfolio = this.hardCopyObject(this.defaultPortfolio);
					this.isLoading = false;
				})
				.catch(error => {
					Dashboard.showAlert('danger', 'Er is iets misgegaan: '+error.message, 8000);
					console.log(error);
					this.isLoading = false;
				});
		},
		showAlert(type, message, duration = 4000) {
			this.alertInfo.visible = true;
			this.alertInfo.type = type;
			this.alertInfo.message = message;
			window.setTimeout(() => {this.alertInfo.visible = false}, duration);
		},
		switchTab(tab, currentPortfolioIndex = null) {
			this.activeTab = tab;
			if(this.activeTab == 'home'){
				document.getElementById("prizes-list-hover").id = "prizes-list-static";
			}
			else {
				document.getElementById("prizes-list-static").id = "prizes-list-hover";
			}
			if(currentPortfolioIndex != null){
				//Hard-copy portfolio
				this.editPortfolio = this.hardCopyObject(this.portfolios[currentPortfolioIndex]);
				this.originalIndex = currentPortfolioIndex;
			}
		},
		validateNumber(x){
		let el = x.target;
			let regex=/^[0-9]+$/;
			if (!el.value.match(regex) || el.value == 0 || el.value === '0')
			{
				if(!this.invalidFields.includes(el.id)){
					this.invalidFields.push(el.id);
					el.classList.add('is-invalid');
				}
			}
			else{
				for(var i = 0; i < this.invalidFields.length; i++){
					if(this.invalidFields[i] == el.id){
						this.invalidFields.splice(i, 1);
						el.classList.remove('is-invalid');
					}
				}
			}
			this.isInvalidInput = this.invalidFields.length > 0;
		}
	},
	mounted() {
		this.axiosHeader = { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') };
	}
}
</script>
