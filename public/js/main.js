var vm, products
var _private = new WeakMap();

var dataVue = new Object({
		defaultCompanyImage:"./public/images/default.jpg",
		color:{primary:"orange darken-4"},
		componentLoadingList:[],
		show:new ShowComponents,
		img:0,
		search:"",
		cart:new CartProducts,
		productView:{value:null},
		companyView:{name:"",address:"",score:0,delivery:0},
		productsList:[],
		client:new Client,
		categories:["","Comidas","Indumentaria","Postres","Regaleria","Herramientas","Recambios"],
		sectors:[],
		socials: [
			{
			  icon: 'mdi-facebook',
			  color: 'indigo',
			},
			{
			  icon: 'mdi-whatsapp',
			  color: 'green darken-1',
			},
			{
			  icon: 'mdi-instagram',
			  color: 'red lighten-3',
			},
		  ],
		products:new Products,
		tab: null,
		tabs:0,
		items: [
			{ tab: 'One', content: 'Tab 1 Content' },
			{ tab: 'Two', content: 'Tab 2 Content' },
		],
		target:[],
		targetDelivery:false,
		footerViewer:true,
		galleryItems: [],
		productosList:[]
})

for(let key of ["socials","show","productView","companyView","color","cart","products","productsList","sectors"]){
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
			reset(){
				localStorage.clear()
			},
			setSearch(v){
				this.search = v
				modal("home",true)
			},
			setCompanyView(v){
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
						products().fromCompany(v)
						.then((response)=>{
							let data = response.data
							this.companyView = data.company
							this.show.company = true
							for(let product of data.products.data){
								product["company"] = data.company
							}
							this.gallery(data.products.data)
						})
						.catch(function (error) {
							console.log(error);
						})
					}
				}
			},
			companies(){
				API.getter('companies')
				.then((response)=>{
					this.productsList=[]
					let sectors = {}
					for( let company of response.data.data ){
						let category = this.categories[company.category_id]
						if(sectors[category] == undefined){
							sectors[category] = {name:category,shops:[]}
						}
						sectors[category].shops.push(company)
					}
					for(let sectorKey in sectors){
						this.sectors.push(sectors[sectorKey])
					}
				})
				.catch(function (error) {
					console.log(error);
				})
			},
			gallery(productsList){
				this.productsList = []
				for(let product of productsList){
					product.price = product.price > 99999 ? 99999 : product.price
					product.image = product.image.length == 0 ? ["./public/images/product-default.jpg"] : product.image
					product["ui"] = new Object({ carousel:0, markColor:["blue",""],mark:"mdi-cart-plus",detailsShow:false})
					if(this.cart.isTargeted(product)){
						this.cart.markRender(product)
					}
					this.productsList.push(product)
				}
				this.cart.open(this.productsList)
				this.show.gallery = true
			},
			image(img){
				console.log({img})
				return API.route('file','open',img).url
			},
		},
		mounted(){
			document.getElementById("loading").classList.add("off")
			this.companies()
			setTimeout( ()=> {
				this.search = "ofertas"
				this.footerViewer = false
			 }, 1000)
		}
	})
}
