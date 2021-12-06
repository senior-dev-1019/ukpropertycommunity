<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class syncController extends Controller
{
    public function sync(Request $request)
    {
        // Extract and format the data
        $data = $request->report;
        $properties = array();

        foreach( $data as $property )
        {
            $id = $property["ID"];
            //$house_number = $property["house_number"];
            $title = $property["title"];
            $bedrooms = $property["bedrooms"];
            $price = str_replace(",", "", str_replace("£", "", $property["price"]));
            $link = $property["link"];
            $phone = $property["phone"];
            $description = $property["description"];
            $m2 = $property["m2"];
            $epc = $property["epc"];
            $address = $property["address"];
            $location = explode(",", $property["location"]);
            $author = trim(str_replace("by", "", substr($location[0], strpos($location[0], "by"), strlen($location[0]))));
            $city = isset($location[1]) ? trim($location[1]) : "Undefined";

            // Remove the first part
            $address = explode(",", $address);

            // Extract the house number from the address when available
            $house_number_string = $address[0];
            preg_match_all('!\d+!', $house_number_string, $matches);
            $house_number = isset($matches[0][2]) ? $matches[0][2] : null;

            // Remove the first portion of the app that contains the extra data
            unset($address[0]);
            $address = implode(" ", $address);

            $row = [
                "id" => $id,
                "house_number" => $house_number,
                "title" => $address,
                "bedrooms" => intval($bedrooms),
                "price" => floatval($price),
                "link" => $link,
                "phone" => str_replace(" ", "", $phone),
                "description" => $description,
                "m2" => intval($m2),
                "epc" => $epc != "" ? $epc : "",
                "address" => $address,
                "author" => $author,
                "city" => str_replace(",", "", $city),
                "images" => $property["image"]
            ];

            array_push($properties, $row);
        }

        foreach($properties as $property)
        {
            // Check if user already exists
            $result = DB::select("SELECT * FROM re_accounts WHERE phone = :phone OR username = :username LIMIT 1", ["phone" => $property["phone"], "username" => $property["author"]]);
            if( sizeof($result) == 0 )
            {
                // Create a user account with enough credit to post
                DB::insert("INSERT INTO re_accounts (first_name, last_name, username, email, password, phone, credits, confirmed_at) VALUES (:first_name, :last_name, :username, :email, :password, :phone, :credit, :confirmed_at)",
                    ["first_name" => $property["author"], ":last_name" => "", "username" => $property["author"], "email" => $property["phone"] . "@site.com", "password" => Hash::make("123456789963"), "phone" => $property["phone"], "credit" => 100, "confirmed_at" => date("Y-m-d")]
                );
            }

            // Get the city id
            $result = DB::select("SELECT * FROM myapp.cities WHERE name = ? LIMIT 1", [$property["city"]]);
            if(sizeof($result) == 0)
            {
                DB::statement("INSERT INTO myapp.cities (name, state_id, country_id, record_id, status, created_at) VALUES (?, ?, ?, ?, ?, ?)", [$property["city"], 1, 1, 1, "active", date("Y-m-d")]);
                $data = DB::select("SELECT * FROM myapp.cities WHERE name = ? LIMIT 1", [$property["city"]]);
                $city_id = $data[0]->id;
            }
            else
            {
                $data = DB::select("SELECT * FROM myapp.cities WHERE name = ? LIMIT 1", [$property["city"]]);
                $city_id = $data[0]->id;
            }

            // Login to the account
            $user = array(
                'email' => $property["phone"] . "@site.com",
                'password' => '123456789963',
            );
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, env("APP_URL")."login");
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $user);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER,false);
            curl_setopt($ch, CURLOPT_COOKIEJAR, "./cookies.txt");
            curl_exec($ch);
            curl_close($ch);

            // Add all images as a json string
            if( sizeof($property["images"]) > 0 )
            {
                $images_data = '[';
                // Upload image
                for($i=0; $i<sizeof($property["images"]); $i++)
                {
                    $image_name = rand(1, 9000) . "img";
                    $img = public_path("/storage/$image_name");
                    file_put_contents("$img.jpeg", file_get_contents($property["images"][$i]));
                    file_put_contents("$img-150x150.jpeg", file_get_contents($property["images"][$i]));
                    file_put_contents("$img-400xauto.jpeg", file_get_contents($property["images"][$i]));
                    file_put_contents("$img-640xauto.jpeg", file_get_contents($property["images"][$i]));
                    file_put_contents("$img-1024xauto.jpeg", file_get_contents($property["images"][$i]));
                    file_put_contents("$img-autox610.jpeg", file_get_contents($property["images"][$i]));
                    $images_data .= '"' . $image_name . '.jpeg",';
                }
                $images_data = trim($images_data, ",");
                $images_data .= ']';
            }
            else
            {
                $images_data = "[]";
            }

            sleep(2);

            // Add property
            $prop_data = array(
                "name" => $property["house_number"] != null ? $property["house_number"] . ", " . $property["title"] : $property["title"],
                "slug" => null,
                "slug_id" => 0,
                "model" => "Botble\RealEstate\Models\Property",
                "type_id" => 1,
                "description" => "<p>".$property["description"]."</p>",
                "content" => "<p>".$property["description"]."</p>",
                "images" => $images_data,
                "city_id" => $city_id,
                "location" => $property["address"],
                "latitude" => null,
                "longitude" => null,
                "number_bedroom" => intval($property["bedrooms"]),
                "number_bathroom" => null,
                "number_floor" => null,
                "square" => intval($property["m2"]) > 20 ? intval($property["m2"]) : 0,
                "price" => $property["price"],
                "currency_id" => 1,
                "period" => "month",
                "auto_renew" => 0,
                "category_id" => 1,
                "facilities[][id]" => null,
                "facilities[][distance]" => null,
                "video[thumbnail]" => null,
                "thumbnail_input" => null,
                "video[url]" => null,
                "seo_meta[seo_title]" => null,
                "seo_meta[seo_description]" => null,
                "submit" => "save",
                "language" => "en_US",
                "ref_from" => null,
                "header_layout" => "layout-1"
            );
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, env("APP_URL")."account/properties/create");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $prop_data);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
            curl_setopt ($ch, CURLOPT_COOKIEFILE, "./cookies.txt");
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE );
            curl_exec($ch);
            curl_close($ch);

            // Update the epc value
            DB::update("UPDATE re_properties SET epc = :epc, original_link = :original_link WHERE city_id = :city_id AND price = :price", ["epc" => $property["epc"], "city_id" => $city_id, "price" => $property["price"], "original_link" => $property["link"]]);
        }

        DB::update("UPDATE re_properties SET moderation_status = 'approved'");
    }
}
