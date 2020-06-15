


class API{
    static dominio(){
        return "https://kaizen-donarosa.com/api/"
    }
    static routes(){
        return {
			company:{
                list:{method:'get',url:'companies'},
                listPage:{method:'get',url:'companies?page=<page>'},
				create:{method:'post',url:'companies'},
				replace:{method:'put',url:'companies/<id>'},
				update:{method:'patch',url:'companies/<id>'},
				remove:{method:'delete',url:'companies/<id>'},
                status:{method:'post',url:'companies/<id>/status'},
                imageDefault:{url:'https://industriacide.com/wp-content/uploads/2019/08/Various-Steps-In-Forming-A-Company-750x430.jpg'}
            },
            product:{
                list:{method:'get',url:'products'},
				listPage:{method:'get',url:'products?page=<page>'},
				listFromCompany:{method:'get',url:'products?company_id=<id>&page=<page>'},
				create:{method:'post',url:'products'},
				replace:{method:'put',url:'products/<id>'},
				update:{method:'patch',url:'products/<id>'},
				remove:{method:'delete',url:'products/<id>'},
                status:{method:'post',url:'products/<id>/status'},
                imageDefault:{url:'https://www.mageplaza.com/assets/img/extensions/product-labels.png'}
            },
            categories:{
                create:{method:'post',url:'categories/<is>'},
                list:{method:'get',url:'categories/<is>'},
            },
			file:{
                create:{method:'post',url:'files'},
                open:{method:'get',url:'files/<id>'}
            },
		}
    }
    static route(entity , action , data){
        let res = API.routes()[entity][action]
        if(data != undefined && res.url.search("<") > -1){
            for(let a in data){
                res.url = res.url.replace("<"+a+">" , data[a])
            }
        }
        res.url = res.url.search('http') == -1 ? API.dominio() + res.url : res.url
        
        if(res.url == "https://kaizen-donarosa.com/api/files/<id>"){
            res.url = API.route('company','imageDefault').url
        }
        return res
    }
    static categories( call_back ){
        API.getter('categories/products')
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

        API.getter('categories/company')
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

class APIHelper{
    constructor(entity , valids){
        this.entity = entity
        this.valids = valids
        this.nextBtn = true
        this.current_page = 1
    }
    valid(item){
        for(let a in item){
            if(this.valids.indexOf(a) == -1){
                delete item[a]
            }
        }
        return item
    }
    api(action, params, paramsUrl){
        params = params == undefined ? {} : this.normalize(params)
        params = this.valid(params)
        paramsUrl = paramsUrl == undefined ? params : paramsUrl
        let { method , url } = API.route(this.entity , action , paramsUrl)
        delete params.id
        return axios[method]( url , params)
    }
    next(params){
        this.current_page++
        let paramsUrl = {page:this.current_page}
        if(params != undefined){
            for(let a in params){
                paramsUrl[a] = params[a]
            }
        }
        let { method , url } = API.route(this.entity , 'listPage' , paramsUrl)
        return axios[method]( url , paramsUrl)
        .then(response => {
            this.nextBtn = response.next_page_url
            for(let item of response.data[this.entity]){
                this.push(item)
            }
        }).catch( error => {console.log({error})})
    }
}