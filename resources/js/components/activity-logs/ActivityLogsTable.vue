<template>
  <div id="kt_app_content_container">
    <div class="card">
      <div class="card-header border-0 pt-6">
        <div class="col-12 pt-3">
          <div class="row">
            <div class="col-md-3">
              <div class="">
                <input readonly class="form-control form-control-solid" placeholder="Pick date range" id="activityLogDateRange"/>
              </div>
            </div>
            <div class="col-md-3">
              <div class="input-group">
                <input 
                v-model="searchQuery"
                placeholder="Search activities"
                type="text" class="form-control form-control-solid">
                <div class="input-group-append">
                  <button @click="fetch('search')" type="button" class="btn btn-success">
                  Go
                  </button>
                </div>
              </div>
            </div>
            <div class="offset-4 col-md-2">
              <div 
              v-if="!noFilter"
              class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                <button type="button" class="btn btn-light-primary me-3" data-kt-menu-static="true" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                  <span class="svg-icon svg-icon-2">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z" fill="currentColor" />
                    </svg>
                  </span>Filter
                </button>
                <div class="menu menu-sub menu-sub-dropdown w-400px w-md-425px" data-kt-menu="true">
                  <div class="px-7 py-5">
                    <div class="fs-5 text-dark fw-bold">Filter Options</div>
                  </div>
                  <div class="separator border-gray-200"></div>
                  <div class="px-7 py-5">
                    <div class="mb-10">
                      <label class="form-label fs-6 fw-semibold">Event Type:</label>
                      <select 
                      v-model="event"
                      class="form-select fw-bold">
                        <option value="all">All</option>
                        <option v-for="event_type in event_types" :value="event_type.value">{{ event_type.label }}</option>
                      </select>
                    </div>
                    <div class="mb-10">
                      <label class="form-label fs-6 fw-semibold">Location:</label>
                      <select 
                      v-model="location"
                      class="form-select fw-bold">
                        <option value="all">All</option>
                        <option v-for="location in locations" :value="location.value">{{ location.label }}</option>
                      </select>
                    </div> 
                    <div 
                    v-if="!userId"
                    class="mb-10">
                      <label class="form-label fs-6 fw-semibold">Users:</label>
                      <select 
                      v-model="user"
                      class="form-select fw-bold">
                        <option value="all">All</option>
                        <option v-for="user in users" :value="user.id">{{ user.name }}</option>
                      </select>
                    </div>               
                    <div class="d-flex justify-content-end">
                      <button
                      @click="resetFilters()" 
                      type="reset" class="btn btn-light btn-active-light-primary fw-semibold me-2 px-6" data-kt-menu-dismiss="true" data-kt-user-table-filter="reset">Reset</button>
                      <button 
                      @click="filter()"
                      type="submit" class="btn btn-primary fw-semibold px-6" data-kt-menu-dismiss="true" data-kt-user-table-filter="filter">Apply</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>            
        </div>
        </div>
        <div class="card-body py-4">
          <div class="table-responsive">
            <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_users">
              <thead>
                <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                  <th class="min-w-125px">Log ID</th>
                  <th class="min-w-125px">Location</th>
                  <th class="min-w-125px">Description</th>
                  <th class="min-w-125px">Event</th>
                  <th class="min-w-125px">User</th>
                  <th class="min-w-125px">Logged At</th>
                  <!-- <th class="text-center min-w-100px">Actions</th> -->
                </tr>
              </thead>
              <tbody class="text-gray-600 fw-semibold">
                <tr v-for="log in logs">
                  <td>
                    <label v-if="!canView(log)">#{{ log.id }}</label>
                    <a v-else class="cursor-pointer text-primary" @click="viewLog(log)">#{{ log.id }}</a>
                  </td>
                  <td>{{ log.location }}</td>
                  <td>{{ log.description }}</td>
                  <td><span :class="log.event_type['class']">{{ log.event_type["label"] }}</span></td>
                  <td><span :class="{'badge badge-primary': log.causer_type == 'user', 'badge badge-info': log.causer_type == 'system'}">{{ log.causer_name }}</span></td>
                  <td>{{ log.logged_at }}</td>
                </tr>
                <tr v-if="!logs.length" class="odd my-10">
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

  <div
    id="activityDrawer"
    class="bg-body"
    data-kt-drawer="true"
    data-kt-drawer-activate="true"
    data-kt-drawer-toggle="#activityClose"
    data-kt-drawer-close="#activityClose"
    data-kt-drawer-overlay="true"
    data-kt-drawer-width="{default:'50%'}"
  >
    <div class="card shadow-none border-0 rounded-0" style="width: 100%">
      <div class="card-header" id="activityHeader">
        <h3 class="card-title fw-bold text-dark">View Log Details </h3>
        <div class="card-toolbar">
          <button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5" id="activityClose">
            <!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
            <span class="svg-icon svg-icon-1">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
                <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor" />
              </svg>
            </span>
            <!--end::Svg Icon-->
          </button>
        </div>
      </div>
      <div class="card-body position-relative" id="kt_activities_body">
        <div class="position-relative me-n5 pe-5">
          <div>
            <label>Original Data</label>
            <pre :class="{'changes-light': (currentMode == 'dark'), 'changes-dark': (currentMode == 'light')}">{{ selectedLog ? selectedLog?.properties['old']: '' }}</pre>
          </div>
          <div class="separator border-gray-200"></div>      
          <div class="mt-3">
            <label>Updated Data</label>      
            <pre :class="{'changes-light': (currentMode == 'dark'), 'changes-dark': (currentMode == 'light')}">{{ selectedLog ? selectedLog?.properties['attributes']: '' }}</pre>
          </div>          
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import ResponseMixins from "Mixins/response.js";

