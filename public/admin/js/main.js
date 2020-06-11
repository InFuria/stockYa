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
		tab: null,
		tabs:0,
		currentItem: 'tab-Web',
		sections: [],
	
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
				this.sections = ['Web', 'Comercios']
				API.categories( {
					company(){
						companies().getterInstance()
					}
				})
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
			}
		},
		mounted(){
			this.auth()
		}
	})
}
