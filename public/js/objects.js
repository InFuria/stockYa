class NavegationManager{
    static go(hash){
        history.pushState( null , null, `${hash}` );
        NavegationManager.valid()
    }
    static valid(){
        if( window.location.hash == "#home" ) {
            for(let a in show()){
                if(ShowComponents.notModal().indexOf(a) == -1){
                    show(a , false)
                }
            }
        }
    }
}

class ShowComponents{
	constructor(){
        for(let attr of ["client","product","cart","categories","gallery","company","map","categories"]){
            this[attr] = false
        }
    }
    static notModal(){
        return ["gallery","company","map"]
    }
    modal(k,v){
        v = Boolean(v)
        if(v){
            this[k] = v
            NavegationManager.go( ( v ? `#${k}` : "#home" ) );
        }else{
            NavegationManager.go( "#home" );
        }
    }
}

class Client{
    constructor(){
        this.details = localStorage.getItem("client") == undefined ? {name:"",phone:0,email:"",address:""} : JSON.parse(localStorage.getItem("client"))
        this.proccessCompanyId = null
        this.phone = 0
        this.email = ""
        this.address = ""
        this.name = ""
    }
    update( k , v ){
        if( this.details[ k ] != undefined ){
            this.details[ k ] = v
            localStorage.setItem("client" , JSON.stringify(this.details))
        }
    }
    validSale( companyId , delivery ){
        let incomplete = []
        if(this.details.name == ""){
            incomplete.push("name")
        }
        let contact = this.details.phone > 0 ? this.details.phone : ( this.details.email != "" ? this.details.email : null )
        if(contact == null){
            incomplete.push("contact")
        }
        if(delivery != undefined && this.details.address == ""){
            incomplete.push("address")
        }
        this.proccessCompanyId = companyId
        return incomplete
    }
}

class CartProducts extends APIHelper{
    constructor(){
        super('websale', "client_name,phone,email,company_id,details,delivery".split(","))
        this.companies = {}
        this.added = {}
        this.delivery = []
        this.history = false
        if(localStorage.getItem("cart") != undefined){
            let cartStorage = JSON.parse(localStorage.getItem("cart"))
            for(let a of ["companies" , "added" , "delivery"]){
                this[a] = cartStorage[a]
            }
            this.history = true
        }
    }
    normalize( v ){ return v}
    valid( v ){ return v}
    open(productsList){
        if(this.history == true){
            for(let company in this.added){
                this.companyAdd(this.added[company][0].company)
                for(let prodKey in this.added[company]){
                    for(let product of productsList){
                        if(this.added[company][prodKey].id == product.id){
                            product.cant = this.added[company][prodKey].cant
                            product.total = this.added[company][prodKey].total
                            this.added[company].splice(prodKey, 1, product)
                        }
                    }
                }
            }
        }
    }
    inspect(productsList){
        for(let a of productsList){
            if(this.target(a , this)){
                this.markRender(a)
            }
        }
        return productsList
    }
    websale(){
        let company = client().proccessCompanyId
        /*
        if(delivery > -1){
            //delete this.companies[company]
            //delete this.added[company]
        }
        */
        console.log(company)
        let companyId = "company"+company.details.id
        let send = {
            client_name:client().details.name,
            phone:client().details.phone,
            email:client().details.email,
            address:client().details.address,
            company_id:company.details.id,
            details:[],
            delivery:company.deliveryActive
        }
        for(let product of cart().added[companyId]){
            send.details.push({product_id:product.id, quantity:product.cant})
        }

        this.api('create', send)
        .then( res => {
            window.open(`https://wa.me/54${res.data.data.phone}?text=PEDIDOSGOYA Codigo de pedido:https://kaizen-donosa.com/api/websales/${res.data.data.tracker}`)
        })
        .catch( error => console.log({error}))
        // client_name , email , phone , company_id , tags , text  
        this.save()
    }
    companyAdd(company){
        console.log(' add ' , this.added)
        let companyId = "company"+company.id
        console.log( ' companyAdd ' , {companyId})
        if(this.added[companyId] == undefined){
            this.added[companyId] = []
            this.companies[companyId] = {total:0,details:company}
            this.companies[companyId].deliveryActive = false
        }
        return companyId
    }
    companyIdFromProduct(product){
        return "company"+product.company.id
    }
    isTargeted( product ){
        if(Object.keys(this.added).length == 0){
            return false
        }else{
            if(this.added[this.companyIdFromProduct(product)] == undefined){
                return false
            }
            if(this.added[this.companyIdFromProduct(product)].length == 0){
                return false
            }
            for( let productItem of this.added[this.companyIdFromProduct(product)] ){
                if(productItem.id == product.id){
                    return true
                }
            }
            return false
        }
    }
    markRender(product){
        product.ui.markColor = product.ui.mark == "mdi-check-circle" ? ["blue",""] : ["white","green--text"]
        product.ui.mark = product.ui.mark == "mdi-check-circle" ? "mdi-cart-plus" : "mdi-check-circle"
    }
	productTarget(product){
        let companyId = this.companyAdd(product.company)
		if( this.isTargeted(product) ){
			this.productRemove(product)
		}else{
            product.cant = 1
            product.total = product.price
            this.added[companyId].push(product)
            this.companyTotal(product)
        }
        this.markRender( product )
        this.save()
    }
    productKey(product){
        if(this.added[this.companyIdFromProduct(product)] == undefined){
            return -1
        }
        return this.added[this.companyIdFromProduct(product)].indexOf(product)
    }
    productCant(product , cant){
        if(cant == 0){
            this.markRender(product)
            this.productRemove(product)
        }else{
            let company_key = this.companyIdFromProduct(product)
            let product_key = this.productKey(product)
            
            this.added[company_key][product_key].cant = cant
            this.added[company_key][product_key].total = (product.price * parseFloat(cant)).toFixed(2)
            
            this.companyTotal( this.added[company_key][product_key] )
        }
        this.save()
    }
    companyTotal(product){
        this.companies[this.companyIdFromProduct(product)].total = 0.0
        for(let a of this.added[this.companyIdFromProduct(product)]){
            this.companies[this.companyIdFromProduct(product)].total += parseFloat(a.total)
        }
        let company = this.companies[this.companyIdFromProduct(product)]
        company.total = this.companies[this.companyIdFromProduct(product)].total.toFixed(1)
        this.companies[this.companyIdFromProduct(product)] = null
        this.companies[this.companyIdFromProduct(product)] = company
    }
	productRemove(product){
        let company_key = this.companyIdFromProduct(product)
        this.added[company_key].splice(this.productKey(product) , 1)
        if(this.added[company_key].length == 0){
            delete this.added[company_key]
        }else{
            this.companyTotal(product)
        }
        this.save()
	}
	list(company){
		return _private.get(company)
    }
    save(){
        localStorage.setItem("cart" , JSON.stringify( { companies:this.companies , added:this.added, delivery:this.delivery} ) )
    }
}

