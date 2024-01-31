<template>
    <div class="input-search-container">
        <input v-model="searchText" @focus="focusEvent" @input="search" :disabled="disabled" class="form-control mb-2" :placeholder="placeholder">
        <div 
        v-if="fetching"
        class="icon-loading-container">
            <i class="fa-solid fa-spinner fa-spin"></i>
        </div>
    </div>
    <div style="position: relative">
        <div 
        class="card list-container"
        @focusout="handleFocusOut"
        tabIndex="0"
        v-if="selectableItems.length && openList">
            <template v-for="item in selectableItems">
                <span 
                @click="select(item)"
                class="d-flex py-2" style="cursor: pointer">
                {{ item[label] }}
                </span>
                <div class="separator border-gray-200"></div>
            </template>
        </div>
    </div>
    <div v-if="selectedItems.length" class="mb-2">
        <span class="badge badge-primary selected-item" v-for="item in selectedItems">
        {{  item[label] }}
        <i
        v-if="!disabled" 
        @click="remove(item)"
        class="remove-icon fa-regular fa-circle-xmark text-warning"></i>
        </span>&nbsp;
    </div>
</template>
<script>
import ResponseMixins from "Mixins/response.js";
import { debounce } from "debounce";

export default {
    name: "search-select",
    props: {
        fetchUrl: {
            type: String,
            default: ""
        },

        label: {
            type: String,
            default: "name"
        },

        value: {
            type: String,
            default: "id"
        },

        preselected: {
            type: Array,
            default: []
        },

        disabled: {
            type: Boolean,
            default: false
        },

        placeholder: {
            type: String,
            default: "Start searching"           
        }
    },

    computed: {
        selectedItems() {
            if(this.selected.length) {
                let selected = this.preSelected;
                selected = selected.concat(this.selected);
                return selected;
            } else {
                return this.preSelected;
            }
            return [];
        },

        selectableItems() {
            return this.items.filter((item) => {
                const ids = this.selectedItems.map((i) => {
                    return i[this.value];
                });
                if(ids.indexOf(item[this.value]) >= 0) {
                    return false;
                }
                return true;
            });
        }
    },

    data: ()  => ({
        items: [],
        selected: [],
        preSelected: [],
        searchText: "",

        fetching: false,
        openList: true,
    }),

    watch: {
        preselected() {
            this.preSelected = this.preselected;
        }
    },

    mounted() {
        this.refresh();
    },

    mixins: [ ResponseMixins ],

    methods: {
        search: debounce(function(e) {
            if(e.target.value.length >= 3) {
                this.fetch(e.target.value);
            }
        }, 500),

        fetch(search) {
            this.fetching = true;
            axios.get(`${this.fetchUrl}&search=${search}`)
                .then((res) => {
                    this.items = res.data;
                    this.openList = true;
                }).catch((err) => {
                    this.parseError(error, null, {});
                }).finally((err) => {
                    this.fetching = false;
                });
        },

        select(item) {
            this.selected.push(item);
            this.change();
        },

        remove(item) {
            const key = item[this.value];

            const selected = this.selected.map((item) => {
                return item[this.value];
            });

            if(selected.indexOf(key) >= 0) {
                const index = selected.indexOf(key);
                this.selected.splice(index, 1);
                this.change();
            }

            const preSelected = this.preSelected.map((item) => {
                return item[this.value];
            });
            
            if(preSelected.indexOf(key) >= 0) {
                const index = preSelected.indexOf(key);
                this.preSelected.splice(index, 1);
                this.change();
            }
        },

        change() {
            this.$emit("change", this.selectedItems);
        },

        focusEvent() {
            if(this.searchText && this.searchText.length >= 3) {
                this.fetch(this.searchText);
            }
        },

        refresh() {
            this.items = [];
            this.selected = [];
            this.preSelected = [];
            this.searchText = "";
        },

        handleFocusOut() {
            this.openList = false;
        }
    },
}
</script>
<style>
.input-search-container {
    position: relative;
}
.icon-loading-container {
    position: absolute;
    top: 13px;
    right: 13px;
}
.list-container {
    width:100%; 
    position: absolute; 
    z-index: 1000;
    display: block;
    border: 1px solid #7E8299;
    padding: 10px;
    height: auto;
    max-height: 200px;
    overflow: scroll;
}
.selected-item {
    margin-right: 5px; 
    margin-bottom: 3px; 
    font-size: 11px;
}
.remove-icon {
    cursor: pointer; 
    margin-left: 3px;

}
</style>