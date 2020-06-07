<html><head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"></head><body><div>Hola frontend</div>
<h3>Products</h3>
<hr>
<button onclick="openTest('productList')">productlist</button>
<hr>
<div>
    PRODUCT UPDATE
    <label>id<input id="idupdate"></label>
    <label>key<input id="keyupdate"></label>
    <label>value<input id="valueupdate"></label>
    <button onclick="openTest('productUpdate')">UPDATE</button>

</div>
<hr>
<div>
    PRODUCT POST
    <div id="valuepost" contenteditable="">{
        "slug":"mesa-de-roble",
        "name":"mesa de roble",
        "description":"enteramente de roble, 2metros de largo x 1 metro de alto",
        "type":"normal",
        "image":"",
        "price":"4300",
        "category_id":1,
        "company_id":1
        }</div>
    <button onclick="openTest('productPost')">POST</button>

</div>
<hr>
<button onclick="openTest('auth')">AUTH</button>
<button onclick="openTest('users')">USERS</button>
<hr>
<div>
    PRODUCT POST
    <input id="file" type="file">
    <button onclick="openTest('filePost')">POST</button>
</div>
<hr>
<output id="out"></output>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    var token
    class Test{
        constructor(){
            this.getdata = null
            this.out = document.querySelector("#out")
        }
        productList(){
            let configRequest = {
                'method':'GET',
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token.token}`
            }
            console.log({configRequest})
            fetch('../api/products', configRequest)
                .then( async response => {
                    console.log({response})
                    this.getdata = await response.json()
                    this.print()
                }).catch(function (error) {
                console.log({error});
            });
        }

        productPost(){
            let val = JSON.parse(document.querySelector("#valuepost").innerHTML.trim())
            let url = '../api/products'
            console.log({url}, val)
            fetch(url, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token.token}`
                },
                body: JSON.stringify(val)
            }).then( async response => {
                this.getdata = await response.json()
                this.print()
            }).catch(function (error) {
                console.log({error});
            });
        }

        auth(){
            let val = {"username":"ely.admin" , "password":"undertale"}
            let url = '../api/auth/login'
            console.log({url}, val)
            fetch(url, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(val)
            }).then( async response => {
                this.getdata = await response.json()
                token = this.getdata
                this.print()
            }).catch(function (error) {
                console.log({error});
            });
        }

        users(){
            let configRequest = {
                'method':'GET',
                'headers': {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token.token}`,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }
            fetch('../api/auth/user', configRequest)
                .then( async response => {
                    console.log(response);
                    console.log({response})
                    this.getdata = await response.json()
                    this.print()
                }).catch(function (error) {
                console.log({error});
            });
        }

        productUpdate(){
            let val = document.querySelector("#valueupdate").value
            let key = document.querySelector("#keyupdate").value
            let id = document.querySelector("#idupdate").value

            let send = {}
            send[key] = val
            let url = '../api/products/'+id
            console.log({url}, JSON.stringify(send))
            fetch(url, {
                method: 'PUT',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(send)
            }).then( async response => {
                this.getdata = await response.json()
                this.print()
            }).catch(function (error) {
                console.log({error});
            });
        }
        filePost(){
            var formData = new FormData();
            var imagefile = document.querySelector('#file');
            formData.append("image", imagefile.files[0]);
            axios.post('../api/files', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            }).then(function (res) {
                console.log(res , 'SUCCESS!!');
            })
                .catch(function (err) {
                    console.log({err:err.message});
                });
        }
        valuepostreset(){
            document.querySelector("#valuepost").innerHTML = `{
            "slug":"mesa-de-roble",
            "name":"mesa de roble",
            "description":"enteramente de roble, 2metros de largo x 1 metro de alto",
            "type":"normal",
            "image":"",
            "price":"4300",
            "category_id":"1",
            "company_id":"1"
          }`
        }
        print(){
            this.out.innerHTML = JSON.stringify(this.getdata)
        }
    }

    let test = new Test
    test.valuepostreset()
    function openTest(name){
        test[name]()
    }
</script></body>
