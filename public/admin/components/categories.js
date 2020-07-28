Vue.component("categories-post", {
    data() {
        return {
            name:''
        }
    },
    methods:{
        create(key){
            categories().create(key,this.name)
            .then( res => {
                key = key == 'products' ? 'product' : key
                categories()[key].push({name:this.name , id:res.data.category})
                this.name = ''
            })
            .catch( error => console.log({error}))
        }
    },
    template:`
    <div>
        <v-text-field
            label="Categoria Nueva"
            v-model="name"
        ></v-text-field>
        <v-btn @click="create('products')">Producto</v-btn>
        <v-btn @click="create('company')">Comercio</v-btn>
        <v-divider></v-divider>
    </div>
    `
})