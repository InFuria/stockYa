
class CategoriesList extends APIHelper{
    constructor(){
        super('categories' , ['id','name','is'])
        this.product = []
        this.company = []
    }
    normalize(list){
        return list
    }
    push(type,item){
        type = type == 'products' ? 'product' : type
        let list = categories()[type]
        list.push(item)
        categories()[type] = []
        categories()[type] = list
    }
    create(is,name){
        return this.api('create' , {name}, {is} )
    }
    getter(call_back){
        this.api('list',{is:'products'})
        .then((response)=>{
            let res = Array.sortObject(response.data.data , 'name')
            for( let category of res ){
                categories().product.push(category)
            }
            if(call_back.products != undefined){
                call_back.products()
            }
        })
        .catch(function (error) {
            console.log({error});
        })

        this.api('list',{is:'company'})
        .then((response)=>{
            let res = Array.sortObject(response.data.data , 'name')
            for( let category of res ){
                categories().company.push(category)
            }
            if(call_back.company != undefined){
                call_back.company()
            }
        })
        .catch(function (error) {
            console.log({error});
        })
    }
}