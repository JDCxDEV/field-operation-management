export default {
	data: () => ({
		coordinates: {
			lat: "",
			long: ""
		}
	}),
	
	mounted() {
		this.setup();
	},

	methods: {
		setup() {
			navigator.geolocation.getCurrentPosition(position => {
				this.coordinates = {
					lat: position.coords.latitude,
					long: position.coords.longitude
				};
			}, err => {
				console.log("Cannot get Coordinates from navigator", err);
			}, { enableHighAccuracy: true });
		}
	}
}