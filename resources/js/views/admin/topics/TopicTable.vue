<template>
  <div id="kt_app_content_container">
    <div class="card">
      <div class="card-header border-0 pt-6">
        <div class="card-title">
          <div class="col-12 my-1">
            <div class="input-group mb-3">
              <input 
              v-model="searchQuery"
              placeholder="Search topics"
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
                  <label class="form-label fs-6 fw-semibold">Status:</label>
                  <select 
                  v-model="status"
                  class="form-select form-select-solid fw-bold">
                    <option value="all">All</option>
                    <option value="active">Active</option>
                    <option value="archived">Deactivated</option>
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
            @click="addTopic()"
            type="button" class="btn btn-primary">
            <span class="svg-icon svg-icon-2">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor" />
                <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="currentColor" />
              </svg>
            </span>Add Topic</button>
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
          :class="{'table-min-height': topics.length}"
          class="table-responsive">
            <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_users">
              <thead>
                <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                  <th class="min-w-125px">Title</th>
                  <th class="min-w-125px">Items</th>
                  <th class="min-w-125px">Status</th>
                  <th class="text-center min-w-100px">Actions</th>
                </tr>
              </thead>
                <draggable 
                @change="sorted($event)"
                :sort="canSort"
                v-if="topics.length" v-model="topics" tag="tbody">
                  <template #item="{element}">
                    <tr class="cursor-pointer text-gray-600 fw-semibold">
                      <td>
                        {{ element.title }}
                      </td>
                      <td>
                        <span class="badge badge-info">{{ element.items_count }}</span>
                      </td>
                      <td>
                        <span v-if="!element.deleted_at" class="badge badge-light-primary">Active</span>
                        <span v-else class="badge badge-light-danger">Deactivated</span>                        
                      </td>                      
                      <td class="text-center">
                        <div class="dropdown">
                          <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                            Action
                          </button>
                          <ul class="dropdown-menu" aria-labellezdby="dropdownMenuButton1">
                            <li>
                              <a @click="viewTopic('view', element)" class="dropdown-item" href="#">View</a>
                            </li>
                            <li>
                              <a @click="viewTopic('edit', element)" class="dropdown-item" href="#">Edit</a>
                            </li>
                            <li v-if="!element.is_active">
                              <confirm-button
                              type="link"
                              :btnText="renderAlertProps(element).text"
                              :title="renderAlertProps(element).title"
                              :description="renderAlertProps(element).description"
                              btn-class="dropdown-item"
                              :route="renderAlertProps(element).route"
                              @confirmed="confirmedSuccess(element)"
                              ></confirm-button>
                            </li>
                          </ul>
                        </div>
                      </td>
                    </tr>
                  </template>
                </draggable>
              <tr v-if="!topics.length" class="odd my-10">
                <td valign="top" colspan="4" class="text-center">No matching records found</td>
              </tr>
            </table>
          </div>
      </div>
    </div>
  </div>
  
  <modal 
  :value="showModal"
  :modalsize="`modal-dialog-centered ${(renderAccessType == 'Add') ? 'mw-650px' : 'modal-xl'}`"
  @change="setModalStatus($event)"
  v-model="showModal">
    <template v-slot:title>
      {{ renderAccessType }} Topic
    </template>
    <template v-slot:body>
      <topic-view
      ref="topic"
      :store-url="storeUrl"
      @cancel="setModalStatus(false)"
      @editing="accessType = 'edit'"  
      @updated="fetch()"
      ></topic-view>
    </template>
  </modal>

</template>
<script>
import ResponseMixins from "Mixins/response.js";
import TopicView from "./TopicView";
import draggable from 'vuedraggable';

export default {
  name: "topic-table",
  
  components: {
    TopicView,
    draggable
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
    },
  
    canSort() {
      const deleted = this.topics.filter((a => a.deleted_at));
      return deleted.length ? false: true;
    }
  },

  data: () => ({
    topics: [],

    status: "active",
    searchQuery: "",

    showModal: false,
    accessType: "create"
  }),

  mounted() {
    this.fetch();
  },

  mixins: [ResponseMixins],

  methods: {

    fetch(isRefresh = null, fetchUrl = null) {
      this.current_url = fetchUrl ? fetchUrl: this.fetchUrl ;
      const params =  {
        params: { search: this.searchQuery, status: this.status }
      };

      axios.get(`${this.current_url}`, params)
      .then((res) => {
        this.topics = res.data;
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

    addTopic() {
      this.$refs.topic.setAccessType("add");
      this.showModal = true;
      this.accessType = "add";
    },

    viewTopic(type, data) {
      this.$refs.topic.setAccessType(type);      
      this.$refs.topic.fetch(data);
      this.showModal = true;
      this.accessType = type;
    },

    activateSuccess(topic) {
      Swal.fire(`${topic.title} Activated`, `${topic.title} has been successfully activated`, "success");
      this.fetch();
    },

    confirmedSuccess(topic) {
      const action = topic.deleted_at ? "restored" : "archived";
      Swal.fire(`${topic.title} ${action}`, `${topic.title} has been successfully ${action}`, "success");
      this.fetch();
    },

    sorted(data) {
      const newPosition = [];
      this.topics.forEach((item, index) => {
        newPosition.push({ id: item.id, order: (index + 1) });
      });
      const params = {
        order: newPosition
      };
      axios.post(`topics/sort/order/update`, params)
        .then(() => {
          this.parseInfo("Topic order has been updated")
        }).catch((err) => {
          this.parseError(err, {});
        });
    },

    refresh() {
      this.searchQuery = "";
      this.fetch(true);
    },

    resetFilter() {
      this.status = "active";
    },

    renderAlertProps(topic) {
      return {
        text: topic.deleted_at ? "Restore": "Archive",
        title: `${topic.deleted_at ? "Restore": "Archive"} ${topic.title}?`,
        description: `Are you sure to ${topic.deleted_at ? "restore" : "archive"} <strong>${topic.title}</strong>?`,
        route: topic.deleted_at ? topic.restore_url : topic.delete_url,
        btnClass: topic.deleted_at ? "btn btn-success" : "btn btn-danger"
      };
    },

  },
}
</script>