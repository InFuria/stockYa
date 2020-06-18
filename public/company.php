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
                        <v-row
                        align="center"
                        class="spacer"
                        no-gutters
                        >
                        <v-col
                            cols="4"
                            sm="2"
                            md="1"
                        >
                            <v-avatar
                            size="36px"
                            >
                            <img
                                v-if="message.avatar"
                                alt="Avatar"
                                src="https://avatars0.githubusercontent.com/u/9064066?v=4&s=460"
                            >
                            <v-icon
                                v-else
                                :color="message.color"
                                v-text="message.icon"
                            ></v-icon>
                            </v-avatar>
                        </v-col>

                        <v-col
                            class="hidden-xs-only"
                            sm="5"
                            md="3"
                        >
                            <strong v-html="message.name"></strong>
                            <span
                            v-if="message.total"
                            class="grey--text"
                            >
                            &nbsp;({{ message.total }})
                            </span>
                        </v-col>

                        <v-col
                            class="text-no-wrap"
                            cols="5"
                            sm="3"
                        >
                            <v-chip
                            v-if="message.new"
                            :color="`${message.color} lighten-4`"
                            class="ml-0 mr-2 black--text"
                            label
                            small
                            >
                            {{ message.new }} new
                            </v-chip>
                            <strong v-html="message.title"></strong>
                        </v-col>

                        <v-col
                            v-if="message.excerpt"
                            class="grey--text text-truncate hidden-sm-and-down"
                        >
                            &mdash;
                            {{ message.excerpt }}
                        </v-col>
                        </v-row>
                    </v-expansion-panel-header>

                    <v-expansion-panel-content>
                        <v-divider></v-divider>
                        <v-card-text v-text="lorem"></v-card-text>
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

	<script src="/js/api.js"></script>
	<script src="/js/objects.js"></script>
	
	<script src="/js/company-ui.js"></script>



</body>

</html>