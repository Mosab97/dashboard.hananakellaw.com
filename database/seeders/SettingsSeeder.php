<?php

namespace Database\Seeders;

use Akaunting\Setting\Facade as Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run()
    {
        $settings = [
            'currency' => 'ILS',
            'privacy_policy' => [
                'en' => 'Your privacy is important to us, and we are committed to safeguarding your personal information. This Privacy Policy explains how we collect, use, store, and protect your data when you access our platform. We collect personal details such as your name, email address, and usage data to enhance your experience and provide better services. Your data will only be used for legitimate purposes and will not be shared with third parties without your consent, unless required by law or for essential service delivery. We implement industry-standard security measures to ensure your data is secure and protected from unauthorized access. You have the right to request access to, modify, or delete any personal data we hold about you. We strive to be transparent and clear in our communication, and we will notify you of any changes to our privacy practices. By using our platform, you consent to the practices described in this policy. If you have any questions or concerns, please feel free to contact us at any time.',

                'ar' => 'خصوصيتك مهمة بالنسبة لنا، ونحن ملتزمون بحماية معلوماتك الشخصية. توضح سياسة الخصوصية هذه كيفية جمع واستخدام وتخزين وحماية بياناتك عند الوصول إلى منصتنا. نحن نجمع التفاصيل الشخصية مثل اسمك وعنوان بريدك الإلكتروني وبيانات الاستخدام لتحسين تجربتك وتقديم خدمات أفضل. سيتم استخدام بياناتك فقط للأغراض المشروعة ولن تتم مشاركتها مع أطراف ثالثة دون موافقتك، ما لم يكن ذلك مطلوبًا بموجب القانون أو لتقديم الخدمة الأساسية. نحن نطبق تدابير أمنية وفقًا لمعايير الصناعة لضمان أمان بياناتك وحمايتها من الوصول غير المصرح به. لديك الحق في طلب الوصول إلى أو تعديل أو حذف أي بيانات شخصية نحتفظ بها عنك. نحن نسعى جاهدين لنكون شفافين وواضحين في تواصلنا، وسنخطرك بأي تغييرات في ممارسات الخصوصية لدينا. باستخدام منصتنا، فإنك توافق على الممارسات الموضحة في هذه السياسة. إذا كانت لديك أي أسئلة أو مخاوف، فلا تتردد في الاتصال بنا في أي وقت.',
            ],

            'terms_conditions' => [
                'en' => 'Your privacy is important to us, and we are committed to safeguarding your personal information. This Privacy Policy explains how we collect, use, store, and protect your data when you access our platform. We collect personal details such as your name, email address, and usage data to enhance your experience and provide better services. Your data will only be used for legitimate purposes and will not be shared with third parties without your consent, unless required by law or for essential service delivery. We implement industry-standard security measures to ensure your data is secure and protected from unauthorized access. You have the right to request access to, modify, or delete any personal data we hold about you. We strive to be transparent and clear in our communication, and we will notify you of any changes to our privacy practices. By using our platform, you consent to the practices described in this policy. If you have any questions or concerns, please feel free to contact us at any time.',

                'ar' => 'خصوصيتك مهمة بالنسبة لنا، ونحن ملتزمون بحماية معلوماتك الشخصية. توضح سياسة الخصوصية هذه كيفية جمع واستخدام وتخزين وحماية بياناتك عند الوصول إلى منصتنا. نحن نجمع التفاصيل الشخصية مثل اسمك وعنوان بريدك الإلكتروني وبيانات الاستخدام لتحسين تجربتك وتقديم خدمات أفضل. سيتم استخدام بياناتك فقط للأغراض المشروعة ولن تتم مشاركتها مع أطراف ثالثة دون موافقتك، ما لم يكن ذلك مطلوبًا بموجب القانون أو لتقديم الخدمة الأساسية. نحن نطبق تدابير أمنية وفقًا لمعايير الصناعة لضمان أمان بياناتك وحمايتها من الوصول غير المصرح به. لديك الحق في طلب الوصول إلى أو تعديل أو حذف أي بيانات شخصية نحتفظ بها عنك. نحن نسعى جاهدين لنكون شفافين وواضحين في تواصلنا، وسنخطرك بأي تغييرات في ممارسات الخصوصية لدينا. باستخدام منصتنا، فإنك توافق على الممارسات الموضحة في هذه السياسة. إذا كانت لديك أي أسئلة أو مخاوف، فلا تتردد في الاتصال بنا في أي وقت.',
            ],

            'social_facebook' => 'https://facebook.com/yourpage',
            'social_instagram' => 'https://instagram.com/yourpage',
            'social_twitter' => 'https://twitter.com/yourpage',

            // Add WhatsApp message limits
            'whatsapp_message_limit_system' => 1000, // Default system-wide limit per month
            'whatsapp_message_price' => 0.10, // Cost per message in your currency
            'whatsapp_reset_period' => 'monthly', // Options: daily, weekly, monthly
            // Mobile app settings - single version for both platforms
            'mobile_app_version' => '1.0.0',
            'mobile_app_link_android' => 'https://play.google.com/store/apps/details?id=your.app.package',
            'mobile_app_link_ios' => 'https://apps.apple.com/app/your-app-id',

        ];

        foreach ($settings as $key => $value) {
            Setting::set($key, $value);
        }

        Setting::save();
    }
}
