var vm, products
var _private = new WeakMap();

var dataVue = new Object({
		token:null,
		alertAuth:"",
		defaultCompanyImage:"./public/images/default.jpg",
		color:{primary:"orange darken-4"},
		companies:new CompaniesList,
		categories:{product:[],company:[]},
		zones:[
			{ id: 1, name: "Norte" },
			{ id: 2, name: "Sur" },
			{ id: 3, name: "Este" },
			{ id: 4, name: "Oeste" },
			{ id: 5, name: "Noreste" },
			{ id: 6, name: "Noroeste" },
			{ id: 7, name: "Suroeste" },
			{ id: 8, name: "Sureste" },
			{ id: 9, name: "Centro" }
		],

		componentLoadingList:[],
		show:new ShowComponents,
		img:0,
		search:"",
		productView:{value:null},
		companyView:{name:"",address:"",score:0,delivery:0},
		productsList:[],
		socials: [],
		productos: [],
		tab: null,
		tabs:0,
		currentItem: 'tab-Web',
		sections: [],
		more: [
			'News', 'Maps', 'Books', 'Flights', 'Apps',
		],
		text: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.',
	
})

for(let key of ["show","companies","categories","color","zones","token"]){
	eval(`function ${key}(k , v){
		if(k == undefined){
			return dataVue.${key}
		};
		if(v == undefined){
			dataVue.${key}.value = v
		}else{
			dataVue.${key}[k] = v
		};
	}`)
}

function modal(k , v){
	dataVue.show.modal( k , v )
}

NavegationManager.go( "#home" );
window.onpopstate = NavegationManager.valid

vueLaunch()

function vueLaunch() {
	vm = new Vue({
		el: '#app',
		vuetify: new Vuetify,
		props: {
			source: String
		},
		data() { return dataVue },
		watch:{ 
			search(v){ this.searchProccess(v) },
		},
		computed: { },
		methods: {
			authValid(){
				this.sections = ['Web', 'Shopping', 'Videos', 'Images']
				this.categoriesGet()
			},
			auth(){
				API.getterPost('auth/login', {"username":"ely.admin" , "password":"undertale"})
				.then( async response => {
					this.token = await response.data
					this.authValid()
				}).catch(function (error) {
					this.alertAuth = "Error de autenticacion"
					console.log({error});
				});
			},
			categoriesGet(){
				API.getter('categories/products')
				.then((response)=>{
					for( let categories of response.data.data ){
						this.categories.product.push(categories)
					}
					this.companiesGet()
				})
				.catch(function (error) {
					console.log({error});
				})

				API.getter('categories/company')
				.then((response)=>{
					for( let categories of response.data.data ){
						this.categories.company.push(categories)
					}
				})
				.catch(function (error) {
					console.log({error});
				})
			},
			companiesGet(){
				API.getter('companies')
				.then((response)=>{
					for( let company of response.data.data ){
						company['ui'] = {view:false}
						company.zone = typeof company.zone == "string" ? 1 : company.zone
						company['category'] = Object.queryid(`${company.category_id}=name` , this.categories.company)
						this.companies.push(company)
					}
				})
				.catch(function (error) {
					console.log(error);
				})
			},
			///
			addItem (item) {
				const removed = this.items.splice(0, 1)
				this.items.push(
				  ...this.more.splice(this.more.indexOf(item), 1)
				)
				this.more.push(...removed)
				this.$nextTick(() => { this.currentItem = 'tab-' + item })
			},
			reset(){
				localStorage.clear()
			},
			setSearch(v){
				this.search = v
				modal("home",true)
			},
			setCompanyView(v){
				v["image"] = v["image"] == undefined ? [this.defaultCompanyImage] : v["image"]
				console.log({v})
				this.companyView = v
				modal("company",true)
			},
			setProductView(v){
				this.productView = v
				modal("product",true)
			},
			setCart(v){
				this.cart = v
			},
			async searchProccess(v){
				this.show.company = false
				this.show.gallery = false
				if(v == "ofertas"){					
					API.getter('products', {type:"promo"})
					.then((response)=>{
						this.gallery(response.data.data);
					})
					.catch(function (error) {
						console.log(error);
					})
				}else{
					if(v.search('vendedor')>-1){
						API.getter('products', {company_id:+v.split("@")[1]})
						.then((response)=>{
							let data = response.data
							data.company.image = [this.defaultCompanyImage]
							this.companyView = data.company
							this.show.company = true
							for(let product of data.products){
								product["company"] = data.company
							}
							this.gallery(data.products)
						})
						.catch(function (error) {
							console.log(error);
						})
					}
				}
			},
			gallery(productsList){
				this.productsList = []
				for(let product of productsList){
					product.price = product.price > 99999 ? 99999 : product.price
					product.image = product.image.search("jpg") == -1 ? ["./public/images/product-default.jpg"] : product.image.split(",")
					product["ui"] = new Object({ carousel:0, markColor:["blue",""],mark:"mdi-cart-plus",detailsShow:false})
					if(this.cart.isTargeted(product)){
						this.cart.markRender(product)
					}
					this.productsList.push(product)
				}
				this.cart.open(this.productsList)
				this.show.gallery = true
			}
		},
		mounted(){
			this.auth()
		}
	})
}
