<?php
	$mode =  strrpos($_SERVER['HTTP_HOST'],'192') > 0 || strrpos($_SERVER['HTTP_HOST'],'dona') > 0 ? 'prod' : 'dev';
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, minimal-ui">
</head>

<body id="body">
	<div style="padding:1rem;top:0;position:fixed;z-index:100" id="instructions"></div>
	<div id="app">
		<v-app id="inspire">
            <v-container fluid>
                <v-row justify="center">
                <h1>Pedidos</h1>
                <v-subheader>....</v-subheader>

                <v-expansion-panels popout>
                    <v-expansion-panel
                    v-for="(message, i) in messages"
                    :key="i"
                    hide-actions
                    >
                    <v-expansion-panel-header>
                        <v-row align="center" class="spacer" no-gutters>
                            <v-col v-if="false"
                                cols="4" sm="2" md="1"
                            >
                                <v-avatar size="36px" >
                                    <v-icon
                                        :color="message.color"
                                        v-text="message.icon"
                                    ></v-icon>
                                </v-avatar>
                            </v-col>

                            <v-col sm="5" md="3">
                                <strong v-html="message.client_name"></strong>
                                <span v-if="message.total" class="grey--text hidden-xs-only">
                                    $ {{ message.total }}
                                </span>
                            </v-col>

                            <v-col class="text-no-wrap" cols="5" sm="3">
                                <v-chip
                                    v-if="true"
                                    :color="`${message.color} lighten-4`"
                                    class="ml-0 mr-2 black--text"
                                    label
                                    small
                                >
                                <v-icon
                                    :color="message.status=='1' ? 'blue' : 'red'"
                                >local_offer</v-icon>
                                </v-chip>
                                <strong class="hidden-xs-only">{{message.status=='1' ? 'Listo' : 'Revisar'}}</strong>
                            </v-col>

                            <v-col
                                v-if="message.address"
                                class="grey--text text-truncate hidden-sm-and-down"
                            >
                                &mdash;
                            </v-col>
                        </v-row>
                    </v-expansion-panel-header>

                    <v-expansion-panel-content>
                        <v-divider></v-divider>
                        <v-card-text>
                            <ul>
                                <li>Para: {{ message.client_name }}</li>
                                <li>Total: {{ message.total }}</li>
                                <li>Dirección: {{ message.address }}</li>
                                <li>Delivery {{ (message.delivery=='1') }}</li>
                                <li>Telefono: {{ message.phone }}</li>
                                <li>Texto: {{ message.text }}</li>
                                <li>Solicitado: {{ message.updated_at }}</li>
                            </ul>
                            <h3>Productos</h3>
                            <ul>
                                <li v-for="product of message.products">[{{product.quantity}}] {{product.detail.name}}</li>
                            </ul>
                            <v-btn @click="confirm(message.id)" class="white--text green">Confirmar</v-btn>
                        </v-card-text>
                    </v-expansion-panel-content>
                    </v-expansion-panel>
                </v-expansion-panels>
                </v-row>
            </v-container>
		</v-app>
	</div>
	<script>
		
		function serverData(){
		    return {"search":"<?php echo $search ?>"}
        }
        
	</script>
	<script src="https://cdn.jsdelivr.net/npm/axios@0.12.0/dist/axios.min.js"></script>
	<?php if($mode == 'prod'){ echo '
		<link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900" rel="stylesheet">
		<link href="https://cdn.jsdelivr.net/npm/@mdi/font@4.x/css/materialdesignicons.min.css" rel="stylesheet">
		<link href="https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.min.css" rel="stylesheet">

		<script src="https://cdn.jsdelivr.net/npm/vue@2.x/dist/vue.js"></script>
		<script src="https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.js"></script>

		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Material+Icons">
	';}
	else{ echo '
		<link href="./assets/css/css.css" rel="stylesheet">
		<link href="./assets/css/css1.css" rel="stylesheet">
		<link href="./assets/css/vuetify.min.css" rel="stylesheet">

		<script src="./assets/vue/vue-resource.min.js"></script>
		<script src="./assets/vue/vue.js"></script>
		<script src="./assets/vue/vuetify.min.js"></script>

		<link href="./assets/css/materialdesignicons.min.css" rel="stylesheet">
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Material+Icons">
		';
	} ?>

	<script src="/orders/js/api.js"></script>
    <script src="/orders/js/objects.js"></script>
    <script src="/orders/js/main.js"></script>
	



</body>

</html>