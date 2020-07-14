Vue.component('next' , {
    props:['entity','query'],
    data(){ return {
        companies:companies(),
        products:products(),
    }},
    computed:{
        nextBtn(){
            return this[this.entity].nextBtn
        }
    },
    methods:{
        next(){
            if(this.query == undefined){
                this[this.entity].next()
            }else{
                this[this.entity].next(this.query)
            }
        }
    },
    template:`
    <div class="d-flex justify-center">
        <v-btn 
            v-if="nextBtn != null" 
            @click="next" 
            class="blue white--text"
        >
            Ver mas
            <v-icon>mdi-plus</v-icon>
        </v-btn>
    </div>
    `
})