Vue.component('client', {
    data() { return {
            show: dataVue.show,
            client: {name:"",phone:0,email:"",address:""},
            color: dataVue.color,
            error:false
    }},
    methods:{
        hide() {
            client().proccessCompanyId = null
            modal("client", false)
        },
        next(){
          this.update()
          this.error = false
          if( parseInt(this.client.phone) > 0){
            if(this.client.phone.length < 10){
              this.error = "Numero de telefono no valido"
            }
          }
          if(this.error == false){
            if(client().proccessCompanyId != null){
              modal("client", false)
              cart().websale()
            }else{
              alert( client().proccessCompanyId )
            }
          }else{
            alert(this.error)
          }
        },
        image(img){
            return API.route('file','open',img).url
        },
        update(){
          for(let detail in this.client){
            dataVue.client.update(detail, this.client[detail])
          }  
        }
    },
    mounted(){
      for(let detail in dataVue.client.details){
        this.client[detail] = dataVue.client.details[detail]
      }
    },
    template: `
<v-row justify="center">
    <v-dialog v-model="show.client" fullscreen hide-overlay transition="dialog-bottom-transition" persistent max-width="600px">
      
      <v-toolbar :color="color.primary" class="position-fixed">
        <v-btn icon dark @click="hide">
            <v-icon>mdi-close</v-icon>
        </v-btn>
        <v-toolbar-title class="white--text">Cliente</v-toolbar-title>
      </v-toolbar>
      <v-card>
        <v-card-text>
          <v-container>
            <v-row>
              <v-col cols="12" sm="12" md="12">
                <v-text-field label="Nombre" required v-model="client.name"></v-text-field>
              </v-col>
              <v-col cols="12">
                <small>*Debe completar numero o email para confirmar el pedido</small>
              </v-col>
              <v-col cols="12" xs="6" sm="6" md="6" lg="6">
                <v-text-field class="mx-1" label="Email" type="email" v-model="client.email"></v-text-field>
              </v-col>
              <v-col cols="12" xs="6" sm="6" md="6" lg="6">
                <v-text-field class="mx-1" label="Telefono" hint="Ejemplo:3777112233" type="number"  v-model="client.phone"></v-text-field>
              </v-col>
              <v-col cols="12">
                <small>*Si solicitará delivery complete dirección</small>
                </v-col>
              <v-col cols="12">
                <v-text-field label="Dirección" type="text"  v-model="client.address"></v-text-field>
              </v-col>
            </v-row>
            <v-alert
              dense
              outlined
              type="error"
              v-if="error!=false"
            >
              {{error}}
            </v-alert>
          </v-container>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="blue darken-1" text @click="hide">Cerrar</v-btn>
          <v-btn color="blue darken-1" text @click="next()">Continuar</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-row>
    `
})