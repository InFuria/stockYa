

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
    }
    normalize(product){
        product.prince = parseFloat(product.city_id).toFixed(2)
        product.category_id = parseInt(product.category_id)
        product.company_id = parseInt(product.company_id)
        product.status = parseInt(product.status)
        return product
    }
    push(product){
        let list = this.list
        this.list = null
        list[product.name] = product
        this.list = list
    }
    getter(company){
        return this.api('listFromCompany',{id:company.id})
    }
}

