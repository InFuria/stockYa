Vue.component("products", {
    data() {
        return {
            products: products(),
            categories: categories(),
            edit: false, mod: false,
            editProduct: null, modProduct: false,
            itemDefault:{
                name: '',
                description: '',
                type: '',
                price: 0.0,
                category_id: 1,
                company_id: 1,
                status: 1,
                image: []
            },
            productTarget:{},
            types:["-","Normal","Combo","Oferta","Descuentos","Usado"],
            modNew:false,
            view:true
        }
    },
    computed: {
        categoriesProduct() {
            let res = []
            for (let category of this.categories.product) {
                res.push(category.name)
            }
            return res
        },
    },
    methods: {
        toggle(product) {
            this.edit = false
            if(product != undefined){
                if(product != null ){
                    this.productTarget = product
                    this.edit = true
                }
            }else{
                console.log("new")
                this.edit = false
                this.productTarget = Object.assign({} , this.itemDefault)
            }
            this.modNew = true
        },
        image(imageSrc){
            let image = typeof imageSrc == 'object' ? imageSrc.id : imageSrc 
            if(Number.isNaN(parseInt(image))){
              return image
            }
            return API.route('file','open',{id:image}).url
        },
        categoryId(name){
            for (let category of this.categories.product) {
                if(category.name == name){
                    return category.id
                }
            }
        },
        update() {
            if(this.edit == true){
                this.products.replace(this.productTarget)
                .then( res => {
                    console.log('replace', {res})
                })
                .catch(error => {
                    console.log({error})
                })
            }
        },
        remove(product){
            this.products.remove(product)
            .then( res => {
                this.reView()
            })
            .catch( error => {
                console.log( {error} )
            })
        },
        create(){
            this.products.create(this.productTarget)
            .then( response => { 
                console.log({response})
                this.modNew = false
                this.edit = false
                this.products.push(response.data.product)
                this.productTarget = Object.assign({} , this.itemDefault)
                this.reView()
            })
            .catch( error => {
                console.log({error})
            })
        },
        reView(){
            this.view=false
            setTimeout( ()=> {
                this.view = true
            } , 100)
        }
    },
    mounted() {
        this.categories = categories()
        this.productTarget = Object.assign({} , this.itemDefault)
    },
    template: `
    <v-row class="pa-2" v-if="view">
        <v-col cols="12" xs="6" sm="6" md="3" lg="2" class="mt-3">
            <span 
                @click="toggle"
                class="mx-2 pa-3 elevation-2 d-flex justify-center align-center"
                style="font-size:8rem;"
            >+</span>
        </v-col>
        <v-col 
            v-for="product of products.list"
            cols="12" xs="6" sm="6" md="3" lg="2"
            :key="product.id"
        >
            <v-card
                class="mx-auto"
            >
                <v-img
                    :src="image(product.image[0])"
                    height="240px" dark
                >
                    <div 
                        class="d-flex flex-column justify-space-between"
                        style="position: relative;height: 100%;"
                    >

                        <v-card-title class="pa-1" style="background-color:rgba(0,0,0,.6)">
                            <v-btn dark icon @click="remove(product)">
                                <v-icon>mdi-close</v-icon>
                            </v-btn>

                            <v-spacer></v-spacer>

                            <v-btn dark icon class="mr-4" @click="toggle(product)">
                                <v-icon>mdi-pencil</v-icon>
                            </v-btn>
                        </v-card-title>

                        <v-spacer></v-spacer>

                        <div class="white--text" style="background-color:rgba(0,0,0,.6)">
                            <div class="py-0 px-1 d-flex align-center justify-center" style="min-height:5rem;width:100%;word-break: break-word;text-align:center">
                                {{ product.name }}
                            </div>
                        </div>
                    </div>
                </v-img>
                
            </v-card>
        </v-col>
        <v-dialog v-model="modNew" fullscreen hide-overlay transition="dialog-bottom-transition">
            <v-card>
                <v-toolbar dark color="primary">
                    <v-btn icon dark @click="modNew=false">
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                    <v-spacer></v-spacer>
                    <span v-if="!edit" class="white--text mr-3">Crear</span>
                    <v-btn v-if="!edit" @click="create" class="white blue--text" small fab><v-icon>mdi-check</v-icon></v-btn>
                </v-toolbar>
                <v-list two-line>
                    <v-list-item>
                        <v-list-item-icon>
                            <v-icon color="indigo">mdi-pencil</v-icon>
                        </v-list-item-icon>

                        <v-list-item-content>
                            <v-list-item-title>
                                <v-text-field
                                    @change="update"
                                    label="Nombre"
                                    v-model="productTarget.name"
                                ></v-text-field>
                            </v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                    <v-list-item>
                        <v-list-item-icon>
                            <v-icon color="indigo">mdi-cash</v-icon>
                        </v-list-item-icon>

                        <v-list-item-content>
                            <v-list-item-title>
                                <v-text-field
                                    @change="update"
                                    label="Precio"
                                    type="number"
                                    v-model="productTarget.price"
                                ></v-text-field>
                            </v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>

                    <v-divider inset></v-divider>

                    <v-list-item>
                        <v-list-item-icon>
                            <v-icon color="indigo">mdi-tag</v-icon>
                        </v-list-item-icon>

                        <v-list-item-content>
                            <v-list-item-title >
                                <v-select
                                    @change="update"
                                    label="Tipo"
                                    :items="types"
                                    v-model="productTarget.type"
                                ></v-select>
                            </v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                    <v-divider inset>
                    </v-divider>
                    <v-list-item>
                        <v-list-item-icon>
                            <v-icon color="indigo">mdi-tag</v-icon>
                        </v-list-item-icon>

                        <v-list-item-content>
                            <v-list-item-title >
                                <v-select
                                    @change="update"
                                    label="Categoria"
                                    :items="categoriesProduct"
                                    v-model="productTarget.category"
                                ></v-select>
                            </v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                    <v-divider inset>
                    </v-divider>

                    <v-list-item>
                        <v-list-item-icon>
                            <v-icon color="indigo">mdi-details</v-icon>
                        </v-list-item-icon>
                        <v-list-item-content>
                            <v-list-item-title >
                                <v-textarea
                                    @change="update"
                                    label="Descripción"
                                    v-model="productTarget.description"
                                ></v-textarea>
                            </v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>

                </v-list>
                <v-divider></v-divider>
                <div class="pa-5 mx-5" v-if="edit"><image-upload @update="update" :images="productTarget"></image-upload></div>
                <v-btn v-if="!edit" @click="create" class="blue white--text ma-5" >Crear <v-icon>mdi-check</v-icon></v-btn>
            </v-card>
        </v-dialog>
        <next :entity="'companies'" :query="{company_id:products.company_id}"></next>
    </v-row>
    `
})