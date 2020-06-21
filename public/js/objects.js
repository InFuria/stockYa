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
        this.details = {name:"",phone:0,email:"",address:""}
        this.details = localStorage.getItem("client") == undefined ? this.details : JSON.parse(localStorage.getItem("client"))
        this.proccesscompanyId = null
    }
    update( k , v ){
        if( this.details[ k ] != undefined ){
            this.details[ k ] = v
            localStorage.setItem("client" , JSON.stringify(this.details))
        }
    }
    validSale( companyId , delivery ){
        console.log("companyId " , companyId)
        let incomplete = []
        if(name == ""){
            incomplete.push("name")
        }
        let contact = this.phone > 0 ? this.phone : ( this.email != "" ? this.email : null )
        if(contact == null){
            incomplete.push("contact")
        }
        if(delivery != undefined && this.address == ""){
            incomplete.push("address")
        }
        this.proccesscompanyId = companyId
        return incomplete
    }
}

class CartProducts{
	constructor(){
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
    websale(company){
        let delivery = this.delivery.indexOf(company)
        if(delivery > -1){
            delete this.companies[company]
            delete this.added[company]
        }
        this.save()
    }
    companyAdd(company){
        let companyId = "company"+company.id
        if(this.added[companyId] == undefined){
            this.added[companyId] = []
            this.companies[companyId] = {total:0,details:company}
        }
        return companyId
    }
    companyId(product){
        return "company"+product.company.id
    }
    isTargeted( product ){
        if(Object.keys(this.added).length == 0){
            return false
        }else{
            if(this.added[this.companyId(product)] == undefined){
                return false
            }
            if(this.added[this.companyId(product)].length == 0){
                return false
            }
            for( let productItem of this.added[this.companyId(product)] ){
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
        if(this.added[this.companyId(product)] == undefined){
            return -1
        }
        return this.added[this.companyId(product)].indexOf(product)
    }
    productCant(product , val){
        if(val == 0){
            this.markRender(product)
            this.productRemove(product)
        }else{
            let prod = this.added[this.companyId(product)][this.productKey(product)]
            prod.cant = val
            prod.total = (prod.price * parseFloat(val)).toFixed(2)
            this.added[this.companyId(product)].splice(this.productKey(product) , 1 , prod)
            this.companyTotal(prod)
        }
        this.save()
    }
    companyTotal(product){
        this.companies[this.companyId(product)].total = 0.0
        for(let a of this.added[this.companyId(product)]){
            this.companies[this.companyId(product)].total += parseFloat(a.total)
        }
        this.companies[this.companyId(product)].total = this.companies[this.companyId(product)].total.toFixed(1)
    }
	productRemove(product){
        this.added[this.companyId(product)].splice(this.productKey(product) , 1)
        if(this.added[this.companyId(product)].length == 0){
            delete this.added[this.companyId(product)]
        }else{
            this.companyTotal(prod)
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


class Categories{
    static api(action, params, paramsUrl){
        params = params == undefined ? {} : params
        paramsUrl = paramsUrl == undefined ? params : paramsUrl
        let { method , url } = API.route('categories' , action , paramsUrl)
        delete params.id
        return axios[method]( url , params)
    }
}