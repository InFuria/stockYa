Vue.component('cart',{
    data(){return{
        show:show(),
		cart:cart(),
		targetDelivery:{},
		tempCant:null,
		proccess:null,
		color:color(),
		width:"256px",
	}},
	computed:{
		added(){
			return cart().added
		}
	},
	methods:{
		websale(companyId){
			modal("cart", false)
			if(!dataVue.client.validSale(companyId, this.targetDelivery[companyId]).length > 0){
				dataVue.client.proccessCompanyId = companyId
				modal("client", true)
			}else{
				dataVue.client.proccessCompanyId = null
				mensaje="pedidosgoya.com/?websale=123857"
				window.open(`https://api.whatsapp.com/send?text=${encodeURIComponent(mensaje)}&phone=54${dataVue.cart.companies[companyId].contact.whatsapp}`);
				//console.log("concretar venta ", dataVue.cart.companies[companyId].contact.whatsapp)
			}
		},
		sendMessagge(companyId){
			dataVue.client.proccessCompanyId = companyId
			//console.log("seller ", dataVue.cart.companies[companyId].contact.whatsapp)
			modal("cart" , false)
			modal("client" , true)
			//window.open(`https://api.whatsapp.com/send?phone=${seller.contact.whatsapp}&text=Nuevo Pedido`)
		},
		productCantTemporal(product , cant){
			cant = typeof cant == 'object' ? cant.target.value : ( cant == undefined ? product.cant : cant )
			cart().productCant(product , cant)
			this.target = Object.assign({} , cart())
		},
		productView(product){
			modal("cart" , false)
			this.$emit("productview", product)
		},
        hide() {
            modal("cart" , false)
        }
	},
    mounted(){
        if(this.$vuetify.breakpoint.xsOnly){
            this.width="100%"
		}
		if( Object.keys(this.cart.companies).length > 1){
			for(let companyName in this.cart.companies){
				this.targetDelivery[this.cart.companies[companyName].details.name] = false
			}
		}
	},
    template:`
    <v-navigation-drawer v-model="show.cart" app fixed right temporary clipped :width="width">
				<v-toolbar :color="color.primary" class="position-fixed">
					<v-divider style="opacity:0"></v-divider>
					<v-toolbar-title class="white--text">Carrito</v-toolbar-title>
					<v-btn icon dark @click="hide">
						<v-icon>mdi-close</v-icon>
					</v-btn>
					
				</v-toolbar>
				<div outlined v-for="(companyProductList, index) of added" v-bind:class="color.primary+' pa-1'">
					<h3 class="pa-2 white--text font-weight-bold text-center text-uppercase">{{cart.companies[index].details.name}}</h3>
					<div class="green white--text pa-2">
						Total: $ {{cart.companies[index].total}} 
						<v-btn class="ml-3" x-small @click="websale(index)">Pedir</v-btn>
					</div>
					<div class="pa-1 d-flex" v-if="cart.companies[index].details.delivery">
						<v-icon v-bind:class="[targetDelivery[index] ? 'white--text' : 'red' , 'mb-1']">mdi-truck</v-icon>
						<v-switch v-model="targetDelivery[index]" class="ma-2" color="grey-darken-3" label="Delivery" hide-details>
						</v-switch>
					</div>	
					<v-card
						class="mx-auto pl-3 mt-1"
						max-width="344"
						v-for="product in companyProductList" :key="product.id"
					>
						<v-list-item-subtitle class="pa-1 font-weight-bold text-center text-uppercase">{{product.name}}</v-list-item-subtitle>
						<v-divider></v-divider>
						<v-list-item three-line>
						<v-list-item-content>
							<div class="overline mb-4">Unidad:$ {{product.price}}</div>
							<v-list-item-title class="green--text mb-1">$ {{product.total}}</v-list-item-title>
							<div class="d-flex py-1">
								<v-text-field type="number" class="pa-0 ma-0" v-model="product.cant" @blur="productCantTemporal(product , $event)"></v-text-field>
								<v-btn fab x-small class="ml-2 "><v-icon small>mdi-check</v-icon></v-btn>
							</div>
						</v-list-item-content>					
						<v-list-item-avatar
							tile
							size="80"
							color="grey"
							@click="productView(product)"
							v-if="product.image.length"
						>
							<v-img loading="lazy" v-if="product.image[0].search('mp4') == -1" :src="product.image[0]"></v-img>
							<video loading="lazy" preload width="100%" height="100%" v-else :src="product.image[0]" controls></video>
						</v-list-item-avatar>
						</v-list-item>
					</v-card>
				</div>
				</v-row>
			</v-navigation-drawer>
    `
})