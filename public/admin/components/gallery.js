Vue.component('gallery',{
  data(){ return {
      cart:cart(),
      carousel:{},
      socials:dataVue.socials,
      productsList:productsList()
  }},
  methods:{
    zone(v){ return Product.zone(v) },
    mark(product){
      cart().productTarget( product )
    },
    productView(product){
			this.$emit("productview", product)
    }
  },
  mount(){
    this.productsList=productsList()
  },
  template:`
  <v-container fluid>
    <v-row dense>
      <v-col
        v-for="product in productsList"
        :key="product.slug"
        xs="6" sm="6" md="4" lg="2"
        class="mb-5"
      >
        <v-card style="max-width:45vw">
          <div style="position:absolute;z-index:1;width:100%" 
          xs="6" sm="6" md="4" lg="2" class="col d-flex justify-space-between">
            <v-btn style="background-color:rgba(0,0,0,.5);" icon class="white--text" @click="product.ui.detailsShow=!product.ui.detailsShow">
              <v-icon>mdi-share-variant</v-icon>
            </v-btn>
            <v-btn :class="product.ui.markColor[0]" icon @click="mark(product)">
              <v-icon :class="product.ui.markColor[1]">{{product.ui.mark}}</v-icon>
            </v-btn>
          </div>
          <v-carousel v-model="product.ui.carousel" height="200px" :hide-delimiters="true" :show-arrows="(product.image.length > 1)">
              <v-carousel-item
                  v-for="image of product.image"
                  :key="image"
              >
                  <v-sheet tile >
                    <v-row
                        class="fill-height"
                        align="center"
                        justify="center"
                    >
                        <v-col class="display-1" xs="6" sm="6" style="max-width:100%">
                            <v-img
                              style="background-color:#fff"
                              loading="lazy"
                              class="white--text align-end"
                              gradient="to bottom, rgba(0,0,0,.1), rgba(0,0,0,.5)"
                              height="200px"
                              @click="productView(product)"
                              :contain="true" v-if="image.search('jpg') > -1" :src="image"
                            >
                              <v-card-title class="justify-center" style="font-size:1rem;text-shadow:1px 1px 2px #000;word-break: break-word;line-height: 1rem;">{{product.name}}</v-card-title>
                            </v-img>
                            <v-card-title v-else class="justify-center mt-5" style="font-size:1rem;text-shadow:1px 1px 2px #000;word-break: break-word;line-height: 1rem;text-align:center">{{product.name}}</v-card-title>
                        </v-col>
                    </v-row>
                  </v-sheet>
              </v-carousel-item>
          </v-carousel>
          <small class="px-3 justify-center d-flex justify-space-between">
            <div class="d-flex justify-center" >
              <span>Zona</span><span>{{ zone(product.zone) }}</span>
            </div>
            <div class="d-flex" v-if="false" style="flex-direction:column">
              <span>Ventas</span>
              <span>{{product.score}}</span>
            </div>
          </small>
          <v-card-actions @click="productView(product)" v-bind:class="product.mark=='mdi-bookmark' ? 'blue' : ''">
            <v-icon small color="yellow">mdi-star</v-icon><small>{{product.score}}</small>
            <v-spacer></v-spacer>
            <span>
              $ {{product.price}}
            </span>
          </v-card-actions>
          <v-card-actions v-if="product.ui.detailsShow" class="white justify-center">
              <v-btn
                v-for="(social, i) in socials"
                :key="i"
                :color="social.color"
                class="white--text"
                fab
                icon
                small
              >
                <v-icon>{{ social.icon }}</v-icon>
              </v-btn>
            </v-card-actions>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
  `
})