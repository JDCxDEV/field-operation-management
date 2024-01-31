<template>
<!--begin::Stepper-->
<div id="custom-stepper" class="custom-stepper stepper stepper-pills">
    <div class="stepper-nav flex-center flex-wrap mb-10">
        <div 
        v-for="step in steps"
        @click="selectStep(step)"
        :class="{'current': currentStep == step.step, 'completed': currentStep > step.step}"
        class="stepper-item mx-2 my-2">
            <!--begin::Wrapper-->
            <div class="stepper-wrapper d-flex align-items-center">
                <!--begin::Icon-->
                <div class="stepper-icon w-40px h-40px">
                    <i 
                    v-if="currentStep > step.step"
                    class="stepper-check fas fa-check"></i>
                    <span 
                    v-else-if="currentStep < step.step"
                    class="stepper-number"><i :class="step.icon" class="text-gray-400"></i></span>
                    <span 
                    v-else
                    class="stepper-number"><i :class="step.icon" class="text-white"></i></span>
                </div>
                <!--end::Icon-->

                <!--begin::Label-->
                <div class="stepper-label">
                    <h3 class="stepper-title">
                        Step {{ step.step }}
                    </h3>

                    <div class="stepper-desc">
                        {{ step.name }}
                    </div>
                </div>
                <!--end::Label-->
            </div>
            <!--end::Wrapper-->

            <!--begin::Line-->
            <div class="stepper-line h-40px"></div>
            <!--end::Line-->
        </div>
    </div>
</div>

<div id="custom-stepper-dropdown" class="custom-stepper-dropdown" style="display: none">
    <div class="mb-5">
        <select 
        v-model="selectedStep"
        @change="selected()"
        class="form-select fw-bold">
            <option 
            v-for="step in steps"
            :disabled="lastAccessTab < step.step && step.step !== 0"
            :value="step.step"
            >{{ step.step !== 0 ? `Step ${step.step}: ` : '' }}{{ step.name }}</option>
        </select>
    </div>
</div>
<!--end::Stepper-->
</template>
<script>
export default {
    name: "stepper",

    props: {
        steps: {
            type: Array,
            default: []
        },

        currentStep: {
            type: Number,
            default: 1
        },

        lastAccessTab: {
            type: Number,
            default: 1            
        },
        
        forceMobile: {
            type: Boolean,
            default: false
        }
    },

    watch: {
        currentStep() {
            this.selectedStep = this.currentStep;
        }
    },

    data: () => ({
        selectedStep: 1
    }),

    mounted() {
        if(this.forceMobile) {
            document.getElementById("custom-stepper").style.display = "none";
            document.getElementById("custom-stepper-dropdown").style.display = "block";
        }
    },

    methods: {
        selectStep(step) {
            if(this.lastAccessTab >= step.step) {
                this.$emit("jumpStep", step);
            }
        },

        selected() {
            const step = this.steps.find((a => a.step == this.selectedStep));
            if(step) {
                this.selectStep(step);
            }
        }
    }
}
</script>