const Counter = {
	data() {
		return {
			screen: 0,
		};
	},
	methods: {
		setTab(screen) {
			this.screen = screen;
		},
	},
};

Vue.createApp(Counter).mount("#wizard-root");
