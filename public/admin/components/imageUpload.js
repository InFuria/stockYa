// filescontrollers linea 20 no existe request/files para validar
Vue.component('image-upload', {
    props:["images"],
    data(){
      return {
        files: '',
        cursor:0,
        responses:[]
      }
    },
    methods: {
      submitFiles(){
        let formData = new FormData();
        console.log("files",this.files[this.cursor])
        formData.append('image', this.files[this.cursor]);
        let { url } = API.route('image','create')
        console.log({url})
        this.cursor++
        axios.post( url , formData, 
            { headers: { 'Content-Type': 'multipart/form-data'}}
        ).then(response =>{
            console.log({response})
            if(this.cursor < this.files.length){
                this.submitFiles()
            }else{
                //this.$emit('upload' , )
            }
        })
        .catch( error =>{
            this.responses.push({error})
          console.log('FAILURE!!');
        });
      },
      handleFilesUpload(){
        this.files = this.$refs.files.files;
      }
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
              <v-col class="col" cols="3" v-for="(image , index) of images">
                  <v-img :src="image"></v-img>
                  <v-btn @click="$emit('remove',index)" class="ml-2" style="background-color:rgba(255,255,255,.75);position:absolute;margin-top:-4rem" text color="red">Eliminar</v-btn>
              </v-col>
          </v-row>
        </div>
        `
})