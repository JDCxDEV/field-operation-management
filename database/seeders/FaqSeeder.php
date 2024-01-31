<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Topic;
use App\Models\TopicItem;
use Faker\Generator;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        Topic::truncate();
        TopicItem::truncate();

        $items = [
            [
                "topic" => "Client Information",
                "order" => 1,
                "items" => [
                    [
                        "order" => 1,
                        "title" => "What is the compliance contact information?",
                        "content" => "
                            Compliance line number: (<a href='tel:8555000936'>855-500-0936</a>).
                            <br />
                            Compliance email: (<a href='mailto:customersupport@healthinsurancecompliance.com'>customersupport@healthinsurancecompliance.com</a>).
                            "
                    ],
                    [
                        "order" => 2,
                        "title" => "SSN Not validating? Try the following:",
                        "content" => "
                            ● Remove suffixes like ‘Jr’.
                            <br />
                            ● Double check spelling and DOB.
                            <br />
                            ● Make sure there are no unnecessary spaces before and after the names
                            <br />
                            ● If nothing else works, use the <a href='https://airtable.com/shrYxV9Qmj36tlN8i' target='_blank'>manual submission link</a>. (Please not that you WILL have to upload a
                            picture of the signed acknowledgement form for a manual submission)."
                    ],
                    [
                        "order" => 3,
                        "title" => "How do I determine basic eligibility?",
                        "content" => "
                            ● Basic eligibility means that the <u>Total Household Income</u> and <u>Total Household Size</u> are at
                            the Minimum Qualifying Level.
                            <br />
                            ● Total Household Income = Income of client + Income of spouse/ Domestic partner (if
                            they file together)
                            <br />
                            ● Total Household Size = Client + Spouse/ Domestic Partner (If they file together) +
                            Dependents claimed on taxes."
                    ],
                    [
                        "order" => 4,
                        "title" => "How do I know if the client is making too much to qualify for a zero dollar plan?",
                        "content" => "
                            ● To put it simply, you can’t know right away. ACA eligibility depends on a number of
                            external factors like tax paying history, tax owed, zip code etc.
                            It is best practice to simply input this information and see if they qualify for Zero Premium
                            Insurance.
                            <br />
                            If the client makes 4 times the Minimum Qualifying Limit, then there is a high chance that
                            they will not qualify for enough Cost Sharing Reduction (CSR) to get Zero Dollar
                            Insurance."
                    ],
                    [
                        "order" => 5,
                        "title" => "If a client does not qualify for a zero dollar plan, what are the next steps?",
                        "content" => "
                            ● If you are licensed and appointed, then you can ‘quote’ a plan that is going to be heavily
                            subsidized, using a platform like healthcare.gov or healthsherpa.com. Once the client
                            has made a decision, you can populate the ‘Plan Information’ field in the application.
                            <br />
                            ● If you are not licensed and appointed, then you can have a Compliance Support Agent
                            quote a plan over the phone. Just call the compliance hotline and let them help you! You
                            will still receive 100% credit for this application.
                            "
                    ],
                    [
                        "order" => 6,
                        "title" => "Is an incarceration ID acceptable?",
                        "content" => "
                            ● Yes, but they will have to provide paperwork with a release date that is not in the future.
                            <br/>
                            This means that they cannot be on a work release program or on probation/conditional release"
                    ],
                    [
                        "order" => 7,
                        "title" => "What if the client is asking basic information about the features of the plan?",
                        "content" => "
                            ● The kind of plan they would qualify for would vary depending on their income, age, zip
                            code etc. If you are a licensed and appointed agent then you can tell them this
                            information from health sherpa/ healthcare.gov. If you are not then simply call the
                            compliance hotline."
                    ],
                    [
                        "order" => 8,
                        "title" => "What addresses are not allowed?",
                        "content" => "
                            ● Homeless Shelters
                            <br/>
                            ● Rehabilitation Centers
                            <br/>
                            ● Halfway houses
                            <br/>
                            ● PO Box
                            <br/>
                            ● Commercial addresses"
                    ],
                    [
                        "order" => 9,
                        "title" => "Who do I contact if I have questions regarding compensation?",
                        "content" => "
                            ● Please call your coordinator."
                    ],
                    [
                        "order" => 10,
                        "title" => "Do I always need to provide SSN for spouses or dependents?",
                        "content" => "
                            ● Only if they need coverage."
                    ],
                ],
            ]
        ];

        foreach($items as $item) {
            $topic = Topic::create(["title" => $item["topic"], "order" => $item["order"]]);
            foreach($item["items"] as $child) {
                TopicItem::create([
                    "order" => $child["order"],
                    "topic_id" => $topic->id,
                    "title" => $child["title"],
                    "content" => $child["content"]
                ]);
            }
        }

        // for ($i = 0; $i <= 10; $i++) { 
        //     $topic = Topic::create([
        //         "order" => $i,
        //         "title" => $faker->sentence
        //     ]);

        //     for ($n = 0; $n <= 3; $n++) {
        //         TopicItem::create([
        //             "order" => $n,
        //             "topic_id" => $topic->id,
        //             "title" => $faker->sentence,
        //             "content" => $faker->paragraph
        //         ]);
        //     }
        // }
    }
}
