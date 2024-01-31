<template>
  <div 
  v-if="topicDetails.deleted_at"
  class="mt-2 mb-5">
    <span 
    class="badge badge-danger">Archived</span>
  </div>
  <form @submit.prevent="onSubmit($event)" class="form">
    <div class="d-flex flex-column me-n7 pe-7">
      <div class="fv-row">
        <label class="required fw-semibold fs-6 mb-2">Title</label>
        <input 
        v-model="topicDetails.title"
        :name="!itemEditMode ? 'title' : ''"
        :disabled="!editMode || itemEditMode"
        class="form-control mb-3 mb-lg-0"
        :class="{'is-invalid': errors?.title && !itemEditMode}" 
        placeholder="Title"/>
        <div 
        :v-if="errors?.title"
        class="invalid-feedback">{{ errors?.title?.[0] }}</div>
      </div>
    </div>
    <div class="text-right pt-15 mb-7">
      <button 
      v-if="!topicDetails.id"
      :disabled="isLoading"          
      type="reset" 
      @click="cancel()"          
      class="btn btn-light me-3">Discard</button>

      <button
      v-if="editMode" 
      :disabled="isLoading || itemEditMode"
      class="btn btn-primary">Submit</button>
    </div>
  </form>

  <template v-if="topicDetails.id && !itemEditMode">
    <div class="separator border-gray-200 mb-7"></div>
    <div class="me-n7 pe-7 mb-7 text-right">
      <button 
      v-if="editMode"
      @click="addItem()"
      class="btn btn-info">
        <span class="svg-icon svg-icon-2">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor" />
            <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="currentColor" />
          </svg>
        </span>
        Add Topic Item
      </button>
    </div>
    <div class="me-n7 pe-7 mb-7">
      <h4>Topic Items</h4>
    </div>    
    <div class="table-responsive">
      <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_users">
        <thead>
          <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
            <th class="min-w-125px">Title</th>
            <th class="min-w-125px">Content</th>
            <th 
            v-if="editMode"
            class="text-center  min-w-100px">Actions</th>
          </tr>
        </thead>
        <draggable 
        @change="sorted($event)"
        :sort="editMode"
        v-if="items.length" v-model="items" tag="tbody">
          <template #item="{element}">
            <tr class="cursor-pointer text-gray-600 fw-semibold">
              <td>
                {{ element.title }}
              </td>
              <td>
                <div v-html="element.content"></div>
              </td>
              <td 
              v-if="editMode"
              class="text-center"
              >
                <div class="dropdown">
                  <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                    Action
                  </button>
                  <ul class="dropdown-menu" aria-labellezdby="dropdownMenuButton1">
                    <li>
                      <a @click="viewItem(element)" class="dropdown-item" href="#">Edit</a>
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
        <tr 
        v-if="!items.length"
        class="text-gray-600 fw-semibold">
          <tr>
            <td>
              No data found
            </td>
          </tr>
        </tr>
      </table>
    </div>
    
    <div 
    class="text-center pt-15">
      <button 
      :disabled="isLoading"          
      type="reset" 
      @click="cancel()"          
      class="btn btn-light me-3">Discard</button>
    </div>
  </template>

  <template v-if="itemEditMode">
    <form @submit.prevent="onSubmitTopicItem($event)" class="form">
      <div class="d-flex flex-column me-n7 pe-7">
        <div class="fv-row mb-7">
          <label class="required fw-semibold fs-6 mb-2">Title</label>
          <input 
          v-model="topicItem.title"
          name="title"
          class="form-control mb-3 mb-lg-0"
          :class="{'is-invalid': errors?.title}" 
          placeholder="Title"/>
          <div 
          :v-if="errors?.title"
          class="invalid-feedback">{{ errors?.title?.[0] }}</div>
        </div>
      </div>
      <div class="d-flex flex-column me-n7 pe-7">
        <div class="fv-row">
          <label class="required fw-semibold fs-6 mb-2">Content</label>
          <div :class="{'is-invalid': errors.content}">
           <Editor
            v-model="topicItem.content"        
            :disabled="!editMode"
            :api-key="tinymceApiKey"
            />
          </div>
          <div 
          :v-if="errors?.content"
          class="invalid-feedback">{{ errors?.content?.[0] }}</div>
        </div>
      </div>
      <div class="text-right pt-15 mb-7">
        <button 
        :disabled="isLoading"          
        type="reset" 
        @click="cancelItem()"          
        class="btn btn-light me-3">Discard</button>
        
        <button
        :disabled="isLoading"
        class="btn btn-primary">Submit</button>
      </div>
    </form>
  </template>

</template>
<script>
import ResponseMixins from "Mixins/response.js";
import draggable from 'vuedraggable';
import Editor from '@tinymce/tinymce-vue';

