<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\FormTemplate;

class FormTemplatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $parent = [
            "title" => "Acknowledgements - Patient Protection & Affordable Care Act (PPACA) Assistance",
            "description" => "Sample Form template",
            "is_active" => true,
            "content" => '
                        <p>I (Customer) acknowledge that I have engaged the assistance of an independent Health Insurance Agent (Agent) licensed in the state of Florida, certified by the Centers for Medicare &amp; Medicaid Services (CMS), to assist me in the enrollment process for the Affordable Care Act (ACA) through the Healthcare.gov (Marketplace) website.<br><br></p> 
                        <div class="mb-4">In doing so, I understand and agree that all information supplied by me is accurate to the best of my knowledge. If proof of income or any other information is required by the Marketplace, it is my sole responsibility to provide it. I understand that any changes included but not limited to my information, income, health plan, etc. made by me can affect the eligibility/status of my health insurance plan. I also understand that I should be reporting my accurate income estimate, not the estimate that maximizes the amount of premium tax credit for which I may be eligible when completing or updating an application for eligibility.</div> 
                        <div class="mb-4">I understand that my current income and/or premium-cost limitations may result in having a lower-level plan with less benefits. I understand that my Agent can contact me via text or email which I provided unless I have checked this box.</div> 
                        <div class="mb-4">By signing this Acknowledgement, I provide my signature, expressly authorizing Alliance and Associates Insurance (Alliance), their agents or partner companies to contact me at the number and address provided with insurance quotes or to obtain additional information for such purpose, via live/prerecorded/autodialed calls, text messages and/or emails for a period of 3 years. I understand that my signature is not a condition of purchasing any property, goods, or services and that I may revoke my consent at any time. I also understand that I can contact my Agent for any questions pertaining to my policy and/or any changes that I would like to discuss. I consent to my Agent calling the Marketplace Call Center to ask about the status of a Marketplace enrollment as needed, up to once a year. If I need help or have questions about my policy, I understand that I can contact Alliance &amp; Associates Insurance and/or the Marketplace with the contact information below:</div> 
                        <div class="mb-4 d-flex flex-column">&nbsp;</div> 
                        <div class="mb-4 d-flex flex-column"><strong><span class="fw-bolder">Alliance &amp; Associates Insurance</span></strong></div> 
                        <div class="mb-4 d-flex flex-column"><strong>Phone</strong>: (866) 625-4425</div> 
                        <div class="mb-4 d-flex flex-column"><strong>Email</strong>: healthservice@alliance321.com</div> 
                        <div class="mb-4 d-flex flex-column">&nbsp;</div> 
                        <div class="mb-4 d-flex flex-column"><strong><span class="fw-bolder">Marketplace</span></strong></div> 
                        <div class="mb-4 d-flex flex-column">Phone: (800) 318-2596</div> 
                        <div class="mb-4 d-flex flex-column">&nbsp;</div> 
                        <div class="mb-4">If my policy requires a payment, I do hereby authorize Alliance &amp; Associates Insurance to initiate payment, using the payment information that I provided. If I have questions about any billing/invoice matters, I understand that I can contact Alliance &amp; Associates and/or the Marketplace, using the contact information above.</div> 
                        <div class="mb-4">&nbsp;</div> 
                        <div class="mb-4">I understand that my policy is through Florida Blue/Blue Cross Blue Shield and may or may not contain dental coverage and that if I am unsure of what coverages are contained within my policy, I can read through my policy documents once they are mailed to me and/or reach out to Alliance &amp; Associates Insurance, using the contact information above.</div> 
                        <div class="mb-4">&nbsp;</div> 
                        <div class="mb-4"><strong><span class="fw-bolder">The Customer acknowledges receipt of HIPPA Privacy Information at time of enrollment and agrees to allow Agent access to&nbsp;<a href="https://www.healthcare.gov/" target="_blank" rel="noopener">Healthcare.gov</a>&nbsp;as needed to secure and maintain my health insurance policy throughout the initial term of the plan and subsequent renewals.</span></strong></div> 
                        <div class="mb-4">&nbsp;</div> 
                    '
        ];

        $form = FormTemplate::create($parent);

        $child = [
            "title" => "Permission To Review",
            "description" => "Sample Children Form template",
            "is_active" => false,
            "parent_id" => $form->id,
            "content" => '
                        <div class="mb-4">I am voluntarily authorizing and giving consent to (Agent), as a Licensed Florida Health Insurance Agent, to review my health insurance enrollment now and annually, and advise me using all reasonable efforts, that I have made a sound choice for the current policy term and any subsequent policy terms. I understand the main roles of my agent are to advise me on how I can become eligible for certain plans on the Marketplace, build an understanding of my needs as a client, answer any questions I may have about my plan, and assist me in securing coverage.</div> 
                        <div class="mb-4">&nbsp;</div> 
                        <div class="mb-4">I understand that my Agent has permission to conduct a search for the consumer application using approved Classic Direct Enrollment/Enhanced Direct Enrollment websites in the Marketplace, assist with completing an eligibility application, assist with plan selection and enrollment, and assist with ongoing account/enrollment maintenance.</div> 
                        <div class="mb-4">&nbsp;</div> 
                        <div class="mb-4">I understand that this consent will last indefinitely unless I as the client revoke it. I understand that my legal name matches my signature below and I do know the name of my agent. My signature below also confirms my understanding of all the above.</div> 
                        <div class="mb-4">&nbsp;</div> 
                        <div class="mb-4">I give my consent so you can assist me in obtaining marketplace coverage, This includes my consent for you to search to the marketplace for applications, get me quotes, apply for a subsidy and apply for a current application.</div> 
                        <p>&nbsp;</p>
                        </div>
                    '

        ];
        FormTemplate::create($child);        
    }
}
