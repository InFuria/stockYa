Vue.component("companies", {
    data() {
        return {
            companies: companies(),
            categories: categories(),
            edit: null, mod: false,
            editProduct: null, modProduct: false,
            zones: zones(),
            itemDefault:{
                "name":"learfen","address":"santa fe 264","email":"learfen001@gmail.com",
                "phone":"3777","whatsapp":"3773","social":"-",
                "city_id":1,"delivery":0,
                "zone":{ "id": 1, "name": "Norte" },
                "attention_hours":"-",
                "category_id":1,"category":""
            },
            itemNew:{},
            modNew:false
        }
    },
    computed: {
        categoriesCompany() {
            let res = []
            for (let category of this.categories.company) {
                res.push(category.name)
            }
            return res
        }
    },
    methods: {
        toggle(company) {
            if (company == this.edit) {
                this.mod = false
                this.edit = null
            } else {
                this.mod = true
                this.edit = company
            }
        },
        toggleProduct(company) {
            products().getter(company)
                .then(response => {
                    for(let product of response.data.data.products){
                        products().push(product)
                    }
                    console.log(products().list)
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
        update(company) {
            this.companies.replace( company )
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
            this.companies.create(this.itemNew)
        }
    },
    mounted() {
        this.categories = categories()
        this.itemNew = Object.assign({} , this.itemDefault)
    },
    template: `
    <v-row class="pa-2">
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
                        <h3 class="pa-3">Productos</h3>
                        <v-card
                            class="mx-auto"
                            max-width="344"
                            outlined
                            v-for="product of company.products"
                            :key="product.id"
                        >
                            <v-list-item three-line>
                            <v-list-item-content>
                                <div class="overline mb-4">OVERLINE</div>
                                <v-list-item-title class="headline mb-1">Headline 5</v-list-item-title>
                                <v-list-item-subtitle>Greyhound divisely hello coldly fonwderfully</v-list-item-subtitle>
                            </v-list-item-content>

                            <v-list-item-avatar
                                tile
                                size="80"
                                color="grey"
                            ></v-list-item-avatar>
                            </v-list-item>

                            <v-card-actions>
                            <v-btn text>Button</v-btn>
                            <v-btn text>Button</v-btn>
                            </v-card-actions>
                        </v-card>
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

                            <div class="pa-5">
                                <image-upload></image-upload>
                            </div>
                        </v-list>
                    </v-card>
                </v-dialog>


                <v-img
                    :src="company.image[0]"
                    height="240px" dark
                >
                    <div 
                        class="d-flex flex-column justify-space-between"
                        style="position: relative;height: 100%;"
                    >

                        <v-card-title class="pa-1" style="background-color:rgba(0,0,0,.6)">
                            <v-btn dark icon>
                                <v-icon>mdi-chevron-left</v-icon>
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
                <v-divider></v-divider>
                <v-btn @click="create" class="blue white--text ma-5" >Crear <v-icon>mdi-check</v-icon></v-btn>
            </v-card>
        </v-dialog>
    </v-row>
    `
})