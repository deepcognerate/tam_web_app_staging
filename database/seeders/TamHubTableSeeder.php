<?php

namespace Database\Seeders;

use App\Models\Tamhub;
use Illuminate\Database\Seeder;

class TamhubTableSeeder extends Seeder
{
    public function run()
    {
        $Tamhub = [
            [
                
                'id'                    => 1,
                'organisation_name'     => 'CADRRE',
                'resource_category_id'  => 1,
                'city'                  => 'Trivandrum',
                'areas'                 => 'Autism Spectrum Disorders Only',
                'services'              => "Assessments, Speech therapy, Occupational therapy, 
                                             Special education, Group therapy, art/music/yoga therapy,
                                             family support and orientation, employability training, 
                                             remedial intervention",
                'special_note'          => '',
                'contact_no'            => '9207450001',
                'email_id'              => 'NA',
                'website_link'          => 'https://cadrre.org/',
                'address'               => "T C 12/43, PMG-Plamudu Rd.,Trivandrum - 695004, Kerala, India
                                            TC 22/985, Sasthamangalam Junction,  Trivandrum – 695010 Kerala",   
            ],
            [
                
                'id'                    => 2,
                'organisation_name'     => 'Amrita Hospital',
                'resource_category_id'  => 1,
                'city'                  => 'Kochi',
                'areas'                 => "Behavioural disorders,neurological disorders, 
                                             SLD, any other developmental delays",
                'services'              => "Occupational therapy, speech and language therapy,physiotherapy, behavioural therapy, special education, family counseling",
                'special_note'          => "Child Development Centre(C Block, 5th floor)",
                'contact_no'            => "484 668 2100 484 285 2100",
                'email_id'              => "customerservice@aims.amrita.edu",
                'website_link'          => "https://amritahospitals.org/",
                'address'               => "Amrita Institute of Medical Sciences, Ponekkara, AIMS (P.O.), Kochi 682 041, Kerala",   
            ],
        ];

        Tamhub::insert($Tamhub);
    }
}