class Products extends APIHelper{
    constructor(){
        super('product', ['id','visits'])
    }
    normalize(v){
        return v
    }
    valid(v){
        return v
    }
    fromCompany(v){
        return this.api('listFromCompany', {id:v.split("@")[1]})
    }
    zone(zone){
        return zone == 1 ? "Centro" : (zone == 2 ? "Sur" : "Norte")
    }
}


class Categories extends APIHelper{
    constructor(){
        super('categories' , ['id' , 'name'])
        this.product=[]
        this.company=[]
    }
    normalize(v){
        return v
    }
    valid(v){
        return v
    }
    getAll( v ){
        if(this.product.length == 0){
            this.api('list',{is:'products'})
            .then( res => {
                res.data.data.forEach( item => this.product.push(item) )
                this.showCategories = v
            })
            this.api('list',{is:'companies'})
            .then( res => {
                res.data.data.forEach( item => this.company.push(item) )
            })
        }else{
            this.showCategories = v
        }
    }
    /*
    static api(action, params, paramsUrl){
        params = params == undefined ? {} : params
        paramsUrl = paramsUrl == undefined ? params : paramsUrl
        let { method , url } = API.route('categories' , action , paramsUrl)
        delete params.id
        return axios[method]( url , params)
    }
    */
}

class CompaniesList extends APIHelper{
    constructor(){
        super('company' , ['id' , 'name' , 'company_id'])
        this.list = []
    }
    normalize(v){
        return v
    }
    valid(v){
        return v
    }
    listRequest(){
        return this.api('list')
    }
    renderSectors(){
        if(this.list.length == 0){
            this.listRequest().then((response)=>{
                this.list = response.data.data
                let sectorsName = []
                for( let company of response.data.data ){
                    let category = Object.queryid(`${company.category_id}=name` , dataVue.categories.company)
                    let key = sectorsName.indexOf(category)
                    if(key == -1){
                        sectorsName.push(category)
                        sectors().push({name:category,shops:[company]})
                    }else{
                        sectors()[key].shops.push(company)
                    }
                }
            })
            .catch( error => {
                console.log({error});
                setTimeout( ()=> {
                    this.renderSectors()
                }, 5000) 
            })
        }
    }
}

Object["queryid"] = (query , array) => {
    let [key , val] = query.split("=")
    if(Number.isNaN( parseInt(key) )){
        for(let a of array){
            if(a[key] == val){
                return a.id
            }
        }
    }else{
        for(let a of array){
            if(a.id == key){
                return a[val]
            }
        }
    }
    return null
}

Array["sortObject"] = (array , key)=>{
    array.sort(function (o1,o2) {
        //comparación lexicogŕafica
        if (o1[key] > o2[key]) { return 1; } 
        else if (o1[key] < o2[key]) { return -1; } 
        return 0;
    })
    return array
}