export default {
  name: "form-template-view",

  components: {
    draggable,
    Editor
  },

  props: {
    storeUrl: {
      type: String, 
      default: null
    },
  },
  
  data: () => ({
    topicDetails: {
      title: "",
    },
    items: [],

    topicItem: {
      title: "",
      content: ""
    },

    itemEditMode: false,
    editMode: true,
    isLoading: false,
    accessType: "add",
  
    submitUrl: "",

    tinymceApiKey: "hyevq2v5hi63o3hf4wtm4x7wplukkgumy07zjx8me376esbq",

  }),

  mounted() {
    this.submitUrl = this.storeUrl;
  },

  mixins: [ResponseMixins],

  methods: {
    
    fetch(data) {
      this.editMode = false;
      this.initLoading = true;
      axios.get(data.find_url)
        .then((res) => {
          this.$nextTick(() => {
            this.topicDetails = res.data.topic;
            this.items = this.topicDetails.items;
          });
          this.submitUrl = data.update_url;
        }).catch((error) => {
          this.parseError(error, null, {});
        }).finally(() => {
          this.initLoading = false;
          if(this.accessType == "edit") {
            this.editMode = true;
          }
        });
    },

    setAccessType(type) {
      this.resetErrors();
      this.accessType = type;
      this.cancelItem();
      switch(type) {
        case "add":
          this.topicDetails = {
            title: "",
          };
          this.editMode = true;
          break;
        case "edit":
          this.editMode = true;
          break;
        case "view":
          this.editMode = false;          
          break;
      }
    },

    onSubmit(event) {
      this.resetErrors();
      let form = event.target;
      if (!form) {
        form = event;
      }
      const params = new FormData(form);      
      
      this.isLoading = true;
      axios.post(this.submitUrl, params)
        .then((res) => {
          const action = this.accessType == "add" ? "created" : "updated"; 
          Swal.fire(`${this.topicDetails.title} ${action}`, `${this.topicDetails.title} has been ${action}.`, "success");
          this.$emit("updated");
          this.cleanFields();
        }).catch((error) => {
          console.log(error);
          /* Display Error message */
          this.parseError(error, null, {});          
        }).finally(() => {
          this.isLoading = false;          
        });
    },

    onSubmitTopicItem(event) {
      this.resetErrors();
      let form = event.target;
      if (!form) {
        form = event;
      }
      const params = new FormData(form);
      params.append("content", this.topicItem.content);
      
      this.isLoading = true;
      const url = this.topicItem.id ? this.topicItem.update_url : `topic-items/${this.topicDetails.id}/store`;
      axios.post(url, params)
        .then((res) => {
          const action = this.selectedItem ? "updated" : "created";
          Swal.fire(`${this.topicItem.title} ${action}`, `${this.topicItem.title} has been ${action}.`, "success");
          this.fetch(this.topicDetails);
          this.$emit("updated");
          this.cancelItem();
        }).catch((error) => {
          console.log(error);
          /* Display Error message */
          this.parseError(error, null, {});          
        }).finally(() => {
          this.isLoading = false;          
        });
    },

    confirmedSuccess(topicItem) {
      const action = topicItem.deleted_at ? "restored" : "archived";
      Swal.fire(`${topicItem.title} ${action}`, `${topicItem.title} has been successfully ${action}`, "success");
      this.$emit("updated");
      this.fetch(this.topicDetails);
    },

    setToEditMode() {
      this.editMode = true;
      this.accessType = "edit";
      this.$emit("editing");
    },

    addItem() {
      this.itemEditMode = true;
    },

    viewItem(item) {
      this.itemEditMode = true;
      this.topicItem = item;
    },

    sorted(data) {
      const newPosition = [];
      this.items.forEach((item, index) => {
        newPosition.push({ id: item.id, order: (index + 1) });
      });
      const params = {
        order: newPosition
      };
      axios.post(`topic-items/sort/order/update/${this.topicDetails.id}`, params)
        .then(() => {
          this.parseInfo("Topic Item order has been updated");
        }).catch((err) => {
          this.parseError(err, {});
        });
    },

    cancelItem() {
      this.itemEditMode = false;
      this.topicItem = {
        title: "",
        content: "",
      };
    },

    cancel() {
      this.$emit("cancel");
    },

    cleanFields() {
      if(this.accessType != "add") return;
      this.topicDetails = {
        title: "",
      };
    },

    renderAlertProps(topicItem) {
      return {
        text: topicItem.deleted_at ? "Restore": "Archive",
        title: `${topicItem.deleted_at ? "Restore": "Archive"} ${topicItem.title}?`,
        description: `Are you sure to ${topicItem.deleted_at ? "restore" : "archive"} <strong>${topicItem.title}</strong>?`,
        route: topicItem.deleted_at ? topicItem.restore_url : topicItem.delete_url,
        btnClass: topicItem.deleted_at ? "btn btn-success" : "btn btn-danger"
      };
    },

  }

}
</script>
<style>
.text-right {
  text-align: right;
}
.mr-s {
  margin-right: 10px;
}
</style>