

class CompaniesList extends APIHelper{
    constructor(){
        super('company' , [
            'id',
            'name',
            'email',
            'phone',
            'category_id',
            'address',
            'whatsapp',
            'social',
            'image',
            'delivery',
            'zone',
            'status',
            'attention_hours',
            'company_id'])
        this.list = {}
    }
    normalize(company){
        company.company_id = company.company_id == null ? 0 : parseInt(company.company_id)
        company.city_id = parseInt(company.city_id)
        company.category_id = parseInt(company.category_id)
        for (let index = 0; index < company.image.length; index++) {
            if(company.image[index].search("http") > -1){
                company.image.splice(index , 1)
            }
        }
        company.zone = typeof company.zone == 'object' ? String(company.zone.id) : String(company.zone)
        return company
    }
    push(company){
        let list = this.list
        this.list = null
        list[company.name] = company
        this.list = list
    }
    getter(){
        return this.api('list')
    }
    getterInstance(){
        this.getter()
        .then( response => {
            this.nextBtn = response.data.next_page_url
            for( let company of response.data.data ){
                if(company.image.length == 0){
                    company.image = [API.route('company','imageDefault').url]
                }else{
                    for(let a of company.image){
                        a = './uploadedimages/'+a+'.jpg'
                    }
                }
                company['ui'] = {view:false}
                company.zone = typeof company.zone == "string" ? 1 : company.zone
                company['category'] = Object.queryid(`${company.category_id}=name` , categories().company)
                this.push(company)
            }
        })
        .catch(function (error) {
            console.log(error);
        })
    }
    create(companyView){
        let company = Object.assign({} , companyView)
        return this.api('create' , company )
    }
    replace(company){
        return this.api('replace' , Object.assign({} , company))
    }
    remove(company){
        return this.api('remove' , company)
    }
    image(company){
        return this.api('put' , company)
    }
}