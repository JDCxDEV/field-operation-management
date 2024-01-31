<template>
  <div id="kt_app_content_container">
    <div class="card">
      <div class="card-header border-0 pt-6">
        <div class="card-title">
          <div class="col-12 my-1">
            <div class="input-group mb-3">
              <input 
              v-model="searchQuery"
              placeholder="Search markets"
              type="text" class="form-control form-control-solid">
              <div class="input-group-append">
                <button @click="fetch('search')" type="button" class="btn btn-success">
                  Go
                </button>
              </div>
            </div>
          </div>
        </div>
        <div class="card-toolbar">
          <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
            <button type="button" class="btn btn-light-primary me-3" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
            <span class="svg-icon svg-icon-2">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z" fill="currentColor" />
              </svg>
            </span><span class="filter-text">Filter</span></button>
            <div class="menu menu-sub menu-sub-dropdown w-300px w-md-325px" data-kt-menu="true">
              <div class="px-7 py-5">
                <div class="fs-5 text-dark fw-bold">Filter Options</div>
              </div>
              <div class="separator border-gray-200"></div>
              <div class="px-7 py-5">
                <!-- <div class="mb-10">
                  <label class="form-label fs-6 fw-semibold">Status:</label>
                  <select 
                  v-model="status"
                  class="form-select form-select-solid fw-bold">
                    <option value="all">All</option>
                    <option value="active">Active</option>
                    <option value="archived">Deactivated</option>
                  </select>
                </div> -->
                <div class="mb-10">
                  <label class="form-label fs-6 fw-semibold">State:</label>
                  <select 
                  v-model="state"
                  class="form-select form-select-solid fw-bold">
                    <option value="all">All</option>
                    <option v-for="(state, index) in states" :value="state.abbreviation">{{ state.name }}</option>
                  </select>
                </div>
                <div 
                v-if="(!companyCoordinator && !marketDirector)"
                class="mb-10">
                    <label class="form-label fs-6 fw-semibold">Company:</label>
                    <select
                        v-model="company"
                        class="form-select form-select-solid fw-bold">
                        <option value="all">All</option>
                        <option v-for="company in companies" :value="company.id">{{ company.company }}</option>
                    </select>
                </div>
                <!--end::Input group-->
                <div class="d-flex justify-content-end">
                  <button 
                  @click="resetFilter()"
                  type="reset" class="btn btn-light btn-active-light-primary fw-semibold me-2 px-6" data-kt-menu-dismiss="true" data-kt-user-table-filter="reset">Reset</button>
                  <button 
                  @click="fetch('search')"
                  type="submit" class="btn btn-primary fw-semibold px-6" data-kt-menu-dismiss="true" data-kt-user-table-filter="filter">Apply</button>
                </div>
              </div>
            </div>
              <button
              v-if="canAdd && !selectedRestore.length && !selectedArchive.length" 
              @click="addMarket()"
              type="button" class="btn btn-primary">
              <span class="svg-icon svg-icon-2">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor" />
                  <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="currentColor" />
                </svg>
              </span>Add Market</button>

              <!--begin::Batch restore -->
              <div 
              v-if="selectedRestore.length"
              style="margin-right: 10px">
                <confirm-button
                :btnText="renderAlertProps().restoreAlert.text"
                :title="renderAlertProps().restoreAlert.title"
                :description="renderAlertProps().restoreAlert.description"
                confirm-button-text="Confirm"
                btn-class="btn btn-primary"
                icon="fa-solid fa-trash-undo"
                @confirmed="batchRestore()"
                ></confirm-button>	
              </div>
              <!--end::Batch restore-->

              <!--begin::Batch archive-->
              <confirm-button
              v-if="selectedArchive.length"
              :btnText="renderAlertProps().archiveAlert.text"
              :title="renderAlertProps().archiveAlert.title"
              :description="renderAlertProps().archiveAlert.description"
              confirm-button-text="Confirm"
              btn-class="btn btn-danger"
              icon="fa-solid fa-trash"
              @confirmed="batchArchive()"
              ></confirm-button>											
              <!--end::Batch archive-->

            </div>
            <div class="d-flex justify-content-end align-items-center d-none" data-kt-user-table-toolbar="selected">
              <div class="fw-bold me-5">
              <span class="me-2" data-kt-user-table-select="selected_count"></span>Selected</div>
              <button type="button" class="btn btn-danger" data-kt-user-table-select="delete_selected">Delete Selected</button>
            </div>
          </div>
        </div>
        <div class="card-body py-4">
          <div 
          :class="{'table-min-height': markets.length}"
          class="table-responsive">
            <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_users">
              <thead>
                <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                  <th class="w-10px pe-2">
                    <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                      <input 
                      @change="selectAllMarkets()"
                      :checked="selectAll"
                      class="form-check-input" type="checkbox" value="1" />
                    </div>
                  </th>
                  <th class="min-w-125px">Market</th>
                  <th class="min-w-125px">Address</th>                
                  <th class="min-w-125px">Company</th>
                  <th class="min-w-125px">User(s)</th>
                  <th class="text-end min-w-100px">Actions</th>
                </tr>
              </thead>
              <tbody class="text-gray-600 fw-semibold table-body">
                <tr v-if="markets.length" v-for="market in markets">
                  <td>
                    <div class="form-check form-check-sm form-check-custom form-check-solid">
                      <input 
                      v-model="market.selected"
                      @change="selectMarket(market)"
                      :checked="market.selected"
                      :disabled="postLoading"                    
                      class="form-check-input" type="checkbox" value="1" />
                    </div>
                  </td>
                  <td class="d-flex align-items-center no-border">
                    <div class="d-flex flex-column">
                      <a :href="`/markets/${market.id}/overview`">
                        <span class="text-gray-800 mb-1">{{ market.market_name }}</span>
                        &nbsp;
                        <span 
                        v-if="market.deleted_at"
                        class="badge badge-danger">Deactivated</span>
                      </a>
                      <span>{{ market.email }}</span>
                      <span>{{ market.phone }}</span>                    
                    </div>                      
                  </td>
                  <td class="align-items-center">
                    <span class="d-flex text-gray-800 mb-1">{{ market.address_1 }}</span>
                    <span class="d-flex  text-gray-800 mb-1">{{ market.address_2 }}</span>                    
                    <span>{{ market.city }}, {{ market.state }}, {{ market.zip }}</span>
                  </td>        
                  <td>
                    <div class="badge badge-light fw-bold">
                      <a 
                      v-if="market.company_name"
                      :href="`/companies/${market.company_id}/overview`"
                      class="text-gray-600"
                      >
                        {{ market.company_name }}
                      </a>
                      <span v-else>
                        Not assigned
                      </span>
                    </div>             
                  </td>
                  <td>{{ market.users_count }}</td>
                  <td class="text-end">
                    <div class="dropdown">
                      <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                        Action
                      </button>
                      <ul class="dropdown-menu" aria-labellezdby="dropdownMenuButton1">
                        <li>
                          <a @click="viewMarket('view', market)" class="dropdown-item" href="#">View</a>
                        </li>
                        <li>
                          <a @click="viewMarket('edit', market)" class="dropdown-item" href="#">Edit</a>
                        </li>
                        <!-- <li>
                          <confirm-button
                          type="link"
                          :btnText="renderAlertProps(market).text"
                          :title="renderAlertProps(market).title"
                          :description="renderAlertProps(market).description"
                          btn-class="dropdown-item"
                          :route="renderAlertProps(market).route"
                          @confirmed="confirmedSuccess(market)"
                          ></confirm-button>
                        </li> -->
                      </ul>
                    </div>
                  </td>
                </tr>
                <tr v-else class="odd my-10">
                    <td valign="top" colspan="6" class="text-center">No matching records found</td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="row">
            <div class="col-sm-12 col-md-4 d-flex align-items-center justify-content-center justify-content-md-start">
              <span class="text-gray-600">Total: <strong>{{ current_count || 0 }}</strong> / <strong>{{ total }}</strong></span>
            </div>
            <div class="col-sm-12 col-md-8 d-flex align-items-center justify-content-center justify-content-md-end">
              <nav aria-label="Page navigation example">
                <ul class="pagination">
                  <li class="page-item">
                    <button :disabled="!this.prev_page_url" @click="fetch(false, this.prev_page_url)" class="page-link" href="#" aria-label="Previous">
                      <span aria-hidden="true">&laquo;</span>
                    </button>
                  </li>

                  <li v-for="link in links" class="page-item" :class="{ active: link.active }">
                    <button :disabled="link.label == '...'" type="button" class="page-link" @click="fetch(false, link.url)">{{ link.label }}</button>
                  </li>

                  <li class="page-item">
                    <button :disabled="!this.next_page_url" @click="fetch(false, this.next_page_url)" class="page-link" href="#" aria-label="Next">
                      <span aria-hidden="true">&raquo;</span>
                    </button>
                  </li>
                </ul>
              </nav>
            </div>
          </div>
          
      </div>
    </div>
  </div>
  
  <modal 
  :value="showModal"
  modalsize="modal-dialog-centered mw-650px"
  @change="setModalStatus($event)"
  v-model="showModal">
    <template v-slot:title>
      {{ renderAccessType }} Market
    </template>
    <template v-slot:body>
      <market-view
      ref="marketView"
      :store-url="storeUrl"
      :companies-fetch-url="companiesFetchUrl"
      :child-component="true"
      :company-id="companyId"
      @cancel="setModalStatus(false)"
      @editing="accessType = 'edit'"  
      @updated="refresh()"      
      ></market-view>
    </template>
  </modal>

