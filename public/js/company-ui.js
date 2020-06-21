var vm, products
var _private = new WeakMap();

var dataVue = new Object({
    color:{primary:"orange darken-4"},
    componentLoadingList:[],
    show:new ShowComponents,
    messages: [
        {
            avatar: 'https://avatars0.githubusercontent.com/u/9064066?v=4&s=460',
            name: 'John Leider',
            title: 'Welcome to Vuetify.js!',
            excerpt: 'Thank you for joining our community...',
        },
        {
            color: 'red',
            icon: 'people',
            name: 'Social',
            new: 1,
            total: 3,
            title: 'Twitter',
        },
        {
            color: 'teal',
            icon: 'local_offer',
            name: 'Promos',
            new: 2,
            total: 4,
            title: 'Shop your way',
            exceprt: 'New deals available, Join Today',
        },
    ],
    lorem: 'Lorem ipsum dolor sit amet, at aliquam vivendum vel, everti delicatissimi cu eos. Dico iuvaret debitis mel an, et cum zril menandri. Eum in consul legimus accusam. Ea dico abhorreant duo, quo illum minimum incorrupte no, nostro voluptaria sea eu. Suas eligendi ius at, at nemore equidem est. Sed in error hendrerit, in consul constituam cum.'
})

for(let key of ["show","color"]){
    eval(`function ${key}(k , v){
        if(k == undefined){
            return dataVue.${key}
        };
        if(v == undefined){
            dataVue.${key}.value = v
        }else{
            dataVue.${key}[k] = v
        };
    }`)
}

function modal(k , v){
    dataVue.show.modal( k , v )
}

NavegationManager.go( "#home" );
window.onpopstate = NavegationManager.valid

vueLaunch()

function vueLaunch() {
    vm = new Vue({
        el: '#app',
        vuetify: new Vuetify,
        props: {
            source: String
        },
        data() { return dataVue },
        watch:{ 
        },
        computed: { },
        methods: {
            image(img){
                console.log({img})
                return API.route('file','open',img).url
            },
        },
        mounted(){
        }
    })
}
