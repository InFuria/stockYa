class ProductsList extends APIHelper{
    constructor(){
        super('product' , [
            'id',
            'name',
            'description',
            'type',
            'price',
            'category_id',
            'company_id',
            'status',
            'image'])
        this.list = {}
        this.company_id = null
        this.targetByProccess = null 
    }
    normalize(product){
        product["image"] = product["image"] == undefined ? [] : product["image"]
        //product["image"] = product["image"].length ? product["image"] : [API.route('product', 'imageDefault').url]
        if(product.image.length > 0){
            for (let index = 0; index < product.image.length; index++) {
                product.image[index] = typeof product.image[index] == 'object' ? product.image[index].id : product.image[index]
                if( typeof product.image[index] != 'object' && Number.isNaN(parseInt(product.image[index])) ){
                    product.image.splice(index , 1)
                }
            }
        }
        product.prince = parseFloat(product.prince).toFixed(2)
        product.company_id = parseInt(product.company_id)
        product.status = parseInt(product.status)
        product.type = String(product.type)
        return product
    }
    push(product){
        let list = this.list
        this.list = null
        if(product.image.length > 0){
            for (let index = 0; index < product.image.length; index++) {
                product.image[index] = typeof product.image[index] == 'object' ? product.image[index].id : product.image[index]
                if( typeof product.image[index] != 'object' && Number.isNaN(parseInt(product.image[index])) ){
                    product.image.splice(index , 1)
                }
            }
        }else{
            product["image"] = product.image.length == 0 ? [API.route('product', 'imageDefault').url] : product.image
        }
        list["product-"+product.id] = product
        this.list = list
    }
    getter(company){
        this.list = {}
        this.company_id = company.id
        return this.api('listFromCompany',{id:company.id})
    }
    replace(productView){
        let product = Object.assign({} , productView)
        return this.api('replace' , product)
    }
    remove(product){
        let list = this.list
        delete list["product-"+product.id]
        this.list = list
        return this.api('remove' , product)
    }
    create(productView){
        let product = Object.assign({} , productView)
        if(product != undefined){
            product.company_id = parseInt(this.company_id)
            return this.api('create' , product )
        }
    }
    image(company){
        return this.api('put' , company)
    }
}