</template>
<script>
import ResponseMixins from "Mixins/response.js";
import FormatterMixins from "Mixins/formatter.js";
import StatesMixins from "Mixins/states.js";
import MarketView from "./MarketView";
export default {
  name: "market-table",
  
  components: {
      MarketView
  },

  props: {
    fetchUrl: {
        type: String,
        default: ""
    },

    storeUrl: {
        type: String,
        default: ""
    },

    canAdd: {
        type: Boolean,
        default: true
    },

    companyId: {
        type: [String, Number],
        default: ""      
    },

    companiesFetchUrl: {
        type: String,
        default: ""
    },
    enableCompanyFilter: {
        type: Boolean,
        default: true
    },
    companyCoordinator: {
        type: Number,
        default: false
    },
    marketDirector: {
        type: Number,
        default: false
    },
  },

  computed: {

    renderAccessType() {
      switch (this.accessType) {
        case "add":
          return "Add";
        case "view":
          return "View";
        case "edit":
          return "Update"
      }
    }

  },

  data: () => ({
    markets: [],
    links: [],
    prev_page_url: null,
    next_page_url: null,
    current_url: null,
    current_count: 0,
    total: 0,

    status: "active",
    state: "all",
    company: "all",
    searchQuery: "",

    showModal: false,
    accessType: "create",

    selectAll: false,

    selectedArchive: [],
    selectedRestore: [],

    postLoading: false,

    companies: [],
  }),

  mounted() {
    this.fetch();
    this.fetchCompanies();
  },

  mixins: [FormatterMixins, ResponseMixins, StatesMixins],

  methods: {

    fetchCompanies() {
        if(this.companiesFetchUrl) {
            axios.get(this.companiesFetchUrl)
            .then((res) => {
                    this.companies = res.data.companies;
            }).catch((err) => {
                    console.log(err);
            });
        }
    },
    fetch(isRefresh = null, fetchUrl = null) {
      this.current_url = fetchUrl ? `${fetchUrl}&paginate=true` : `${this.fetchUrl}?paginate=true` ;
      const params =  {
        params: { search: this.searchQuery, status: this.status,  company: this.company, state: this.state }
      };

      axios.get(`${this.current_url}`, params)
      .then((res) => {
        this.markets = res.data.data.map((market) => {
          let status = false;
          if (this.selectedArchive.indexOf(market.id) >= 0 || this.selectedRestore.indexOf(market.id) >= 0) {
            status = true;
          }
          return { ...market, phone: this.phoneFormat(market.phone), selected: status }
        });;
        this.links = res.data.links;
        this.total = res.data.total;
        this.current_count = res.data.to;
        if(this.links) {
          this.links.shift();
          this.links.pop();    
        }

        this.next_page_url = res.data.next_page_url;
        this.prev_page_url = res.data.prev_page_url;
      }).catch((error) => {
        this.parseError(error, null, {});
      }).finally(() => {
        if(isRefresh) {
          if(isRefresh != 'search') {
            toastr.success('', 'Table Refresh!');
          }else {
            this.search = '';
          }
        }
      });
    },

    setModalStatus(status) {
      this.showModal = status;
    },

    addMarket() {
      this.showModal = true;
      this.$refs.marketView.setAccessType("add");
      this.accessType = "add";
    },

    viewMarket(type, data) {
      this.showModal = true;
      this.accessType = type;
      this.$refs.marketView.setAccessType(type);
      this.$refs.marketView.fetch(data);
    },

    selectMarket(market) {
      const key = market.id;
      if(!market.deleted_at) {
        const index = this.selectedArchive.indexOf(key);
        if(index < 0) {
          this.selectedArchive.push(key);
        } else {
          this.selectedArchive.splice(index, 1);
        }
      } else {
        const index = this.selectedRestore.indexOf(key);
        if(index < 0) {
          this.selectedRestore.push(key);
        } else {
          this.selectedRestore.splice(index, 1);
        }
      }
    },

    selectAllMarkets() {
      const status = this.selectAll = !this.selectAll;
            this.markets.forEach((market) => {
                market.selected = status;
            });
            if(status) {
                this.selectedArchive = this.markets.filter(market => !market.deleted_at).map((market) => {
                    return market.id;
                });
                this.selectedRestore = this.markets.filter(market => market.deleted_at).map((market) => {
                    return market.id;
                });
            }	else {
                this.selectedArchive = [];
                this.selectedRestore = [];
            }
    },

    confirmedSuccess(market) {
      const action = market.deleted_at ? "activated" : "deactivated";
      Swal.fire(`Market ${action}`, `Market has been successfully ${action}`, "success");
      this.refresh();
    },

    refreshTable() {
      this.searchQuery = "";
      this.fetch(true);
    },    

    resetFilter() {
      this.status = "active";
      this.state = "all";
      this.company = "all";
      this.fetch();
    },

    renderAlertProps(market = null) {
      if(market) {
        return {
          text: market.deleted_at ? "Activate": "Deactivate",
          title: `${market.deleted_at ? "Activate": "Deactivate"} ${market.market_name}?`,
          description: `Are you sure to ${market.deleted_at ? "activate" : "deactivate"} <strong>${market.market_name}</strong>?`,
          route: market.deleted_at ? market.restore_url : market.delete_url
        };
      } else {
        return {
          archiveAlert: {
            text: `Deactivate (${this.selectedArchive.length}) Markets`,
            title: `Deactivate Markets?`,
            description: `
            Are you sure to deactivate selected markets?
            <br/>
            <br/>
            <i>Note: ${this.selectedArchive.length} markets will be deactivated.</i>
            `,
          },
          restoreAlert: {
            text: `Activate (${this.selectedRestore.length}) Markets`,
            title: `Activate Markets?`,
            description: `
            Are you sure to activate selected markets?
            <br/>
            <br/>
            <i>Note: ${this.selectedRestore.length} markets will be activated.</i>
            `,
          }
        };
      }
    },

    batchRestore() {
      this.postLoading = true;
      const data = {
        ids: this.selectedRestore
      };
      axios.post(`/markets/batch/restore`, data)
        .then((res) => {
          this.selectedRestore = [];
          this.selectAll = false;
          Swal.fire("Markets Activated", "Selected markets has been activated", "success");
          this.fetch();
        }).catch((error) => {
          this.parseError(error, null, {});
        }).finally(() => {
          this.postLoading = false;
        });
    },

    batchArchive() {
      this.postLoading = true;
      const data = {
        ids: this.selectedArchive
      };
      axios.post(`/markets/batch/archive`, data)
        .then((res) => {
          this.selectedArchive = [];
          this.selectAll = false;
          Swal.fire("Markets Deactivated", "Selected markets has been deactivated", "success");
          this.fetch();
        }).catch((error) => {
          this.parseError(error, null, {});
        }).finally(() => {
          this.postLoading = false;
        });
    },

    refresh() {
      this.fetch();
      this.$emit("refresh");
    }

  },
}
</script>
<style>
.no-border {
  border-bottom: none !important;
}

.modal{
  z-index: 1050 !important;   
}
.modal-backdrop{
  z-index: 103 !important;        
}

.pac-container {
  z-index: 1051 !important;
}
</style>