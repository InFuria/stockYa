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
    }
    normalize(product){
        product["image"] = product["image"] == undefined ? [] : product["image"]
        product["image"] = product["image"].length ? product["image"] : [API.route('product', 'imageDefault').url]
        product.image = product.image == undefined ? [] : product.image
        for (let index = 0; index < product.image.length; index++) {
            if(product.image[index].search("http") > -1){
                product.image.splice(index , 1)
            }
        }
        product.prince = parseFloat(product.city_id).toFixed(2)
        product.category_id = parseInt(product.category_id)
        product.company_id = parseInt(product.company_id)
        product.status = parseInt(product.status)
        return product
    }
    push(product){
        let list = this.list
        this.list = null
        product["image"] = product["image"] == undefined ? [] : product["image"]
        product["image"] = product["image"].length ? product["image"] : [API.route('product', 'imageDefault').url]
        list["product-"+product.id] = product
        this.list = list
    }
    getter(company){
        return this.api('listFromCompany',{id:company.id})
    }
    replace(product){
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
        return this.api('create' , product )
    }
}

