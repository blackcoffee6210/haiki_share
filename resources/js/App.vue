<template>
	<div>
		<Header />
		<RouterView />
		<Footer />
	</div>
</template>

<script>

import { mapState } from 'vuex'
import Header       from "./components/Header";
import Footer       from "./components/Footer";

export default {
	name: "App",
	components: {
		Header,
		Footer
	},
	watch: {
	
	}
}
</script>
