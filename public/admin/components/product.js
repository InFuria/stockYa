Vue.component('product', {
    data() { return {
            show: show(),
            loading: false,
            selection: 1,
            imagesSliderStatus:0,
            product:productView(),
            color:color(),
            markAux:null
    }},
    computed:{
        markGet(){
            this.markAux = this.markAux == null ? cart().isTargeted(this.product) : this.markAux
            return this.markAux
        }
    },
    methods:{
        zone(v){ return Product.zone(v) },
        companyView(company){
            this.$emit("search", "vendedor:"+company)
        },
        reserve () {
            this.loading = true
            setTimeout(() => (this.loading = false), 2000)
        },
        push(){
            cart().productTarget( this.product )
            this.markAux = !this.markAux
        },
        hide() {
            modal("product" , false)
        }
    },
    template: `
<v-row justify="center">
    <v-dialog v-model="show.product" fullscreen hide-overlay transition="dialog-bottom-transition">
    <v-card
      :loading="loading"
      class="mx-auto my-12 px-1"
    >
        <v-toolbar :color="color.primary" class="position-fixed">
            <v-btn icon dark @click="hide">
                <v-icon>mdi-close</v-icon>
            </v-btn>
            <v-toolbar-title>.</v-toolbar-title>
            <v-spacer></v-spacer>
            <v-toolbar-items class="align-center">
                <v-rating
                    :value="parseFloat(product.score)"
                    color="amber"
                    dense
                    half-increments
                    readonly
                    size="20"
                ></v-rating>
                <div class="white--text ml-4">{{product.score}}</div>
            </v-toolbar-items>
        </v-toolbar>
    <v-row>
    <v-col xs="12" sm="12" md="6">
        <div class="px-1">
            <v-carousel v-if="$vuetify.breakpoint.xsOnly && product.image.toString().search('jpg') > -1" v-model="model" style="max-height:320px;max-width:320px" :hide-delimiters="(product.image.length < 2)" :show-arrows="(product.image.length > 1)">
                <v-carousel-item
                    v-for="image of product.image"
                    :key="image"
                >
                    <v-sheet
                    height="320px"
                    width="320px"
                    tile
                    >
                    <v-row
                        class="fill-height"
                        align="center"
                        justify="center"
                    >
                        <div class="display-1">
                            <v-img width="320px" height="320px"
                                v-if="image.search('jpg') > -1" 
                                loading="lazy" 
                                :src="image"
                                :contain="false"
                            ></v-img>
                        </div>
                    </v-row>
                    </v-sheet>
                </v-carousel-item>
            </v-carousel>
            <v-carousel v-else v-model="imagesSliderStatus" style="max-height:480px;max-width:480px" :hide-delimiters="(product.image.length < 2)" :show-arrows="(product.image.length > 1)">
                <v-carousel-item
                    v-for="image of product.image"
                    :key="image"
                >
                    <v-sheet
                    height="480px"
                    width="480px"
                    tile
                    >
                    <v-row
                        class="fill-height"
                        align="center"
                        justify="center"
                    >
                        <div class="display-1">
                            <v-img width="480px" height="480px"
                                v-if="image.search('jpg') > -1"
                                loading="lazy" 
                                :src="image"
                                :contain="false"
                            ></v-img>
                        </div>
                    </v-row>
                    </v-sheet>
                </v-carousel-item>
            </v-carousel>
        </div>
    </v-col>
    <v-col xs="12" sm="12" md="6">
        <v-card-title class="text-uppercase">
            {{product.name}}
            <v-btn small color="orange" class="mx-2 white--text" @click="push">
                Agregar al carrito <v-icon v-if="markGet">mdi-check</v-icon>
            </v-btn>
        </v-card-title>
    
        <v-card-text>
            <div class="green--text my-4 subtitle-1">
                <b>Precio: $ {{product.price}}</b>
            </div>
            <v-chip-group
                active-class="accent-4 white--text"
                column
            >
                <v-chip v-for="tag of product.units" :key="tag">{{tag}}</v-chip>
            </v-chip-group>
            <v-chip-group
                active-class="accent-4 white--text"
                column
            >
                <v-chip v-for="tag of product.tags" :key="tag">{{tag}}</v-chip>
            </v-chip-group>
        </v-card-text>
        <v-card-title class="text-uppercase">
            VENDEDOR
        </v-card-title>
        <v-card-text>
            <div>
                <div class="d-flex">
                    <span>{{product.company.name}}</span>
                    <v-rating
                        :value="parseFloat(product.company.score)"
                        color="amber"
                        dense
                        half-increments
                        readonly
                        size="14"
                    ></v-rating>
                </div>
                <v-btn text :color="color.primary" @click="companyView(product.company.name+'@'+product.company.id)">+ Detalles del vendedor</v-btn>
            </div>
            <v-chip-group>
                <v-chip v-if="false">Ventas en el sitio: {{product.company.score_count}}</v-chip>
                <v-chip>Delivery: {{(product.company.delivery == 1 ? "SI" : "NO")}}</v-chip>
                <v-chip>Zona: {{ zone(product.company.zone) }}</v-chip>
            </v-chip-group>
        </v-card-text>
    </v-col>
    </v-row>
    </v-card>
    </v-dialog>
  </v-row>
    `
})