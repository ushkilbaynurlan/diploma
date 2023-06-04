<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CustomField;
class CustomFieldSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $status = 0;
        $created_by = 1;

        $fields = [
            [
                "name" => __("Name"),
                "type" => "text",
                "placeholder" => __("Name"),
                "width" => "6",
            ],
            [
                "name" => __("Email"),
                "type" => "email",
                "placeholder" => __("Email"),
                "width" => "6",
            ],
            [
                "name" => __("Category"),
                "type" => "select",
                "placeholder" => __("Select Category"),
                "width" => "6",
            ],
            [
                "name" => __("Subject"),
                "type" => "text",
                "placeholder" => __("Subject"),
                "width" => "6",
            ],
            [
                "name" => __("Description"),
                "type" => "textarea",
                "placeholder" => __("Description"),
                "width" => "12",
            ],
            [
                "name" => __("Attachments"),
                "type" => "file",
                "placeholder" => __("You can select multiple files"),
                "width" => "12",
            ]
        ];

        foreach($fields as $order => $field) {

            $f = $field;
            $f['order'] = $order;
            $f['status'] = $status;
            $f['created_by'] = $created_by;

            CustomField::create($f);
        }
    }
}
