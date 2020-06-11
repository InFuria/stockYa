
class TestCompany{
    static post(){
        let ref = "TestCompany.post"
        name = `company ${Date.now()}`
        let body = {
            name,
            "address":"santa fe 264",
            "email":`company${Date.now()}@gmail.com`,
            "phone":`3777${Date.now()}`,
            "whatsapp":`3773${Date.now()}`,
            "social":"-",
            "city_id":1,
            "delivery":0,
            "zone":"2",
            "attention_hours":"-",
            "category_id":1,
            "slug":encodeURI(name)
        }
        TestCompany.exe(ref , 'post' , body)
    }
    static put(){
        let ref = "TestCompany.put"
        let body = {"id":2,"address":"calle falsa "+String(Date.now())}
        TestCompany.exe(ref , 'put' , body)
    }
    static delete(id){
        let ref = "TestCompany.delete"
        TestCompany.exe(ref , 'delete' , {} , "/"+id)
    }
    static exe(ref , methods, params, url){
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
        axios(request)
        .then( response => { console.log( ref , {response} ) })
        .catch( error => console.log(ref , {error}) )
    }
}

class TestProduct{
    static post(){
        let ref = "TestProduct.post"
        name = `product ${Date.now()}`
        let body = {
            name,
            "description":"santa fe 264",
            "email":`product${Date.now()}@gmail.com`,
            "phone":`3777${Date.now()}`,
            "whatsapp":`3773${Date.now()}`,
            "type":"normal",
            "price":4300,
            "category_id":1,
            "company_id":1,
            "slug":encodeURI(name)
        }
        TestProduct.exe(ref , 'post' , body)
    }
    static put(){
        let ref = "TestProduct.put"
        let body = {"id":2,"description":"talle "+String(Date.now())}
        TestProduct.exe(ref , 'put' , body)
    }
    static delete(id){
        let ref = "TestProduct.delete"
        TestProduct.exe(ref , 'delete' , {} , "/"+id)
    }
    static exe(ref , methods, params, url){
        params = typeof params == 'string' ? params : JSON.stringify(params)
        let request = {
            methods, params,
            url:API.dominio()+'products',
            responseType: 'json',responseEncoding: 'utf8',
            headers:{
                'Accept':'application/json',
                'Content-Type':'application/json'
            }
        }
        request.url = url == undefined ? request.url : request.url + url
        console.log( { request } )
        axios(request)
        .then( response => { console.log( ref , {response} ) })
        .catch( error => console.log(ref , {error}) )
    }
}

class TestFile{
    static post(){
        let ref = "TestCompany.post"
        var formData = new FormData();
        var imagefile = document.querySelector('#fileTest');
        formData.append("image", imagefile.files[0]);
        TestFile.exe( ref , 'post' , formData )
    }
    static exe(ref, methods, params, url){
        params = typeof params == 'string' ? params : JSON.stringify(params)
        let request = {
            methods, params,
            url:API.dominio()+'files',
            responseType: 'json',responseEncoding: 'utf8',
            headers:{
                'Accept':'application/json',
                'Content-Type':'application/json'
            }
        }
        request.url = url == undefined ? request.url : request.url + url
        console.log( { request } )
        axios(request)
        .then( response => { console.log( ref , {response} ) })
        .catch( error => console.log(ref , {error}) )
    }
}

const nodo = n => document.createElement(n)
let testContainer = nodo("div")
    testContainer.id = "testContainer"
    testContainer.style="top:0;background:#eee;z-index:1000;padding:1rem"
    document.body.appendChild(testContainer)
let styles = nodo("style")
    styles.innerHTML = "#testContainer>button{padding:.5rem;border:1px #333 solid;margin:.5rem}"
    testContainer.appendChild(styles)
// file
let testInputFile = nodo("input")
    testInputFile.id = "fileTest"
    testInputFile.type = "file"
    testInputFile.onchange = ()=> { TestFile.post() }
// company
let testCompanyBtnPost = nodo("button")
    testCompanyBtnPost.innerHTML = "Test company post"
    testCompanyBtnPost.onclick = ()=> { TestCompany.post() }

let testCompanyBtnPut = nodo("button")
    testCompanyBtnPut.innerHTML = "Test company put"
    testCompanyBtnPut.onclick = ()=> { TestCompany.put() }

let testCompanyBtnDelete = nodo("button")
    testCompanyBtnDelete.innerHTML = "Test company delete"
    testCompanyBtnDelete.onclick = ()=> { 
        TestCompany.delete(testCompanyInputDelete.value) 
    }
let testCompanyInputDelete = nodo()
    testCompanyInputDelete.type = "number"
    testCompanyInputDelete.id = "companyIdDelete"
    testCompanyInputDelete.placeholder = "companyIdDelete"
// Product
let testProductBtnPost = nodo("button")
    testProductBtnPost.innerHTML = "Test Product post"
    testProductBtnPost.onclick = ()=> { TestProduct.post() }

let testProductBtnPut = nodo("button")
    testProductBtnPut.innerHTML = "Test Product put"
    testProductBtnPut.onclick = ()=> { TestProduct.put() }

let testProductBtnDelete = nodo("button")
    testProductBtnDelete.innerHTML = "Test Product delete"
    testProductBtnDelete.onclick = ()=> { 
        TestProduct.delete(testProductInputDelete.value) 
    }
let testProductInputDelete = nodo()
    testProductInputDelete.type = "number"
    testProductInputDelete.id = "ProductIdDelete"
    testProductInputDelete.placeholder = "ProductIdDelete"


// set in container
// file
testContainer.appendChild(testInputFile)
// company
testContainer.appendChild(testCompanyBtnPost)
testContainer.appendChild(testCompanyBtnPut)
testContainer.appendChild(testCompanyBtnDelete)
// product
testContainer.appendChild(testProductBtnPost)
testContainer.appendChild(testProductBtnPut)
testContainer.appendChild(testProductBtnDelete)