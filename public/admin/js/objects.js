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
        for(let attr of ["client","product","cart","categories","gallery","company","map"]){
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

class Product{
    static zone(zone){
        return zone == 1 ? "Centro" : (zone == 2 ? "Sur" : "Norte")
    }
}

class CompaniesList{
    constructor(){
        this.list = {}
    }
    push(company){
        let list = this.list
        this.list = null
        list[company.name] = company
        this.list = list
    }
    static create(companyView){
        let company = Object.assign({} , companyView)
        company.city_id = parseInt(company.city_id)
        company.category_id = parseInt(company.category_id)
        company.zone = String(company.zone.id)
        delete company.category
        company.slug = encodeURI(company.name)

        CompaniesList.exe('post' , company)
    }
    static put(company){
        return CompaniesList.exe('put' , company)
    }
    static exe(methods, params, url){
        params = typeof params == 'string' ? params : JSON.stringify(params)
        let request = {
            methods, params,
            url:API.dominio()+'companies',
            responseType: 'json',responseEncoding: 'utf8',
            headers:{
                'Accept':'application/json',
                'Content-Type':'application/json'
            }
        }
        request.url = url == undefined ? request.url : request.url + url
        console.log( { request } )
        return axios(request)
    }
}

class API{
    static dominio(){
        return "https://kaizen-donarosa.com/api/"
    }
    static getter(entity, params , headers){
        params = params == null ? {} : params  
        headers = headers == null ? {} : headers  
        return axios({
            methods:'get',
            url:API.dominio()+entity,
            params,
            responseType: 'json',responseEncoding: 'utf8',
            headers
        })
    }
    static getterPost(entity, params , headers){
        params = params == null ? {} : params  
        headers = headers == null ? {} : headers  
        return axios({
            method:'POST',
            url:API.dominio()+entity,
            params,
            responseType: 'json',
            responseEncoding: 'utf8',
            headers
        })
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
