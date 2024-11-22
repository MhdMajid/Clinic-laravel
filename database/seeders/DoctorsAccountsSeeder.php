<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DoctorsAccountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $doctors = [
            ['doctor_id' => 1, 'name' => 'د. أحمد الكردي', 'email' => 'ahmad@example.com', 'mobile' => '0999999991'],
            ['doctor_id' => 2, 'name' => 'د. سارة يوسف', 'email' => 'sara@example.com', 'mobile' => '0999999992'],
            ['doctor_id' => 3, 'name' => 'د. خالد العلي', 'email' => 'khaled@example.com', 'mobile' => '0999999993'],
            ['doctor_id' => 4, 'name' => 'د. ليلى عيسى', 'email' => 'leila@example.com', 'mobile' => '0999999994'],
            ['doctor_id' => 5, 'name' => 'د. محمود ربيع', 'email' => 'mahmoud@example.com', 'mobile' => '0999999995'],
            ['doctor_id' => 6, 'name' => 'د. أحمد علي', 'email' => 'ahmad.ali@hospital.com', 'mobile' => '0991123456'],
            ['doctor_id' => 7, 'name' => 'د. خالد حسن', 'email' => 'khaled.hassan@hospital.com', 'mobile' => '0992233445'],
            ['doctor_id' => 8, 'name' => 'د. سارة محمود', 'email' => 'sara.mahmoud@hospital.com', 'mobile' => '0993344556'],
            ['doctor_id' => 9, 'name' => 'د. مروان سعيد', 'email' => 'marwan.saeed@hospital.com', 'mobile' => '0994455667'],
            ['doctor_id' => 10, 'name' => 'د. هناء زكريا', 'email' => 'hana.zakaria@hospital.com', 'mobile' => '0995566778'],
            ['doctor_id' => 11, 'name' => 'د. رامي قاسم', 'email' => 'rami.qasim@hospital.com', 'mobile' => '0996677889'],
            ['doctor_id' => 12, 'name' => 'د. علي فهمي', 'email' => 'ali.fahmi@hospital.com', 'mobile' => '0999988776'],
            ['doctor_id' => 13, 'name' => 'د. سمية حسن', 'email' => 'samiya.hassan@hospital.com', 'mobile' => '0997766554'],
            ['doctor_id' => 14, 'name' => 'د. سعيد محمود', 'email' => 'saeed.mahmoud@hospital.com', 'mobile' => '0996655443'],
            ['doctor_id' => 15, 'name' => 'د. إيمان زكريا', 'email' => 'eman.zakaria@hospital.com', 'mobile' => '0995544332'],
            ['doctor_id' => 16, 'name' => 'د. محمد القادري', 'email' => 'mohammad.qadri@hospital.com', 'mobile' => '0995566778'],
            ['doctor_id' => 17, 'name' => 'د. نور الدين الشهابي', 'email' => 'noureddin.shihabi@hospital.com', 'mobile' => '0996677889'],
            ['doctor_id' => 18, 'name' => 'د. هدى صالح', 'email' => 'huda.saleh@hospital.com', 'mobile' => '0999988776'],
            ['doctor_id' => 19, 'name' => 'د. سامر عبد الرحمن', 'email' => 'samer.abdelrahman@hospital.com', 'mobile' => '0997766554'],
            ['doctor_id' => 20, 'name' => 'د. علي يوسف', 'email' => 'ali.youssef@hospital.com', 'mobile' => '0991122334'],
            ['doctor_id' => 21, 'name' => 'د. أسامة الخطيب', 'email' => 'osama.khatib@hospital.com', 'mobile' => '0991122334'],
            ['doctor_id' => 22, 'name' => 'د. رنا الأحمد', 'email' => 'rana.ahmad@hospital.com', 'mobile' => '0992233445'],
            ['doctor_id' => 23, 'name' => 'د. كمال يوسف', 'email' => 'kamal.youssef@hospital.com', 'mobile' => '0993344556'],
            ['doctor_id' => 24, 'name' => 'د. هبة سعيد', 'email' => 'hiba.saeed@hospital.com', 'mobile' => '0994455667'],
            ['doctor_id' => 25, 'name' => 'د. سامي عمر', 'email' => 'sami.omar@hospital.com', 'mobile' => '0995566778'],
            ['doctor_id' => 26, 'name' => 'د. ليلى زهران', 'email' => 'laila.zahran@hospital.com', 'mobile' => '0998877661'],
            ['doctor_id' => 27, 'name' => 'د. محمد سعد', 'email' => 'mohammad.saad@hospital.com', 'mobile' => '0997766554'],
            ['doctor_id' => 28, 'name' => 'د. هالة يوسف', 'email' => 'hala.youssef@hospital.com', 'mobile' => '0996655443'],
            ['doctor_id' => 29, 'name' => 'د. عماد خليل', 'email' => 'imad.khalil@hospital.com', 'mobile' => '0995544332'],
            ['doctor_id' => 30, 'name' => 'د. ريم العباس', 'email' => 'reem.abbas@hospital.com', 'mobile' => '0994433221'],
            ['doctor_id' => 31, 'name' => 'د. خالد عابد', 'email' => 'khaled.abed@hospital.com', 'mobile' => '0999988771'],
            ['doctor_id' => 32, 'name' => 'د. نور الرفاعي', 'email' => 'nour.refai@hospital.com', 'mobile' => '0998877662'],
            ['doctor_id' => 33, 'name' => 'د. غسان الحسين', 'email' => 'ghassan.hussein@hospital.com', 'mobile' => '0997766553'],
            ['doctor_id' => 34, 'name' => 'د. سمر جابر', 'email' => 'samer.jaber@hospital.com', 'mobile' => '0996655444'],
            ['doctor_id' => 35, 'name' => 'د. هبة سمير', 'email' => 'hiba.samir@hospital.com', 'mobile' => '0995544335'],
            ['doctor_id' => 36, 'name' => 'د. رامي ناصر', 'email' => 'rami.naser@hospital.com', 'mobile' => '0999988772'],
            ['doctor_id' => 37, 'name' => 'د. سامي الأسعد', 'email' => 'sami.assad@hospital.com', 'mobile' => '0998877663'],
            ['doctor_id' => 38, 'name' => 'د. هالة إبراهيم', 'email' => 'hala.ibrahim@hospital.com', 'mobile' => '0997766554'],
            ['doctor_id' => 39, 'name' => 'د. أحمد غريب', 'email' => 'ahmad.gharib@hospital.com', 'mobile' => '0996655445'],
            ['doctor_id' => 40, 'name' => 'د. سعاد فوزي', 'email' => 'suhad.fawzi@hospital.com', 'mobile' => '0995544336'],
            ['doctor_id' => 41, 'name' => 'د. ليلى محمود', 'email' => 'layla.mahmoud@hospital.com', 'mobile' => '0999988771'],
            ['doctor_id' => 42, 'name' => 'د. نادر حسن', 'email' => 'nader.hassan@hospital.com', 'mobile' => '0998877662'],
            ['doctor_id' => 43, 'name' => 'د. عبير شرف', 'email' => 'abeer.sharaf@hospital.com', 'mobile' => '0997766553'],
            ['doctor_id' => 44, 'name' => 'د. خالد علي', 'email' => 'khaled.ali@hospital.com', 'mobile' => '0996655444'],
            ['doctor_id' => 45, 'name' => 'د. ميساء فوزي', 'email' => 'maysaa.fawzi@hospital.com', 'mobile' => '0995544335'],
            ['doctor_id' => 46, 'name' => 'د. سامي عواد', 'email' => 'sami.awad@hospital.com', 'mobile' => '0996677881'],
            ['doctor_id' => 47, 'name' => 'د. رشا إبراهيم', 'email' => 'rasha.ibrahim@hospital.com', 'mobile' => '0997766552'],
            ['doctor_id' => 48, 'name' => 'د. وليد حمدان', 'email' => 'walid.hamdan@hospital.com', 'mobile' => '0996655443'],
            ['doctor_id' => 49, 'name' => 'د. سلوى عبد الله', 'email' => 'salwa.abdullah@hospital.com', 'mobile' => '0995544334'],
            ['doctor_id' => 50, 'name' => 'د. جمال خليل', 'email' => 'jamal.khalil@hospital.com', 'mobile' => '0994433225'],
            ['doctor_id' => 51, 'name' => 'د. مازن العلي', 'email' => 'mazen.ali@hospital.com', 'mobile' => '0991112233'],
            ['doctor_id' => 52, 'name' => 'د. هدى الشعار', 'email' => 'huda.shar@hospital.com', 'mobile' => '0992223344'],
            ['doctor_id' => 53, 'name' => 'د. سامر مرعي', 'email' => 'samer.marri@hospital.com', 'mobile' => '0993334455'],
            ['doctor_id' => 54, 'name' => 'د. رانيا حسين', 'email' => 'rania.hussein@hospital.com', 'mobile' => '0994445566'],
            ['doctor_id' => 55, 'name' => 'د. فادي خليل', 'email' => 'fadi.khalil@hospital.com', 'mobile' => '0995556677'],
            ['doctor_id' => 56, 'name' => 'د. سامي علي', 'email' => 'sami.ali@hospital.com', 'mobile' => '0991234567'],
            
            ['doctor_id' => 57, 'name' => 'د. هبة صبري', 'email' => 'hiba.sabri@hospital.com', 'mobile' => '0992345678'],
            ['doctor_id' => 58, 'name' => 'د. عصام يوسف', 'email' => 'issam.youssef@hospital.com', 'mobile' => '0993456789'],
            ['doctor_id' => 59, 'name' => 'د. ليلى حمدان', 'email' => 'leila.hamedan@hospital.com', 'mobile' => '0994567890'],
            ['doctor_id' => 60, 'name' => 'د. كمال إبراهيم', 'email' => 'kamal.ibrahim@hospital.com', 'mobile' => '0995678901'],
            ['doctor_id' => 61, 'name' => 'د. ليلى يوسف', 'email' => 'leila.yusuf@hospital.com', 'mobile' => '0998765432'],
            ['doctor_id' => 62, 'name' => 'د. سامر إسماعيل', 'email' => 'samer.ismail@hospital.com', 'mobile' => '0998765433'],
            
            ['doctor_id' => 63, 'name' => 'د. سمر الحلبي', 'email' => 'samar.halabi@hospital.com', 'mobile' => '0998765434'],
            ['doctor_id' => 64, 'name' => 'د. وسيم الكيلاني', 'email' => 'wasim.kilani@hospital.com', 'mobile' => '0998765435'],
            ['doctor_id' => 65, 'name' => 'د. هدى محمد', 'email' => 'huda.mohammad@hospital.com', 'mobile' => '0998765436'],
            ['doctor_id' => 66, 'name' => 'د. أحمد البني', 'email' => 'ahmed.bani@hospital.com', 'mobile' => '0998765437'],
            ['doctor_id' => 67, 'name' => 'د. مازن صالح', 'email' => 'mazen.saleh@hospital.com', 'mobile' => '0998765438'],
            ['doctor_id' => 68, 'name' => 'د. سمير فواز', 'email' => 'samir.fawaz@hospital.com', 'mobile' => '0998765449'],
            ['doctor_id' => 69, 'name' => 'د. سعاد ناصر', 'email' => 'souad.naser@hospital.com', 'mobile' => '0998765440'],
            ['doctor_id' => 70, 'name' => 'د. نبيل فاضل', 'email' => 'nabil.fadel@hospital.com', 'mobile' => '0998765441'],


            


        ];
        foreach ($doctors as $doctor) {
            DB::table('doctors_accounts')->insert([
                'doctor_id' => $doctor['doctor_id'],
                'name' => $doctor['name'],
                'email' => $doctor['email'],
                'email_verified_at' => now(),
                'password' => Hash::make(substr($doctor['mobile'], 0, 8)), // تشفير الرقم
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
