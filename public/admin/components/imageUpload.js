// filescontrollers linea 20 no existe request/files para validar
Vue.component('image-upload', {
    props:["images"],
    data(){
      return {
        dominio:API.dominio(),
        files: '',
        cursor:0,
        responses:[],
        item:this.images
      }
    },
    methods: {
      submitFiles(){
        let formData = new FormData();
        console.log("files",this.files[this.cursor])
        formData.append('file', this.files[this.cursor]);
        let { url } = API.route('file','create')
        this.cursor++
        axios.post( url , formData, 
            { headers: { 'Content-Type': 'multipart/form-data'}}
        ).then(response =>{
          console.log({response})
            this.item.image.push(response.data.id)
            if(this.cursor < this.files.length){
                this.submitFiles()
            }else{
                this.$emit('update' , this.item)
            }
        })
        .catch( error =>{
          console.log({error})
          console.log('FAILURE!!');
        });
      },
      handleFilesUpload(){
        this.files = this.$refs.files.files;
      },
      remove(i){
        this.images.image.slice(i , 1)
        this.$emit('update' , images)
      }
    },
    mounted(){
      console.log(this.images)
    },
    template:`
        <div class="container">
            <div class="large-12 medium-12 small-12 cell">
            <label>Subir Imagen
                <input type="file" id="files" ref="files" @change="cursor=0" multiple v-on:change="handleFilesUpload()"/>
            </label>
            <v-btn class="green white--text" v-on:click="submitFiles()">Subir</v-btn>
            </div>

            <v-row>
              <v-col class="col" cols="3" v-for="(image , index) of images.image">
                  <v-img :src="dominio+'files/'+image"></v-img>
                  <v-btn @click="remove(index)" class="ml-2" style="background-color:rgba(255,255,255,.75);position:absolute;margin-top:-4rem" text color="red">Eliminar</v-btn>
              </v-col>
          </v-row>
        </div>
        `
})