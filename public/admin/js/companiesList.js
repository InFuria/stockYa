

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
        company.city_id = parseInt(company.city_id)
        company.category_id = parseInt(company.category_id)
        company.zone = String(company.zone.id)
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
            console.log(response)
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
        this.exe('create' , company )
        .then( response => { 
            this.push(response.data.company)
         })
        .catch( error => console.log({error}) )
    }
    replace(company){
        return this.api('replace' , company)
    }
    image(company){
        return this.api('put' , company)
    }
}