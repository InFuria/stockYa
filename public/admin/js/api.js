


class API{
    static dominio(){
        return "http://kaizen-donarosa.com/api/"
    }
    static routes(){
        return {
			company:{
                list:{method:'get',url:'companies'},
                create:{method:'post',url:'companies'},
				replace:{method:'put',url:'companies/<id>'},
				update:{method:'patch',url:'companies/<id>'},
				remove:{method:'delete',url:'companies/<id>'},
                status:{method:'post',url:'companies/<id>/status'},
                imageDefault:{url:'https://industriacide.com/wp-content/uploads/2019/08/Various-Steps-In-Forming-A-Company-750x430.jpg'}
            },
            product:{
                list:{method:'get',url:'products'},
				listFromCompany:{method:'get',url:'products?company_id=<id>'},
				create:{method:'post',url:'products'},
				replace:{method:'put',url:'products/<id>'},
				update:{method:'patch',url:'products/<id>'},
				remove:{method:'delete',url:'products/<id>'},
                status:{method:'post',url:'products/<id>/status'},
            },
			image:{create:{method:'post',url:'files'}},
		}
    }
    static route(entity , action , data){
        let res = API.routes()[entity][action]
        if(data != undefined && res.url.search("$") > -1){
            console.log({data})
            for(let a in data){
                res.url = res.url.replace("<"+a+">" , data[a])
            }
        }
        res.url = res.url.search('http') == -1 ? API.dominio() + res.url : res.url
        return res
    }
    static categories( call_back ){
        API.getter('categories/products')
        .then((response)=>{
            for( let category of response.data.data ){
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
            for( let category of response.data.data ){
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
    }
    valid(item){
        for(let a in item){
            if(this.valids.indexOf(a) == -1){
                delete item[a]
            }
        }
        return item
    }
    api(action, params){
        params = params == undefined ? {} : this.normalize(params)
        params = this.valid(params)
        let { method , url } = API.route(this.entity , action , params)
        console.log( { method , url } )
        return axios[method]( url , params)
    }
}