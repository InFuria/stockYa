Vue.component("categories-slider" , {
    data(){ return {
        color:color(),
        sectors:sectors(),
        show:show()
    }},
    methods:{
        categoryOpen(v){
            if(v != "todas"){
                if(v == "ofertas"){
                    this.$emit("search","ofertas")
                }else{
                    modal("categories", true)
                    let a = document.querySelector("#category-"+v)
                    a.onclick = e => {
                        document.querySelector("#categories>div").scrollTop = e.target.offsetTop
                    }
                    setTimeout( ()=> {
                        a.click()
                    },500)
                }
            }else{
                modal("categories", true)
            }
        }
    },
    template:`
    <v-col xs="12" sm="12" md="6">
        <v-tabs :color="color.primary" background-color="white" show-arrows>
            <v-tabs-slider ></v-tabs-slider>
            <v-tab @click="categoryOpen('todas')">Todas</v-tab>
            <v-tab @click="categoryOpen(sector.name)"v-for="sector in sectors" :key="sector.name" :href="'#tab-' + sector.name">
                {{sector.name}}
            </v-tab>
            <v-tab @click="categoryOpen('ofertas')">Ofertas</v-tab>
        </v-tabs>
    </v-col>
    `
})