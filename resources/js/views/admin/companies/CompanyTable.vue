<template>
  <div id="kt_app_content_container">
    <div class="card">
      <div class="card-header border-0 pt-6">
        <div class="card-title">
          <div class="col-12 my-1">
            <div class="input-group mb-3">
              <input 
              v-model="searchQuery"
              placeholder="Search companies"
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
                <div class="mb-10">
                  <label class="form-label fs-6 fw-semibold">State:</label>
                  <select 
                  v-model="state"
                  class="form-select form-select-solid fw-bold">
                    <option value="all">All</option>
                    <option v-for="(state, index) in states" :value="state.abbreviation">{{ state.name }}</option>
                  </select>
                </div>
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
            @click="addCompany()"
            type="button" class="btn btn-primary">
            <span class="svg-icon svg-icon-2">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor" />
                <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="currentColor" />
              </svg>
            </span>Add Company</button>
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
          :class="{'table-min-height': companies.length}" 
          class="table-responsive">
            <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_users">
              <thead>
                <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                  <th class="w-10px pe-2">
                    <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                      <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_table_users .form-check-input" value="1" />
                    </div>
                  </th>
                  <th class="min-w-125px">Company</th>
                  <th class="min-w-125px">User(s)</th>
                  <th class="min-w-125px">Market(s)</th>
                  <th class="min-w-125px">Address</th>
                  <th class="text-end min-w-100px">Actions</th>
                </tr>
              </thead>
              <tbody class="text-gray-600 fw-semibold">
                <tr v-for="company in companies">
                  <td>
                    <div class="form-check form-check-sm form-check-custom form-check-solid">
                      <input class="form-check-input" type="checkbox" value="1" />
                    </div>
                  </td>
                  <td class="d-flex align-items-center no-border">
                    <div class="d-flex flex-column">
                      <a :href="`/companies/${company.id}/overview`">
                        <span class="text-gray-800 mb-1">
                          {{ company.company }}
                          <span 
                          v-if="company.deleted_at"
                          class="badge badge-danger">Deactivated</span>                      
                        </span>
                      </a>
                      <span>{{ company.phone }}</span>
                    </div>
                  </td>
                  <td>
                    {{ company.users_count }}
                  </td>
                  <td>
                    {{ company.markets_count }}
                  </td>
                  <td class="align-items-center">
                    <span class="d-flex text-gray-800 mb-1">{{ company.address_1 }}</span>
                    <span class="d-flex  text-gray-800 mb-1">{{ company.address_2 }}</span>                    
                    <span>{{ company.city }}, {{ company.state }}, {{ company.zip }}</span>
                  </td>
                  <td class="text-end">
                    <div class="dropdown">
                      <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                        Action
                      </button>
                      <ul class="dropdown-menu" aria-labellezdby="dropdownMenuButton1">
                        <li>
                          <a @click="viewCompany('view', company)" class="dropdown-item" href="#">View</a>
                        </li>
                        <li>
                          <a @click="viewCompany('edit', company)" class="dropdown-item" href="#">Edit</a>
                        </li>
                        <!-- <li>
                          <confirm-button
                          type="link"
                          :btnText="renderAlertProps(company).text"
                          :title="renderAlertProps(company).title"
                          :description="renderAlertProps(company).description"
                          btn-class="dropdown-item"
                          :route="renderAlertProps(company).route"
                          @confirmed="confirmedSuccess(company)"
                          ></confirm-button>
                        </li> -->
                      </ul>
                    </div>
                  </td>
                </tr>
                <tr v-if="!companies.length" class="odd my-10">
                  <td valign="top" colspan="7" class="text-center">No matching records found</td>
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
      {{ renderAccessType }} Company
    </template>
    <template v-slot:body>
      <company-view
      ref="companyView"
      :store-url="storeUrl"
      :child-component="true"
      @cancel="setModalStatus(false)"
      @editing="accessType = 'edit'"  
      @updated="fetch()"  
      ></company-view>
    </template>
  </modal>

</template>
<script>
import FormatterMixins from "Mixins/formatter.js";
import ResponseMixins from "Mixins/response.js";
import StatesMixins from "Mixins/states.js";
import CompanyView from "./CompanyView";

export default {
  name: "company-table",
  
  components: {
    CompanyView
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
    companies: [],
    links: [],
    prev_page_url: null,
    next_page_url: null,
    current_url: null,
    current_count: 0,
    total: 0,

    searchQuery: "",
    status: "active",
    state: "all",    
  
    showModal: false,
    accessType: "create"
  }),

  mounted() {
    this.fetch();
  },

  mixins: [FormatterMixins, ResponseMixins, StatesMixins],

  methods: {

    fetch(isRefresh = null, fetchUrl = null) {
      this.current_url = fetchUrl ? `${fetchUrl}&paginate=true` : `${this.fetchUrl}?paginate=true` ;
      const params =  {
        params: { search: this.searchQuery, status: this.status, state: this.state }
      };

      axios.get(`${this.current_url}`, params)
      .then((res) => {
        this.companies = res.data.data.map((company) => {
          return { ...company, phone: this.phoneFormat(company.phone) };
        });
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

    addCompany() {
      this.showModal = true;
      this.accessType = "add";
      this.$refs.companyView.setAccessType("add");
    },

    async viewCompany(type, data) {
      this.showModal = true;
      this.accessType = type;
      this.$refs.companyView.setAccessType(type);
      this.$refs.companyView.fetch(data);
    },

    confirmedSuccess(company) {
      const action = company.deleted_at ? "activated" : "deactivated";
      Swal.fire(`Company ${action}`, `Company has been successfully ${action}`, "success");
      this.fetch();
    },

    refresh() {
      this.searchQuery = "";
      this.fetch(true);
    },

    resetFilter() {
      this.status = "active";
      this.state = "all";
      this.fetch();
    },

    renderAlertProps(company) {
      return {
        text: company.deleted_at ? "Activate": "Deactivate",
        title: `${company.deleted_at ? "Activate": "Deactivate"} ${company.company}?`,
        description: `Are you sure to ${company.deleted_at ? "activate" : "deactivate"} <strong>${company.company}</strong>?`,
        route: company.deleted_at ? company.restore_url : company.delete_url
      };
    },

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
  z-index: 100 !important;        
}

.pac-container {
  z-index: 1051 !important;
}
</style>