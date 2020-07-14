var vm, products
var _private = new WeakMap();

class Entity extends APIHelper{
    constructor(name , attrValid){
        super(name , attrValid)
    }
    normalize(list){
        return list
    }
    valid(v){
    	return v
    }
}

var dataVue = new Object({
		token:null,
		alertAuth:"",
		defaultCompanyImage:"./public/images/default.jpg",
		color:{primary:"orange darken-4"},
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
		messages: [],	
})

for(let key of [
	"show","color","zones","token"]){
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
		computed: { },
		methods: {
			authValid(){
				API.getter( 'nawebsales' )
				.then( async res => {
					this.messages = await res.data.data.data
					console.log({messages:this.messages})
				})
				.then( r => {
					console.log( {r} )
				})
			},
			auth(){
				API.getterPost('auth/login', {"username":"ely.admin" , "password":"undertale"})
				.then( async response => {
					let res = await response.data
					dataVue.token = res.token_type+' '+res.token
					this.authValid()
				}).catch(function (error) {
					this.alertAuth = "Error de autenticacion"
					console.log({error});
				});
			},
			confirm(order){
				API.getter( 'nawebsales/'+order+"/sendTicket" )
				.then( async res => {
					let messages = await res.data
					console.log({messages})
				})
				.then( r => {
					console.log( {r} )
				})
			}
		},
		mounted(){
			this.auth()
		}
	})
}
