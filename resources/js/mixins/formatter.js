export default {

  methods: {

    clean(input) {
      if (input) {
        return input.replace(/\D/g, "");        
      }
      return "";
    },

    alphabetStringOnly(event) {
      let value = event.target.value;
      if (value) {
        return value.replace(/[^a-z ]+/i, "");    
      }
      return "";
    },

    phoneFormat(input) {
      if (!input) return; 
      if (input.length < 10) {
        return input.replace(/\D/g, "");
      } else if (input.length === 10) {
        const _input = this.formatNumber(input);
        return _input;
      } else {
        return input;
      }
    },

    formatNumber(value) {
      if (value) {
        return value.replace(/(\d{3})(\d{3})(\d{4})/g, `($1) $2-$3`);
      }
    },

    ssnFormat(input) {
      if (!input) return; 
      if (input.length < 9) {
        return input.replace(/\D/g, "");
      } else if (input.length === 9) {
        const _input = this.formatSSN(input);
        return _input;
      } else {
        return input;
      }
    },

    formatSSN(value) {
      if (value) {
        return value.replace(/(\d{3})(\d{2})(\d{4})/g, `$1-$2-$3`);
      }
    },

    currencyFormat(input) {
      if (!input) return;
      input = parseInt(this.clean(input.toString()));
      return input.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,').slice(0, -3);
    },

    ccFormat(input) {
      let value = input.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
      let matches = value.match(/\d{4,16}/g);
      let match = matches && matches[0] || '';
      let parts = [];

      for (let i = 0, len = match.length; i < len; i += 4 ) {
        parts.push(match.substring(i, i + 4));
      }

      if (parts.length) {
        return parts.join('-');
      } else {
        return input;
      }      
    },

    uniqueId(length = 10) {
      let result = '';
      const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
      const charactersLength = characters.length;
      let counter = 0;
      while (counter < length) {
        result += characters.charAt(Math.floor(Math.random() * charactersLength));
        counter += 1;
      }
      return result;
    }
  }

}