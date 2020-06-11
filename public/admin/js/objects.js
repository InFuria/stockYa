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
