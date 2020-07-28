Vue.component("companies", {
    data() {
        return {
            companies: companies(),
            categories: categories(),
            edit: null, mod: false,
            editProduct: null, modProduct: false,
            zones: zones(),
            view:true,
            itemDefault:{
                "name":"","address":"","email":"",
                "phone":"","whatsapp":"","social":"-",
                "city_id":1,"delivery":0,
                "zone":{},
                "attention_hours":"-",
                "category_id":1,"category":"",image:[]
            },
            itemNew:{},
            modNew:false,
            imagesCompany:[],
            imagesShow:false,
            alert:'',
            filter:'Todas'
        }
    },
    computed: {
        categoriesCompany() {
            let res = []
            for (let category of this.categories.company) {
                res.push(category.name)
            }
            return res
        },
        companiesFilter(){
            let res = Object.values(this.companies.list)
            if(this.filter == 'Todas'){
                return res
            }
            return res.filter( item => { return (item.category == this.filter) } )
        }
    },
    methods: {

        companyNormalizeCategory( company ){
            if(company.id != undefined && !company.hasOwnProperty('updated')){
                for (let category of this.categories.company) {
                    if(category.id == company.category_id){
                        company.category = category.name
                        return company
                    }
                }
            }else{
                for (let category of this.categories.company) {
                    if(category.name == company.category){
                        company.category_id = category.id
                        return company
                    }
                }
            }
        },
        companyNormalizeImage(company){
            company.image = company.image.filter( image => Number.isInteger(image) )
            return company
        },
        companyNormalize( company ){
            console.log(' normalize ' , {company})
            company = this.companyNormalizeCategory( company )
            console.log(' normalize post ' , {company})
            company = this.companyNormalizeImage( company )
            return company
        },
        image(image){
            if(Number.isNaN(parseInt(image))){
              return image
            }
            return API.route('file','open',{id:image}).url
        },
        toggle(company) {
            console.log(this.categories.company)
            this.imagesShow = false
            if (company == this.edit) {
                this.itemNew = Object.assign( {} , this.itemDefault)
                this.imagesCompany = []
                this.mod = false
                this.edit = null
            } else {
                this.imagesCompany = company.image
                this.mod = true
                this.edit = null
                this.edit = company
            }
            setTimeout( ()=> {
                this.imagesShow = true
            }, 500)
        },
        toggleProduct(company) {
            products().getter(company)
                .then(response => {
                    this.nextBtn = response.data.next_page_url
                    this.company_id = company.id
                    for(let product of response.data.products.data){
                        products().push(product)
                    }
                    //console.log(products().list)
                    if (company == this.editProduct) {
                        this.modProduct = false
                        this.editProduct = null
                    } else {
                        this.modProduct = true
                        this.editProduct = company
                    }
                })
                .catch(function (error) {
                    console.log({ error });
                })
        },
        reView(){
            this.view=false
            setTimeout( ()=> {
                this.view = true
            } , 100)
        },
        remove(company){
            this.companies.remove(company)
            .then( res => {
                this.reView()
                console.log({res})
            })
            .catch( error => {
                this.reView()
                console.log( {error} )
            })
        },
        updateImages(images){
            if(this.editProduct != null){
                this.company.image = images
                this.update( this.company )
            }else{
                this.itemNew.image = images
            }
        },
        update(company) {
            this.alert = ''
            company.updated = true
            let companyNew = this.companyNormalize( Object.assign( {} , company ) )
            this.companies.replace( companyNew )
            .then( res => {
                delete company.updated
                console.log({res})
            })
            .catch(error => {
                console.log({error})
                this.alert = JSON.stringify(error)
            })
            /*
            let companyNew = Object.assign({}, company)
            companyNew.category_id = Object.queryid(`name=${companyNew.category}` , this.categories.company)
            this.companies.replace( 'company' , companyNew )
            for (let zone of this.zones) {
                if (zone.name == companyNew.zone) {
                    companyNew.zone = zone.id
                    console.log({ companyNew })
                    return true
                }
            }
            console.log({companyNew})
            */
        },
        create(){
            this.alert = ''
            console.log('test ', this.itemNew)
            this.companies.create( this.companyNormalize( Object.assign({} , this.itemNew) ) )
            .then( response => { 
                this.view = false
                this.companies.push ( this.companyNormalizeCategory(response.data.company) )
                setTimeout( ()=> {
                    this.view = true
                    this.toggle( this.companies[this.companies.length - 1] )
                },500)
            })
             .catch( error => {
                 console.log({error})
                 this.alert = JSON.stringify(error)
                 //this.itemNew = this.itemDefault
                 //this.reView()
            })
        }
    },
    mounted() {
        this.categories = categories()
        this.itemNew = Object.assign({} , this.itemDefault)
    },
    template: `
    <v-row class="pa-2" v-if="view">
        <v-col cols="12" xs="12" sm="12" md="12" lg="12" class="my-3 d-flex">
        <v-select
            label="Mostrar por categorias "
            :items="categoriesCompany"
            v-model="filter"
        ></v-select>
        <v-btn class="ml-3" @click="filter='Todas'">Todas</v-btn>
        </v-col>
        <div v-if="alert!=''"><v-btn @click="this.alert=''" class="red white--text mr-5" small>x</v-btn> <span class="pa-1">{{alert}}</span></div>
        <v-col cols="12" xs="6" sm="6" md="3" lg="2" class="mt-3">
            <span
                @click="modNew=true"
                class="mx-2 pa-3 elevation-2 d-flex justify-center align-center"
                style="font-size:8rem;"
            >+</span>
        </v-col>
        <v-col
            v-for="company of companies.list"
            cols="12" xs="6" sm="6" md="3" lg="2"
            :key="company.id"
        >
            <v-card
                class="mx-auto"
                v-if="company.category == filter || filter == 'Todas'"
            >
                <v-dialog v-if="editProduct == company" v-model="modProduct" fullscreen hide-overlay transition="dialog-bottom-transition">
                    <v-card>
                        <v-toolbar dark color="primary">
                            <v-btn icon dark @click="toggleProduct(company)">
                                <v-icon>mdi-close</v-icon>
                            </v-btn>
                            <v-toolbar-title>{{ company.name }}</v-toolbar-title>
                            <v-spacer></v-spacer>
                        </v-toolbar>
                        <products></products>
                    </v-card>
                </v-dialog>

                <v-dialog v-if="edit == company" v-model="mod" fullscreen hide-overlay transition="dialog-bottom-transition">
                    <v-card>
                        <v-toolbar dark color="primary">
                            <v-btn icon dark @click="toggle(company)">
                                <v-icon>mdi-close</v-icon>
                            </v-btn>
                            <v-toolbar-title>Editar</v-toolbar-title>
                            <v-spacer></v-spacer>
                        </v-toolbar>
                        <v-list two-line>
                            <v-list-item>
                                <v-list-item-icon>
                                    <v-icon color="indigo">mdi-pencil</v-icon>
                                </v-list-item-icon>

                                <v-list-item-content>
                                    <v-list-item-title>
                                        <v-text-field
                                            label="Nombre"
                                            v-model="company.name"
                                            @change="update(company)"
                                        ></v-text-field>
                                    </v-list-item-title>
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item>
                                <v-list-item-icon>
                                    <v-icon color="indigo">mdi-phone</v-icon>
                                </v-list-item-icon>

                                <v-list-item-content>
                                    <v-list-item-title>
                                        <v-text-field
                                            label="Telefono"
                                            v-model="company.phone"
                                            @change="update(company)"
                                        ></v-text-field>
                                    </v-list-item-title>
                                </v-list-item-content>
                            </v-list-item>

                            <v-list-item>
                                <v-list-item-action></v-list-item-action>

                                <v-list-item-content>
                                    <v-list-item-title>
                                        <v-text-field
                                            label="Whatsapp"
                                            v-model="company.whatsapp"
                                            @change="update(company)"
                                        ></v-text-field>
                                    </v-list-item-title>
                                </v-list-item-content>
                            </v-list-item>

                            <v-divider inset></v-divider>

                            <v-list-item>
                                <v-list-item-icon>
                                    <v-icon color="indigo">mdi-email</v-icon>
                                </v-list-item-icon>

                                <v-list-item-content>
                                    <v-list-item-title>
                                        <v-text-field
                                            label="Correo"
                                            v-model="company.email"
                                            @change="update(company)"
                                        ></v-text-field>
                                    </v-list-item-title>
                                </v-list-item-content>
                            </v-list-item>

                            <v-divider inset></v-divider>
        
                            <v-list-item>
                                <v-list-item-icon>
                                    <v-icon color="indigo">mdi-email</v-icon>
                                </v-list-item-icon>
        
                                <v-list-item-content>
                                    <v-list-item-title>
                                        <v-text-field
                                            label="Social"
                                            v-model="company.social"
                                            @change="update(company)"
                                        ></v-text-field>
                                    </v-list-item-title>
                                </v-list-item-content>
                            </v-list-item>

                            <v-divider inset></v-divider>

                            <v-list-item>
                                <v-list-item-icon>
                                    <v-icon color="indigo">mdi-map-marker</v-icon>
                                </v-list-item-icon>

                                <v-list-item-content>
                                    <v-list-item-title>
                                        <v-text-field
                                            label="Dirección"
                                            v-model="company.address"
                                            @change="update(company)"
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
                                            label="Categoria"
                                            :items="categoriesCompany"
                                            v-model="company.category"
                                            @change="update(company)"
                                        ></v-select>
                                    </v-list-item-title>
                                </v-list-item-content>
                            </v-list-item>

                            <v-divider inset></v-divider>

                            <v-list-item>
                                <v-list-item-icon>
                                    <v-icon color="indigo">mdi-clock</v-icon>
                                </v-list-item-icon>

                                <v-list-item-content>
                                    <v-list-item-title >
                                        <v-textarea
                                            label="Horarios"
                                            v-model="company.attention_hours"
                                            @change="update(company)"
                                        ></v-textarea>
                                    </v-list-item-title>
                                </v-list-item-content>
                            </v-list-item>

                            <v-divider inset></v-divider>

                            <v-list-item>
                                <v-list-item-icon>
                                    <v-icon color="indigo">mdi-car</v-icon>
                                </v-list-item-icon>

                                <v-list-item-content>
                                    <v-list-item-title>
                                        <v-text-field
                                            label="Delivery"
                                            type="number"
                                            v-model="company.delivery"
                                            @change="update(company)"
                                        ></v-text-field>
                                    </v-list-item-title>
                                </v-list-item-content>
                            </v-list-item>

                            <div class="pa-5 mx-5">
                                Zonas
                                <div>
                                    <v-btn small text
                                        v-bind:class="[company.zone == zone.id ? 'blue white--text':'','py-1 px-2']"
                                        v-for="zone of zones"
                                        :key="zone.id"
                                        @click="company.zone = zone.id;update(company)"
                                    >{{zone.name}}</v-btn>
                                </div>
                            </div>

                            <div class="pa-5 mx-5">
                                <image-upload @update="updateImages" :items="imagesCompany"></image-upload>
                            </div>
                        </v-list>
                    </v-card>
                </v-dialog>


                <v-img
                    :src="image(company.image[0])"
                    height="240px" dark
                >
                    <div
                        class="d-flex flex-column justify-space-between"
                        style="position: relative;height: 100%;"
                    >

                        <v-card-title class="pa-1" style="background-color:rgba(0,0,0,.6)">
                            <v-btn dark icon @click="remove(company)">
                                <v-icon>mdi-close</v-icon>
                            </v-btn>

                            <v-spacer></v-spacer>

                            <v-btn dark icon class="mr-4" @click="toggle(company)">
                                <v-icon>mdi-pencil</v-icon>
                            </v-btn>

                            <v-btn dark icon @click="toggleProduct(company)">
                                <v-icon>mdi-package-variant</v-icon>
                            </v-btn>
                        </v-card-title>

                        <v-spacer></v-spacer>

                        <div class="white--text" style="background-color:rgba(0,0,0,.6)">
                            <div class="py-0 px-1 d-flex align-center justify-center" style="min-height:5rem;width:100%;word-break: break-word;text-align:center">
                                {{ company.name }}
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
                    <v-spacer></v-spacer><span class="white--text mr-3">Crear</span>
                    <v-btn @click="create" class="white blue--text" small fab><v-icon>mdi-check</v-icon></v-btn>
                </v-toolbar>
                <v-list two-line>
                    <div class="pa-5 mx-5">
                        <image-upload :show="imagesShow" @update="updateImages" :items="imagesCompany"></image-upload>
                    </div>
                    <v-divider inset></v-divider>
                    <v-list-item>
                        <v-list-item-icon>
                            <v-icon color="indigo">mdi-pencil</v-icon>
                        </v-list-item-icon>

                        <v-list-item-content>
                            <v-list-item-title>
                                <v-text-field
                                    label="Nombre"
                                    v-model="itemNew.name"
                                ></v-text-field>
                            </v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                    <v-list-item>
                        <v-list-item-icon>
                            <v-icon color="indigo">mdi-phone</v-icon>
                        </v-list-item-icon>

                        <v-list-item-content>
                            <v-list-item-title>
                                <v-text-field
                                    label="Telefono"
                                    v-model="itemNew.phone"
                                ></v-text-field>
                            </v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>

                    <v-list-item>
                        <v-list-item-action></v-list-item-action>

                        <v-list-item-content>
                            <v-list-item-title>
                                <v-text-field
                                    label="Whatsapp"
                                    v-model="itemNew.whatsapp"
                                ></v-text-field>
                            </v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>

                    <v-divider inset></v-divider>

                    <v-list-item>
                        <v-list-item-icon>
                            <v-icon color="indigo">mdi-email</v-icon>
                        </v-list-item-icon>

                        <v-list-item-content>
                            <v-list-item-title>
                                <v-text-field
                                    label="Correo"
                                    v-model="itemNew.email"
                                ></v-text-field>
                            </v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>

                    <v-divider inset></v-divider>

                    <v-list-item>
                        <v-list-item-icon>
                            <v-icon color="indigo">mdi-email</v-icon>
                        </v-list-item-icon>

                        <v-list-item-content>
                            <v-list-item-title>
                                <v-text-field
                                    label="Social"
                                    v-model="itemNew.social"
                                ></v-text-field>
                            </v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>

                    <v-divider inset></v-divider>

                    <v-list-item>
                        <v-list-item-icon>
                            <v-icon color="indigo">mdi-map-marker</v-icon>
                        </v-list-item-icon>

                        <v-list-item-content>
                            <v-list-item-title>
                                <v-text-field
                                    label="Dirección"
                                    v-model="itemNew.address"
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
                                    label="Categoria"
                                    :items="categoriesCompany"
                                    v-model="itemNew.category"
                                ></v-select>
                            </v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>

                    <div class="pa-5 mx-5">
                        Zonas
                        <div>
                            <v-btn small text
                                v-bind:class="[itemNew.zone == zone.id ? 'blue white--text':'','py-1 px-2']"
                                v-for="zone of zones"
                                :key="zone.id"
                                @click="itemNew.zone = zone.id;"
                            >{{zone.name}}</v-btn>
                        </div>
                    </div>
                </v-list>

                <v-divider inset></v-divider>

                <v-list-item>
                    <v-list-item-icon>
                        <v-icon color="indigo">mdi-car</v-icon>
                    </v-list-item-icon>

                    <v-list-item-content>
                        <v-list-item-title>
                            <v-text-field
                                label="Delivery"
                                type="number"
                                v-model="itemNew.delivery"
                            ></v-text-field>
                        </v-list-item-title>
                    </v-list-item-content>
                </v-list-item>
                <v-divider></v-divider>
                <v-btn @click="create" class="blue white--text ma-5" >Crear <v-icon>mdi-check</v-icon></v-btn>
            </v-card>
        </v-dialog>
        <next :entity="'companies'"></next>
    </v-row>
    `
})
