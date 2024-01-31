import moment, { max } from "moment";
export default {
  
  data: () => ({
    min: "",
    max: "",
    current: "",
    minValue: 65,
    maxValue: 18
  }),

  mounted() {
    this.setup();
  },

  methods: {
    setup() {
      this.max = moment().subtract(this.maxValue, "years").format("YYYY-MM-DD");
      this.min = moment().subtract(this.minValue, "years").format("YYYY-MM-DD");
      this.current = moment().format("YYYY-MM-DD");      
    }
  }
}