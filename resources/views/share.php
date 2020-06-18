<?php
	$mode =  strrpos($_SERVER['HTTP_HOST'],'192') > 0 || strrpos($_SERVER['HTTP_HOST'],'dona') > 0 ? 'prod' : 'dev';
	$mode = 'prod';
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, minimal-ui">
	<!-- op graph -->
	<meta property="og:type" content="<?php echo $type ?>" />
    <meta property="og:title" content="<?php echo $title ?>" />
    <meta property="og:description" content="<?php echo $description ?>" />
    <meta property="og:image" content="<?php echo $image ?>" />
    <meta property="og:url" content="<?php echo $url ?>" />
    <meta property="og:site_name" content="<?php echo $site_name ?>" />
	<!--
    <meta property="article:published_time" content="2013-09-17T05:59:00+01:00" />
	<meta property="article:modified_time" content="2013-09-16T19:08:47+01:00" />
	-->
	<meta property="article:published_time" content="<?php echo $date_post ?>" />
	<meta property="article:modified_time" content="<?php echo $date_update ?>" />
	<meta property="article:section" content="<?php echo $section ?>" />
	<meta property="article:tag" content="<?php echo $tag ?>" />
	<meta property="fb:admins" content="<?php echo $facebook_id ?>" />
    <!-- twitter -->
    <meta name="twitter:title" content="<?php echo $title ?>" />
    <meta name="twitter:description" content="<?php echo $description ?>" />
    <meta name="twitter:image" content="<?php echo $image ?>" />
    <meta name="twitter:site" content="<?php echo $twitter_creator ?>">
    <meta name="twitter:creator" content="<?php echo $twitter_creator ?>">
	<!-- Twitter summary card with large image. Al menos estas medidas 280x150px -->
	<meta name="twitter:image:src" content="<?php echo $image_little ?>">
</head>

