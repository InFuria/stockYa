<div>Hola frontend</div>
<button onclick="openTest('productList')">productlist</button>
<div>
    PRODUCT UPDATE
    <label>id<input id="idupdate"></label>
    <label>key<input id="keyupdate"></label>
    <label>value<input id="valueupdate"></label>
    <button onclick="openTest('productUpdate')">UPDATE</button>

</div>
<hr>
<output id="out"></output>
<script>
    class Test{
        constructor(){
            this.getdata = null
            this.out = document.querySelector("#out")
        }
        productList(){
            fetch('http://kaizen-donarosa.com/api/products')
                .then( async response => {
                    this.getdata = await response.json()
                    this.print()
                }).catch(function (error) {
                console.log(error.toJSON());
            });
        }
        productUpdate(){
            let val = document.querySelector("#valueupdate").value
            let key = document.querySelector("#keyupdate").value
            let id = document.querySelector("#idupdate").value

            let send = {}
            send[key] = val
            let url = 'https://kaizen-donarosa.com/api/products/'+id
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
                console.log(error.toJSON());
            });
        }
        print(){
            this.out.innerHTML = JSON.stringify(this.getdata)
        }
    }

    let test = new Test
    function openTest(name){
        test[name]()
    }
</script>
