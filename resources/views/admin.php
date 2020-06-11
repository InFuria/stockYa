<?php
	$mode =  strrpos($_SERVER['HTTP_HOST'],'dona') > 0 ? 'prod' : 'dev';
	$mode = "prod";
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

			<template>
				<v-card>
					<v-toolbar
					color="deep-purple accent-4"
					dark
					flat
					>
					<!--
						<v-app-bar-nav-icon></v-app-bar-nav-icon>
					-->
			
					<v-toolbar-title>Pedidos Goya</v-toolbar-title>

					<v-spacer></v-spacer>

					<v-btn icon>
						<v-icon>mdi-magnify</v-icon>
					</v-btn>

					<v-btn icon>
						<v-icon>mdi-dots-vertical</v-icon>
					</v-btn>

					<template v-slot:extension>
						<v-tabs
						v-model="currentItem"
						fixed-tabs
						slider-color="white"
						>
						<v-tab  v-if="sections.length == 0">Login</v-tab>
						<v-tab 
						v-for="item of sections"
						:key="item">
							{{ item }}
						</v-tab>
						</v-tabs>
					</template>
					</v-toolbar>

					<v-tabs-items v-model="currentItem">
						<v-tab-item v-if="sections.length == 0">
							<h3>Login</h3>
							<small>{{this.alertAuth}}</small>
							<v-btn @click="auth">AUTH</v-btn>
						</v-tab-item>
						<v-tab-item>
							<v-card flat>
							<v-card-text>
								<h2>content web</h2>
							</v-card-text>
							</v-card>
						</v-tab-item>
						<v-tab-item>
							<companies></companies>
						</v-tab-item>
					</v-tabs-items>
				</v-card>
				</template>
		</v-app>
	</div>

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
		<script src="https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.js"></script>

		<link href="./assets/css/materialdesignicons.min.css" rel="stylesheet">
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Material+Icons">
		';
	} ?>
	<script>

		if ('loading' in HTMLImageElement.prototype) {

			// Si el navegador soporta lazy-load, tomamos todas las imágenes que tienen la clase

			// `lazyload`, obtenemos el valor de su atributo `data-src` y lo inyectamos en el `src`.

			const images = document.querySelectorAll("img.lazyload");

			images.forEach(img => {

				img.src = img.dataset.src;

			});

		} else {

			// Importamos dinámicamente la libreria `lazysizes`

			let script = document.createElement("script");

			script.async = true;

			script.src = "https://cdnjs.cloudflare.com/ajax/libs/lazysizes/5.2.0/lazysizes.min.js";

			document.body.appendChild(script);

		}
	</script>

	
	<script src="./admin/js/api.js"></script>
	<script src="./admin/js/companiesList.js"></script>
	<script src="./admin/js/productsList.js"></script>
	<script src="./admin/js/objects.js"></script>
	<script src="./admin/components/imageUpload.js"></script>
	<script src="./admin/components/companies.js?d=<?php echo date("h:m:s")?>"></script>
	<script src="./admin/components/products.js?d=<?php echo date("h:m:s")?>"></script>
	<script src="./admin/js/main.js"></script>
	
</body>

</html>