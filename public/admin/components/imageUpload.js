Vue.component('image-upload', {
    props:['items','show'],
    data(){
      return {
        files: '',
        cursor:0,
        responses:[],
        item:[],
        view:true
      }
    },
    watch:{
      view( v ){
        if(v){
          this.item = items
        }else{
          this.reset()
        }
      }
    },
    computed:{
      images(){
        this.item = this.items
        let newArr = []
        let myObj = []
        this.item.forEach(el => !(el in myObj) && (myObj[el] = true) && newArr.push(el))
        return this.item
      },
      uploadReport(){
        let res = ( (this.files.length / this.count) * 100 )
        if( Number.isNaN(res) ){ return ''}
        if(res == 0){ return '' }
        return res + "%"
      },
      imagePostProccess(){
        let res = []
        for(let img of this.images){
          let image = typeof img == 'object' ? img.id : img 
          if(Number.isNaN(parseInt(image))){
            res.push({src:image , id:image})
          }else{
            res.push( {src:API.route('file','open',{id:image}).url , id:image} )
          }
        }
        return res
      }
    },
    methods: {
      reset(){
        //this.view = false
        this.$refs.files.value = null
        setInterval( () => {
            //this.view = true
        }, 100)
      },
      uploadEnd( image ){
        this.$emit('update' , image )
        this.reset()
      },
      submitFiles(){
          if(this.files.length > 0){
                let formData = new FormData();
                formData.append('file', this.files[this.cursor]);
                let { url } = API.route('file','create')
                axios.post( url , formData, 
                    { headers: { 'Content-Type': 'multipart/form-data'}}
                )
                .then(response =>{
                  console.log('server response post image',{response})
                  console.log('push new image>' , response.data.id)
                  if(this.item.indexOf( response.data.id ) == -1){
                    this.item.push(response.data.id)
                  }
                })
                .catch( error =>{
                    this.cursor++
                    console.log('server post error image' , {error})
                    //alert('error al subir imagen')
                })
                .then(()=> {
                    console.log('images for update ',this.item)
                    if(this.cursor < this.files.length){
                      this.cursor++
                      this.submitFiles()
                    }else{
                        this.uploadEnd( this.item )
                    }  
                })
          }
      },
      handleFilesUpload(){
        this.files = this.$refs.files.files;
        this.cursor=0;
        console.clear()
      },
      remove(id){
        for (let i = 0; i < this.item.length; i++) {
          if(this.item[i] == id){
            this.item.splice(i , 1)
            this.$emit('update' , this.item)
            break
          }
        }
      }
    },
    mounted(){
      this.item = this.items
    },
    template:`
        <div class="container" v-if="view">
            <div class="large-12 medium-12 small-12 cell">
              <label>Subir Imagen
                  <input 
                    type="file" id="files" ref="files" multiple 
                    @change="handleFilesUpload()"
                  />
              </label>
              <v-btn class="green white--text" 
                @click="handleFilesUpload();submitFiles()"
              >Subir {{ uploadReport }}</v-btn>
            </div>
            <v-row>
              <v-col v-if="view" class="col" cols="3" v-for="imageId of imagePostProccess" :key="imageId.id">
                  <v-img style="max-height:180px" :src="imageId.src"></v-img>
                  <v-btn @click="remove(imageId.id)" 
                    class="ml-2" style="background-color:rgba(255,255,255,.75);position:absolute;margin-top:-4rem" text color="red">Eliminar</v-btn>
            </v-col>
          </v-row>
        </div>
  `
})