<template>
  <div
    :ref="modalRef"
    class="modal fade"
    tabindex="0"
    role="dialog"
    aria-hidden="true"
    data-bs-backdrop="static"
  >
    <div
      class="modal-dialog"
      :class="sizeClass"
    >
      <div class="modal-content">
        <!-- Modal Header -->
        <div class="modal-header">
          <h2
            v-show="$slots['title']"
            class="fw-bold"
          >
            <slot name="title" />
          </h2>
          <!--begin::Close-->
          <div 
          @click="close"
          class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-users-modal-action="close">
            <!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
            <span class="svg-icon svg-icon-1">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
                <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor" />
              </svg>
            </span>
            <!--end::Svg Icon-->
          </div>
          <!--end::Close-->
        </div>

        <!-- Modal Body -->
        <div
          v-show="$slots['body']"
          :class="modalBodyClass"
          class="modal-body scroll-y"
        >
          <slot name="body" />
        </div>

        <!-- Modal Footer -->
        <div
          v-show="$slots['footer']"
          class="modal-footer"
        >
          <slot name="footer" />
        </div>

        <slot name="content" />
      </div>
    </div>
  </div>
</template>

<script>
import FormatterMixins from "Mixins/formatter.js";
export default {
  name: 'modal',

  model: {
    prop: 'value',
    event: 'change'
  },

  props: {
    small: {
      default: false,
      type: Boolean
    },

    backdrop: {
      default: true,
      type: [ Boolean, String ],
      validator: (value) => {
        return [ 'static', true, false ].indexOf(value) !== -1;
      }
    },

    value: {
      default: false,
      type: Boolean
    },

    modalsize: {
      default: "modal-sm",
      type: String
    },

    modalBodyClass: {
      default: "m-5",
      type: String,
    },
  
  },

  data: () => ({
    elem: null,
    modalRef: ""
  }),

  computed: {
    sizeClass() {
      return this.small ? 'modal-sm' : this.modalsize;
    }
  },

  watch: {
    value(value) {
      if (value) {
        this.open();
      } else {
        this.close();
      }
    }
  },
  beforeMount(){
    this.modalRef = this.uniqueId();
  },
  mounted() {
    this.setup();
  },

  mixins: [ FormatterMixins ],  

  methods: {
    setup() {
      this.elem = $(this.$refs[this.modalRef]);

      this.elem.on('hidden.bs.modal', () => {
        this.close();
        this.$emit('change', false);
      });
    },

    open() {
      this.elem.modal("show");
    },

    close() {
      this.elem.modal('hide');
      this.$emit('change', false);
    }
  }
};
</script>

<style>
.modal { overflow: auto !important; }
</style>