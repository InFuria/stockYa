

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
        company.image = company.image == undefined || !Array.isArray(company.image) ? [] : company.image
        if(company.image.length > 0){
            for (let index = 0; index < company.image.length; index++) {
                if( Number.isNaN(parseInt(company.image[index])) ){
                    company.image.splice(index , 1)
                }
            }
        }
        company.zone = typeof company.zone == 'object' ? String(company.zone.id) : String(company.zone)
        return company
    }
    push(company){
        let list = this.list
        this.list = null
        list['company-'+company.id] = company
        company.zone = company.zone == null ? 1 : company.zone
        let images = []
        for(let image of company.image){
            images.push( ( typeof image == 'object' ? image.id : image ) )
        }
        company.image = images
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
                }
                company['ui'] = {view:true}
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
    replace(companyView){ 
        let company = Object.assign({} , companyView)
        return this.api('replace' , company)
    }
    remove(company){
        let list = this.list
        delete list["company-"+company.id]
        this.list = list
        return this.api('remove' , company)
    }
    image(company){
        return this.api('put' , company)
    }
}