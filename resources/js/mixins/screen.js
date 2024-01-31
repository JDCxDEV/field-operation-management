export default {

	computed: {
		isMobileScreen() {
            try {
                if(this.screenWidth <= 425) {
                    return true;
                }
                return false;
            } catch (e) {
                return false;
            }
        }
	},

	data: () => ({
        screenWidth: 426,
	}),

	mounted() {
        this.setupScreenWidth();
	},

	methods: {
		setupScreenWidth() {
            try {
                this.screenWidth = screen.width;            
            } catch (error) {
                this.screenWidth = this.screenWidth;
            }
            window.addEventListener("resize", (event) => {
                this.screenWidth = screen.width;
            });
        }
	}
		

}