export default {
  name: "activity-logs-table",

  props: {
    fetchUrl: {
      type: String,
      default: ""
    },

    currentMode: {
      type: String,
      default: "light"      
    },
    
    userId: {
      type: [String, Number],
      default: 0   
    },

    noFilter: {
      type: Boolean,
      default: false,
    },
  },

  computed: {
    canSearch() {
      if(this.searchQuery.length === 0) {
        return true;
      } else if (this.searchQuery.length >= 3) {
        return true;
      }
      return false;
    }
  },

  data: () => ({
    logs: [],
    links: [],
    prev_page_url: null,
    next_page_url: null,
    current_url: null,
    current_count: 0,
    total: 0,

    selectedLog: "",

    event: "all",
    location: "all",
    user: "all",
    createdAt: "",
    searchQuery: "",

    event_types: [
      { value: "created", label: "Created" },
      { value: "updated", label: "Updated" },
      { value: "deleted", label: "Deleted" },
      { value: "restored", label: "Restored" },
      { value: "logged-in", label: "Logged-in" },            
    ],

    locations: [
      { value: "BlacklistedAddress", label: "Blacklisted Address" },
      { value: "Company", label: "Company" },
      { value: "Form", label: "Agent Submission Form" },
      { value: "Market", label: "Market" },
      { value: "User", label: "User" },            
    ],

    users: [],
    drawer: ""
  }),

  mounted() {
    KTDrawer.createInstances();
    var drawerElement = document.querySelector("#activityDrawer");
    this.drawer = KTDrawer.getInstance(drawerElement);
    
    this.initializeDatePicker();

    if(this.userId) {
      this.user = this.userId;
    }
    this.fetch();
    this.fetchUsers();
  },

  mixins: [ResponseMixins],

  methods: {

    initializeDatePicker() {
      let $this = this;
      $("#activityLogDateRange").daterangepicker({
        startDate: moment(),
        endDate: moment().add(1, "month"),
      }, function (start, end, label) {
        $this.createdAt = `${start} - ${end}`;
        $this.fetch();
      });
    },

    fetch(isRefresh = null, fetchUrl = null) {
      this.current_url = fetchUrl ? fetchUrl: this.fetchUrl ;
      const params =  {
        params: { 
          event: this.event, 
          location: this.location, 
          user: this.user,
          created_at: this.createdAt, 
          search: this.searchQuery
        }
      };

      axios.get(`${this.current_url}`, params)
      .then((res) => {
        this.logs = res.data.data;
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

    fetchUsers() {
      axios.get("/users/fetch?nopaginate=true&noexemptions=true") 
        .then((res) => {
          this.users = res.data;
        });
    },

    viewLog(log) {
      if(this.canView(log)) {
        this.selectedLog = log;
        this.drawer.show();
      }
    },

    deleteSuccess() {
      Swal.fire("Activity Log Deleted", "Activity log has been deleted", "success");
      this.fetch();
    },

    filter() {
      this.createdAt = $("#createdAtDateRange").val();
      this.fetch('search');
    },

    refresh() {
      this.searchQuery = "";
      this.fetch(true);
    },

    resetFilters() {
      this.event = "all";
      this.location = "all";
      this.user = "all";
      this.fetch();
    },

    canView(log) {
      if(log.properties) {
        if(log.properties["old"] != undefined  || log.properties["attributes"] != undefined) {
          return true;
        }
      }
      return false;
    }

  },
}
</script>
<style>
.changes-light {
  background: #fff;
  color: #000;
  padding: 20px;
}
.changes-dark {
  background: #1e1e2d;
  color: #fff;
  padding: 20px;
}
</style>