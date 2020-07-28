var vm, products
var _private = new WeakMap();

var dataVue = new Object({
		loadingRequest:null,
		loadingRequestCount:1,
		mousePosition:"",
		defaultCompanyImage:"./public/images/default.jpg",
		color:{primary:"orange darken-4" , primaryContrast:"white"},
		componentLoadingList:[],
		show:new ShowComponents,
		showCategories:false,
		img:0,
		search:"",
		cart:new CartProducts,
		productView:{value:null},
		companyView:{name:"",address:"",score:0,delivery:0},
		companiesList: new CompaniesList,
		productsList:[],
		client:new Client,
		categories:new Categories,
		categoriesProducts:[],
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
		galleryItems: []
})

let globalAccess = [
	"show","productView","companyView",
	"color","sectors","socials","categories",
	"cart","client","products","productsList","companiesList"]
for(let key of globalAccess){
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
			search(v){ 
				if(v.length > 3){
					this.searchProccess(v)
				}
			},
		},
		computed: { 
			mousePositionStyle(){
				return this.mousePosition
			},
			productsListCategory(){
				let mark = []
				let res = []
				for(let prod of this.productsList){
					if(mark.indexOf(prod.category_id) == -1){
						mark.push(prod.category_id)
						for(let category of this.categoriesProducts){
							if(category.id == prod.category_id){
								res.push(category)
							}
						}
					}
				}
				return res
			},
			categoriesProductsFilter(){
				if(this.search == ""){
					return []
				}
				return this.categoriesProducts.filter( item => {
					let name =  item.name.toLowerCase()
					return ( name.search( this.search ) > -1 )
				})
			}
		},
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
				v = v.toLowerCase()
				if(v == "ofertas"){
					API.getter('products', {type:"ofertas"})
					.then((response)=>{
						this.gallery(response.data.data);
					})
					.catch(function (error) {
						console.log(error);
					})
					this.showCategories = false
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
							let res = []
							for(let a of data.products.data){
								if(a.type=="Combo"){
									res.push(a)
								}
							}
							for(let a of data.products.data){
								if(res.indexOf(a) == -1){
									res.push(a)
								}
							}
							this.gallery( res )
						})
						.catch(function (error) {
							console.log(error);
						})
					}else{
						this.productsList=[]
						products().api('find',{find:v})
						.then( res => {
							if(res.data.data != undefined){
								if(res.data.data.products != undefined){
									this.gallery(res.data.data.products)
								}
							}
							this.showCategories=false
						})
					}
				}
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
				this.showCategories = false
			},
			image(img){
				return API.route('file','open',img).url
			},
			viewCategories(v){
				console.log('viewCategories ' , v)
			}
		},
		
		mounted(){
			window.onload = ()=>{
				this.categories.getAll( true )
				document.getElementById("loading").style="display:none"
				document.getElementById("app").style="display:block"
				document.getElementById("loading").classList.add("off")
				setTimeout( ()=> {
					this.companiesList.renderSectors()
					this.search = serverData() == null ? "ofertas" : serverData().search
					this.footerViewer = false
					setTimeout( () => {
						this.showCategories = false
						this.viewCategories()
					},100);
				}, 1000)
				window.onmousemove = (event)=>{
					this.mousePosition = `left:${event.clientX}px;top:${event.clientY}px;`
				}
			}
		}
	})
}