<body id="body">
	<div id="loading">
		<div style="position:absolute;top:calc(50% - 10rem);color:#fff;width:100vw;text-align:center">PedidosGoya</div>
		<span>Comidas</span>
		<span>Postres</span>
		<span>Indumentaria</span>
	</div>
	<div style="padding:1rem;top:0;position:fixed;z-index:100" id="instructions"></div>
	<div id="app">
		<v-app id="inspire">
			<client></client>
			<v-app-bar app clipped-right :color="color.primary" dark>
				<v-btn icon @click.stop="show.modal('categories', 'true')">
					<v-icon>mdi-store</v-icon>
				</v-btn>
				<v-spacer></v-spacer>
					<v-img style="max-width:140px;max-height:140px;" src="./assets/img/icon.png?d=<?php echo date("hms")?>"></v-img>
				<v-spacer></v-spacer>
				<v-btn icon @click.stop="show.modal('cart', 'true')">
					<v-icon>mdi-cart</v-icon>
				</v-btn>
			</v-app-bar>

			<categories></categories>

			<v-main>
				<v-container class="fill-height pa-0" fluid >
					<v-row justify="center">
						<v-card style="width:100%;position:relative;min-height:100vh">
							<v-container>
								<v-row v-bind:class="[$vuetify.breakpoint.xsOnly ? 'flex-column' : '']">
									<v-col xs="12" sm="12" md="6">
										<!-- add mic append-icon="mic" -->
										<v-text-field 
											@focus="showCategories=true" 
											@keydown.enter="$event.target.blur();showCategories=false" 
											prepend-inner-icon="search" class="mx-4" flat hide-details label="BUSCAR" v-model="search"></v-text-field>
										<div v-if="showCategories" style="z-index:2;background-color:rgba(255,255,255,.65);position:absolute;width:80vw;margin-left:10vw;left:0">
											<div>Categorias productos</div>
											<v-btn x-small class="ma-1"
												v-for="(item, i) in categoriesProducts" :key="i"
												@click="search=item.name"
											>
												{{ item.name }}
											</v-btn>
										</div>
									</v-col>
									<categories-slider @search="setSearch" class="col" xs="12" sm="12"></categories-slider>
								</v-row>
							</v-container>
							<v-container fluid>
								<v-row dense>
									<v-col cols="12">
										<v-card :color="color.primary" dark style="border-radius:0">
											<v-card-title v-if="search == 'ofertas'" class="headline"> ¡ Ofertas y Promos ! </v-card-title>
											<v-card-subtitle v-if="search == 'ofertas'"> Productos </v-card-subtitle>
											
											<v-card-title v-if="show.company" class="headline"> Productos </v-card-title>
											
											<v-card-text v-if="show.company">
												<v-row class="d-flex justify-center">
													<v-col xs="12" sm="12" md="4" lg="3">
														<v-img :src="image(companyView.image[0])"></v-img>
														<div v-bind:class="[$vuetify.breakpoint.xsOnly ? 'flex-wrap' : '','pa-1 d-flex']" v-bind:style="[$vuetify.breakpoint.mdOnly || $vuetify.breakpoint.lgOnly ? 'margin-top: -2.5rem;' : '']" style="background-color:rgba(0,0,0,0.4);width:100%;position:relative;">
															<v-btn text @click="show.map=!show.map" small>{{companyView.address}}</v-btn>
															<v-divider></v-divider>
															<v-btn v-if="false" text small class="yellow--text"><v-icon>mdi-star</v-icon>{{String(companyView.score)}}</v-btn>
															<v-btn text small v-if="companyView.delivery > 0"><v-icon>mdi-truck</v-icon>Delivery</v-btn>
														</div>
													</v-col>
													<v-col v-if="show.map" xs="12" sm="12" md="4" lg="4">
														<iframe v-if="show.map" :src="companyView.address" frameborder="0" v-bind:style="[$vuetify.breakpoint.mdOnly || $vuetify.breakpoint.lgOnly ? 'height:280px':'height:100vh','border:0;width:100%']" allowfullscreen="" aria-hidden="false" tabindex="0"></iframe>
													</v-col>
													<v-col xs="12" sm="12" md="6" lg="6">
														<template v-if="companyView.delivery > 0">
															<v-card-title>Delivery ($ {{companyView.delivery}})</v-card-title>
														</template>
														<template v-if="companyView.phone > 0 || companyView.whatsapp > 0">
															<v-card-title v-if="companyView.whatsapp > 0">																
																<a class="mx-5" 
																	:href="'https://wa.me/54'+companyView.whatsapp+'?text=Desde http://pedidosgoya.com/ Deseo ordenar _'"
																>
																	<v-btn class="white red--text">Whatsapp: {{companyView.whatsapp}}</v-btn>
																</a>
															</v-card-title>
															<v-card-title v-if="companyView.phone > 0">
																<a class="mx-5" 
																	:href="'tel:54'+companyView.phone"
																>
																	<v-btn class="white red--text">Telefono: {{companyView.phone}}</v-btn>
																</a>
															</v-card-title>
														</template>
														<v-card-title>Horarios</v-card-title>
														<v-chip-group active-class="white--text" column >
															{{ companyView.attention_hours }}
														</v-chip-group>
													</v-col>
												</v-row>
												<v-divider class="my-5"></v-divider>
												<h2 class="white orange--text pa-3">Productos</h2>
											</v-card-text>
											<v-card-actions v-if="search.search('ofertas') == 0">
												<!-- <v-btn text>Ver todas</v-btn> -->
											</v-card-actions>
											<gallery v-if="show.gallery" @productview="setProductView"></gallery>
										</v-card>
									</v-col>
									<product v-if="show.product" @search="setSearch"></product>

									<v-col v-if="false" cols="12" xs="12" sm="12" md="6" lg="6" v-for="(item, i) in galleryItems" :key="i">
										<v-card :color="item.color" dark>
											<div>
												<v-img :src="item.src" contain></v-img>
												<div>
													<v-card-title class="headline" v-text="item.title"></v-card-title>

													<v-card-subtitle v-text="item.details"></v-card-subtitle>
												</div>
											</div>
										</v-card>
									</v-col>
								</v-row>
							</v-container>
						</v-card>
					</v-row>
				</v-container>
			</v-main>

			<cart v-if="show.cart" @productview="setProductView"></cart>
			<v-btn @click="reset">reset</v-btn>
			<v-footer app color="blue-grey" class="white--text" v-if="footerViewer">
				<span>Daniel Garcia (danielgarcia.clases@gmail.com)</span>
				<v-spacer></v-spacer>
				<span>&copy; 2020</span>
			</v-footer>
			<div v-if="false" @click="show.modal('home','false')" class="v-overlay theme--dark" style="z-index: 6;"><div class="v-overlay__scrim" style="opacity: 0.46; background-color: rgb(33, 33, 33); border-color: rgb(33, 33, 33);"></div><div class="v-overlay__content"></div></div>
		</v-app>
	</div>

	<style>
		#loading {
			position: fixed;
			width: 100vw;
			height: 100vh;
			background-color: #333;
			z-index: 1;
			display: flex;
			justify-content: center;
			align-items: center;
			transition: .5s all;
			left: 0;
			top: 0
		}

		#loading>span {
			display: flex;
			width: 10rem;
			height: 10rem;
			top: calc(50% - 5rem);
			justify-content: center;
			align-items: center;
			color: #eee;
			animation: loading 3s ease-in-out infinite;
			border-radius: 50%;
			position: absolute;
			opacity: 0;
			left: 0;
			font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
			letter-spacing: 2px;
			z-index: 1;
			text-transform: uppercase;
		}

		#loading>span:nth-child(2) {
			background-color: #c44;
		}

		#loading>span:nth-child(3) {
			background-color: #3a3;
			animation-delay: 1s;
			z-index: 2;
		}

		#loading>span:nth-child(4) {
			background-color: #33c;
			animation-delay: 2s;
			z-index: 3;
		}

		#loading.off {
			top: 100vh
		}

		@keyframes loading {
			0% {
				opacity: 1;
				z-index: 5
			}

			30% {
				left: calc(50% - 5rem);
			}

			70% {
				left: calc(50% - 5rem);
				opacity: 1;
				z-index: 1
			}

			100% {
				left: 100%;
				opacity: 0;
			}
		}
	</style>
	<script>

		if ('loading' in HTMLImageElement.prototype || 1 == 1) {

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
	<script src="/components/gallery.js"></script>
	<script src="/components/product.js"></script>
	<script src="/components/cart.js"></script>
	<script src="/components/client.js"></script>
	<script src="/components/categories.js"></script>
	<script src="/components/categories-slider.js"></script>
	
	<script src="/js/main.js"></script>



</body>

</html>