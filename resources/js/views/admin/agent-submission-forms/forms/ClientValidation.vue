<template>
<div 
v-if="accordionType"
class="accordion" id="steps-accordion">
    <div v-for="accordion in stepOneAccordions" class="accordion-item">
        <h2 class="accordion-header" :id="`header-${accordion.key}`">
			<button 
            :disabled="currentAccordionStep != accordion.step && lastAccordionStep < accordion.step && lastAccessTab == 1"
			@click="currentAccordionStep = accordion.step"
            :class="{'collapsed': currentAccordionStep != accordion.step, 'bg-light text-muted': (currentAccordionStep != accordion.step && lastAccordionStep < accordion.step) }"
            class="accordion-button fs-4 fw-semibold" type="button" data-bs-toggle="collapse" :data-bs-target="`#body-${accordion.key}`" aria-expanded="true" :aria-controls="`body-${accordion.key}`">
				<i 
				v-if="currentAccordionStep != accordion.step && lastAccordionStep < accordion.step && lastAccessTab == 1" 
				class="ki-duotone ki-lock-2 fs-1" style="margin-right: 10px">
					<i class="path1"></i>
					<i class="path2"></i>
					<i class="path3"></i>
					<i class="path4"></i>
					<i class="path5"></i>
				</i>
				{{ accordion.name }}
            </button>
        </h2>
        <div :id="`body-${accordion.key}`" 
        :class="{'show': currentAccordionStep == accordion.step}"
        class="accordion-collapse collapse" :aria-labelledby="`header-${accordion.key}`" data-bs-parent="#steps-accordion">
            <div class="accordion-body">
				<div v-show="accordion.key == 'client-validation' && accordion.step == currentAccordionStep">
					<step-one
					:client-validation-url="clientValidationUrl"
					:form-data="formData"
					:edit-mode="editMode"
					@success="success"
					>
					</step-one>
				</div>

				<div v-if="accordion.key == 'client-details' && accordion.step == currentAccordionStep">
					<step-two
					:client-address-validation-url="clientAddressValidationUrl"
					:form-data="formData"
					:edit-mode="editMode"
					@success="success"
					@back="back()"
					>
					</step-two>
				</div>

				<div v-show="accordion.key == 'employment-data' && accordion.step == currentAccordionStep">
					<step-three
					:employment-data-validation-url="employmentDataValidationUrl"
					:form-data="formData"
					:form-values="form"
					:edit-mode="editMode"
					@completed="completed"
					@success="proceed"
					@back="back()"
					>
					</step-three>
				</div>
            </div>
        </div>
    </div>
</div>

<div v-if="!accordionType">
	<stepper
	:steps="stepOneAccordions"
	:current-step="currentAccordionStep"
	:last-access-tab="lastAccessTab > 1 ? 3 : lastAccordionStep"
	:next-button="nextButton"
	@jumpStep="jumpStep"
	class="mb-7"
	/>

	<step-one
	v-show="currentAccordionStep == 1"
	:client-validation-url="clientValidationUrl"
	:form-data="formData"
	:edit-mode="editMode"
	:enable-watcher="enableWatcher"
	:next-button="nextButton"	
	@inputTrigger="saveInput"
	@success="success"
	>
	</step-one>

	<step-two
	v-if="currentAccordionStep == 2"
	:client-address-validation-url="clientAddressValidationUrl"
	:form-data="formData"
	:edit-mode="editMode"
	:enable-watcher="enableWatcher"
	:next-button="nextButton"
	@success="success"
	@inputTrigger="saveInput"	
	@back="back()"
	>
	</step-two>

	<step-three
	v-show="currentAccordionStep == 3"
	:employment-data-validation-url="employmentDataValidationUrl"
	:form-data="formData"
	:form-values="form"
	:edit-mode="editMode"
	:enable-watcher="enableWatcher"
	:next-button="nextButton"
	@inputTrigger="saveInput"	
	@completed="completed"
	@success="proceed"
	@back="back()"
	>
	</step-three>	
		
</div>

</template>
<script>
import ResponseMixins from "Mixins/response.js";
import FormatterMixins from "Mixins/formatter.js";
import Stepper from "Components/steppers/Stepper.vue";
import StepOne from "./client-validation-steps/StepOne.vue";
import StepTwo from "./client-validation-steps/StepTwo.vue";
import StepThree from "./client-validation-steps/StepThree.vue";

export default {
	name: "client-validation",

	components: {
		Stepper, 
		StepOne,
		StepTwo,
		StepThree
	},

	props: {
		clientValidationUrl: {
			type: String,
			default: "",
		},

		clientAddressValidationUrl: {
			type: String,
			default: "",
		},

		employmentDataValidationUrl: {
			type: String,
			default: "",
		},

		formData: {
            type: Object,
            default: {}
        },

		editMode: {
			type: Boolean,
			default: true
		},

		accordionType: {
			type: Boolean,
			default: true
		},
		
		lastAccessTab: {
			type: [String, Number],
			default: 1,
		},

		enableWatcher: {
			type: Boolean,
			default: false
		},

		nextButton: {
			type: Boolean,
			default: false
		}
	},

	data: () => ({

		isLoading: false,

		form: { },

		stepOneAccordions: [
            { name: "Client Validation", key: "client-validation", step: 1, icon: "fa-solid fa-person-circle-exclamation" },
            { name: "Client Details", key: "client-details", step: 2, icon: "fa-solid fa-address-card"},
            { name: "Employment Data", key: "employment-data", step: 3, icon: "fa-solid fa-user-tie" },
        ],
        currentAccordionStep: 1,
		lastAccordionStep: 1,

	}),

	mixins: [ ResponseMixins, FormatterMixins ],

	methods: {

		completed(data) {
			this.proceed(data);
		},
		
		success(data) {
			if(data) {
				this.form = { ...this.form, ...data };
				this.proceedToNextAccordion();
			}
		},

		proceedToNextAccordion() {
			const nextStep = this.currentAccordionStep;
            this.currentAccordionStep = this.stepOneAccordions[nextStep].step;
			if(this.lastAccordionStep <= this.currentAccordionStep) {
				this.lastAccordionStep = this.currentAccordionStep;
			}
		},

		back() {
			const backStep = this.currentAccordionStep - 1;
            this.currentAccordionStep = this.stepOneAccordions[backStep - 1].step;
		},

        proceed(data = "") {
            this.$emit("proceed", data);            
        },
		
		jumpStep(tab) {
			this.currentAccordionStep = tab.step;
        },

		saveInput(data) {
			this.$emit("saveInput", data);
		}
	}
}
</script>