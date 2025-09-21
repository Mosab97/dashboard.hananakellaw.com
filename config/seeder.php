<?php

return [
    'members' => [
        'schools' => [
            [
                'name' => [
                    'en' => 'ABC International School',
                    'ar' => 'مدرسة أي بي سي الدولية',
                ],
                'email' => 'school@gmail.com',
                'country_code' => '+20',
                'phone_number' => '20156589632',
                'profile' => [
                    'whatsapp' => '+2020156589632',
                    'ministry_number' => 'SCH001',
                    'time_start' => '07:30:00',
                    'time_end' => '14:30:00',
                    'location_description' => [
                        'en' => 'Near City Center',
                        'ar' => 'بالقرب من وسط المدينة',
                    ],
                    'lat' => 24.7136,
                    'lng' => 46.6753,
                    'description' => 'Leading international school',
                ],
            ],
            [
                'name' => [
                    'en' => 'Al-Noor Academy',
                    'ar' => 'أكاديمية النور',
                ],
                'email' => 'mosab@s.com',
                'country_code' => '+20',
                'phone_number' => '20156589632',
                'profile' => [
                    'whatsapp' => '+2020156589632',
                    'ministry_number' => 'SCH002',
                    'time_start' => '08:00:00',
                    'time_end' => '15:00:00',
                    'location_description' => [
                        'en' => 'Eastern District',
                        'ar' => 'المنطقة الشرقية',
                    ],
                    'lat' => 24.7236,
                    'lng' => 46.6853,
                    'description' => 'Excellence in education',
                ],
            ],
            [
                'name' => [
                    'en' => 'Smart Vision School',
                    'ar' => 'مدرسة الرؤية الذكية',
                ],
                'email' => 'smart.vision@example.com',
                'country_code' => '+966',
                'phone_number' => '505678901',
            ],
        ],
        'teachers' => [
            [
                'name' => [
                    'en' => 'teacher Johnson',
                    'ar' => 'teacher جونسون',
                ],
                'email' => 'mosab@t.com',
                'country_code' => '+970',
                'phone_number' => '592879186',
                'profile' => [
                    'id_number' => 'T'.random_int(10000000, 99999999),
                    'age' => 35,
                    'graduation_date' => '2010-06-15',
                    'location_description' => [
                        'en' => 'Downtown Area',
                        'ar' => 'وسط المدينة',
                    ],
                    'lat' => 24.7136,
                    'lng' => 46.6753,
                    'assigned_school' => 'mosab@s.com', // Reference to school email

                ],
                'fcm_token' => 'fVK3rhyYQeGVAy8O4diGe6:APA91bED0oBJw3hSX6wUk0To0eg5FppflMSufPj_nadfZ4JgIC7a8XjyF1VaDS_27R3a-0vTPVuxOeK-gFoIxXEQ_7HCk7UI5uktsTKI04JmofQs1LszrYo',

            ],
            [
                'name' => [
                    'en' => 'Sarah Johnson',
                    'ar' => 'سارة جونسون',
                ],
                'email' => 'teacher@gmail.com',
                'country_code' => '+20',
                'phone_number' => '20156589632',
                'fcm_token' => 'fVK3rhyYQeGVAy8O4diGe6:APA91bED0oBJw3hSX6wUk0To0eg5FppflMSufPj_nadfZ4JgIC7a8XjyF1VaDS_27R3a-0vTPVuxOeK-gFoIxXEQ_7HCk7UI5uktsTKI04JmofQs1LszrYo',
                'profile' => [
                    'id_number' => 'T'.random_int(10000000, 99999999),
                    'age' => 28,
                    'graduation_date' => '2018-05-20',
                    'location_description' => [
                        'en' => 'North District',
                        'ar' => 'الحي الشمالي',
                    ],
                    'lat' => 24.7236,
                    'lng' => 46.6853,
                    'assigned_school' => 'school@gmail.com', // Reference to school email

                ],
            ],
            [
                'name' => [
                    'en' => 'Abdullah Al-Saud',
                    'ar' => 'عبدالله آل سعود',
                ],
                'email' => 'abdullah.s@example.com',
                'country_code' => '+966',
                'phone_number' => '509012345',
                'profile' => [
                    'id_number' => 'T'.random_int(10000000, 99999999),
                    'age' => 28,
                    'graduation_date' => '2018-05-20',
                    'location_description' => [
                        'en' => 'North District',
                        'ar' => 'الحي الشمالي',
                    ],
                    'lat' => 24.7236,
                    'lng' => 46.6853,
                    'assigned_school' => 'school@gmail.com', // Reference to school email

                ],
            ],

        ],

        'defaults' => [
            'password' => '123$Test',
            'preferred_language' => 'ar',
            'is_verified' => true,
            'active' => true,
            'school_avatar' => 'media/avatars/school.png',
            'teacher_avatar' => 'media/avatars/teacher.jpg',
        ],
    ],
    'students' => [
        [
            'name' => [
                'en' => 'Student One ABC',
                'ar' => 'طالب واحد أي بي سي',
            ],
            'id_number' => 'SABC123456789',
            'phone_number' => '1012345678',
            'email' => 'student1@abc.com',
            'microsoft_email' => 'student1@abc.edu.sa',
            'mother_contact_number' => '1098765432',
            'place_of_birth' => 'Cairo',
            'date_of_birth' => '2010-01-01',
            'nationality_id' => 1,
            'residence_permit_number' => 'RP123456789',
            'residence_permit_date' => '2022-01-01',
            'residence_permit_expiry_date' => '2025-01-01',
            'home_phone' => '0212345678',
            'relative_name' => 'Relative One',
            'relative_contact_number' => '1011223344',
            'relative_address' => 'Cairo Address',
            'guardian' => [
                'name' => [
                    'en' => 'Guardian One ABC',
                    'ar' => 'ولي الأمر واحد أي بي سي',
                ],
                'phone_number' => '1055555555',
                'id_number' => 'GABC111111111',
                'home_phone' => '0211111111',
                'work_phone' => '0222222222',
            ],
            'assigned_school' => 'school@gmail.com', // Reference to school email
        ],
        [
            'name' => [
                'en' => 'Student Two ABC',
                'ar' => 'طالب اثنان أي بي سي',
            ],
            'id_number' => 'SABC987654321',
            'phone_number' => '1056789012',
            'email' => 'student2@abc.com',
            'microsoft_email' => 'student2@abc.edu.sa',
            'mother_contact_number' => '1023456789',
            'place_of_birth' => 'Alexandria',
            'date_of_birth' => '2011-02-02',
            'nationality_id' => 2,
            'residence_permit_number' => 'RP987654321',
            'residence_permit_date' => '2023-02-02',
            'residence_permit_expiry_date' => '2026-02-02',
            'home_phone' => '0298765432',
            'relative_name' => 'Relative Two',
            'relative_contact_number' => '1044332211',
            'relative_address' => 'Alexandria Address',
            'guardian' => [
                'name' => [
                    'en' => 'Guardian Two ABC',
                    'ar' => 'ولي الأمر اثنان أي بي سي',
                ],
                'phone_number' => '1066666666',
                'id_number' => 'GABC222222222',
                'home_phone' => '0233333333',
                'work_phone' => '0244444444',
            ],
            'assigned_school' => 'school@gmail.com', // Reference to school email
        ],
    ],
];
