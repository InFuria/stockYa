Vue.component("categories" , {
    data(){ return {
        widthBox:"480px",
        color:color(),
        sectors:sectors(),
        show:show(),
        search:dataVue.search,
    }},
    methods:{
        companyView( v ){
            dataVue.search = `vendedor:${v}`
            this.hide()
        },
        ofertas( v ){
            if(dataVue.search != 'ofertas'){
                dataVue.search = "ofertas"
            }else{
                this.hide()
            }
        },
        hide() {
            modal("categories" , false)
        },
        image(img){
            return API.route('file', 'open', img).url
        }
    },
    mounted(){
        if(this.$vuetify.breakpoint.xsOnly){
            this.widthBox="100%"
        }
    },
    template:`
    <v-navigation-drawer id="categories" v-model="show.categories" app fixed left temporary clipped :width="widthBox">
        <v-toolbar :color="color.primary" class="position-fixed">
            <v-btn icon dark @click="hide">
                <v-icon>mdi-close</v-icon>
            </v-btn>
            <v-toolbar-title class="white--text">Categorias</v-toolbar-title>
        </v-toolbar>
        <v-card>
        <v-card-text class="py-0">
            <v-btn class="my-1" small @click="ofertas">OFERTAS</v-btn>
            <v-timeline
                align-top
                dense
                style="margin-left:-40px"
            >
                <v-timeline-item
                color="teal lighten-3"
                small
                v-for="sector of sectors"
                :key="'category-'+sector.name"
                :id="'category-'+sector.name"
                >
                <v-row>
                    <v-col>
                    <strong>{{sector.name}}</strong>
                    <div class="caption mb-2"></div>
                    <div class="d-flex mb-1 align-center" v-for="company of sector.shops" :key="company.id" @click="companyView(company.name+'@'+company.id)">
                        <v-avatar  height="70px" width="70px" style="border-radius:0" class="elevation-2 mr-1">
                            <v-img :src="image(company.image[0])"></v-img>
                        </v-avatar>
                        <div>{{company.name}}</div>
                    </div>
                    </v-col>
                </v-row>
                </v-timeline-item>
            </v-timeline>
            </v-card-text>
        </v-card>
        <v-expansion-panels v-if="false">
            <v-expansion-panel v-for="sector of sectors" :key="sector.name">
                <v-expansion-panel-header disable-icon-rotate>
                    {{sector.name}}
                    <template v-slot:actions>
                        <v-icon color="error">mdi-alert-circle</v-icon>
                    </template>
                </v-expansion-panel-header>
                <v-expansion-panel-content>
                    <v-chip v-for="company of sector.shops" :key="company.id" @click="companyView(company.name+'@'+company.id)">{{company.name}}</v-chip>
                </v-expansion-panel-content>
            </v-expansion-panel>
        </v-expansion-panels>
    </v-navigation-drawer>
    `
})