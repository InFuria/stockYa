Vue.component('gallery',{
  props:['productslist','type','filter'],
  data(){ return {
      cart:cart(),
      carousel:{},
      colors:{oferta:'red', combo:'green'}
      //productsList:productsList()
  }},
  computed:{ 
    productsList() { 
      return this.productslist.filter(prod => {
        if(this.type != undefined){
          let prodType = prod.type.toLowerCase()
          let types = this.type.toString().toLowerCase().split(',')
          for(let type of types){
            if(type.search('!') == 0){
              if(prodType == type.replace('!','')){
                return false
              }
            } else if(type.search('%') == 0){
              return (prodType.search(type.replace('%','') > -1 ) )
            }else{
              return (type == 'all' || prodType==type)
            }
          }
        }
        if(this.filter != undefined){
          let [ keyName , value ] = this.filter.split('=')
          return ( prod[keyName].toLowerCase() == value )
        }
        return false
      })
    }
    
  },
  methods:{
    color(v){ return (this.colors[v.toLowerCase()] != undefined ? this.colors[v.toLowerCase()] : '') },
    zone(v){ return products().zone(v) },
    mark(product){
      cart().productTarget( product )
      this.cart = cart()
    },
    image(img){
        return API.route('file','open',img).url
    },
    productView(product){
			this.$emit("productview", product)
    },
    socials(){ return socials() }
  },
  mount(){
    //this.productsList=productsList()
  },
  template:`
  <v-container fluid>
    <v-row dense>
      <v-col
        v-for="product in productsList"
        :key="product.id"
        xs="6" sm="6" md="4" lg="2"
        class="mb-5"
      >
        <v-card style="max-width:45vw" v-bind:class="[color(product.type)]" >
          <div style="position:absolute;z-index:1;width:100%" 
          xs="6" sm="6" md="4" lg="2" class="col d-flex justify-space-between">
            <v-btn style="background-color:rgba(0,0,0,.5);" icon class="white--text" @click="product.ui.detailsShow=!product.ui.detailsShow">
              <v-icon>mdi-share-variant</v-icon>
            </v-btn>
            <v-btn :class="product.ui.markColor[0]" icon @click="mark(product)">
              <v-icon :class="product.ui.markColor[1]">{{product.ui.mark}}</v-icon>
            </v-btn>
          </div>
          <v-carousel v-model="product.ui.carousel" 
              :hide-delimiters="true" :show-arrows="(product.image.length > 1)"
              :height="$vuetify.breakpoint.xsOnly || $vuetify.breakpoint.smOnly ? '40vw' : '200px'"
          >
              <v-carousel-item
                  v-for="img of product.image"
                  :key="img.id"
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
                              :height="$vuetify.breakpoint.xsOnly || $vuetify.breakpoint.smOnly ? '40vw' : '200px'"
                              @click="productView(product)"
                              :cover="true" :src="image(img)"
                            >
                              <v-card-title class="justify-center" style="font-size:1rem;text-shadow:1px 1px 2px #000;word-break: break-word;line-height: 1rem;">{{product.name}}</v-card-title>
                            </v-img>
                        </v-col>
                    </v-row>
                  </v-sheet>
              </v-carousel-item>
          </v-carousel>
          <small class="px-3 justify-center d-flex justify-space-between">
            <div class="d-flex justify-center" >
              <span>Zona: </span><span>{{ zone(product.zone) }}</span>
            </div>
            <div class="d-flex" v-if="false" style="flex-direction:column">
              <span>Ventas</span>
              <span>{{product.score}}</span>
            </div>
          </small>
          <v-card-actions @click="productView(product)" v-bind:class="product.mark=='mdi-bookmark' ? 'blue' : ''">
            <template v-if="product.company.delivery > 0">
              <v-icon>mdi-truck</v-icon>
            </template>
            <template  v-if="false">
              <v-icon small color="yellow">mdi-star</v-icon><small>{{product.score}}</small>
            </template>
            <v-spacer></v-spacer>
            <span>
              $ {{product.price}}
            </span>
          </v-card-actions>
          <v-card-actions v-if="product.ui.detailsShow" class="white justify-center">
            <a :href="'http://www.facebook.com/sharer.php?u=https://pedidosgoya.com/'+product.slug+'&t='+encodeURI(product.name)">
              <v-btn
                v-for="social of socials"
                :key="i"
                :color="social.color"
                class="white--text"
                fab
                icon
                small
              >
                <v-icon>{{ social.icon }}</v-icon>
              </v-btn>
            </a>
          </v-card-actions>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
  `
})