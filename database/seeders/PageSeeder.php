<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pages = [
            [
                'name'             => 'Privacy Policy',
                'slug'             => 'privacy-policy',
                'icon'             => 'ph-shield-check',
                'title'            => 'Privacy Policy - Meal Manager',
                'content'          => '<h4>1. Introduction</h4>
<p>Welcome to <strong>Meal Manager</strong>, your trusted platform for managing bachelor messes, shared flats, hostels, and group dining expenses. We value your trust and are dedicated to safeguarding your personal information. This Privacy Policy outlines how Meal Manager collects, uses, stores, and protects your data when you use our mobile application and web services.</p>

<h4>2. Information We Collect</h4>
<p>To provide accurate meal tracking, expense sharing, and transparent accounting, we collect the following types of information:</p>
<ul>
    <li><strong>Personal Profile:</strong> Full name, phone number, email address, password (encrypted), and profile picture.</li>
    <li><strong>Mess & Room Data:</strong> Mess name, location/address, room number, joining date, and member roles (e.g., Owner, Manager, Member).</li>
    <li><strong>Meal & Dining Records:</strong> Daily meal inputs (Breakfast, Lunch, Dinner, Guest meals), meal statuses, and meal on/off schedules.</li>
    <li><strong>Financial & Expense Data:</strong> Daily market expenses (bazar costs, shopping item lists, receipts), member advance deposits, monthly rents, and utility contributions.</li>
    <li><strong>Device & Communication Data:</strong> Push notification tokens (Firebase Cloud Messaging - FCM) to send timely alerts about market duties, meal confirmations, and month-end bills.</li>
</ul>

<h4>3. How We Use Your Information</h4>
<p>We use the collected information exclusively to facilitate shared living management, including:</p>
<ul>
    <li>Calculating dynamic daily and monthly meal rates automatically (Total Mess Cost &divide; Total Consumed Meals).</li>
    <li>Generating transparent monthly settlement statements, user bill summaries, and carrying forward closing balances.</li>
    <li>Assigning and scheduling market shopping duties (Bazar Schedules) and notifying assigned members.</li>
    <li>Maintaining an open community notice board and polling system for mess decisions.</li>
    <li>Authenticating user sessions securely and preventing unauthorized access.</li>
</ul>

<h4>4. Information Sharing & Privacy Among Members</h4>
<p>Because Meal Manager is a collaborative mess management tool, certain information (such as your daily meal count, deposit amounts, assigned bazar dates, and final monthly bills) is visible to fellow members and managers within your designated mess. We do <strong>not</strong> sell, rent, or share your personal data with third-party advertisers or external entities.</p>

<h4>5. Data Security & Storage</h4>
<p>We implement industry-standard security measures, including HTTPS/TLS encryption for all data in transit, salted bcrypt password hashing, and restricted database access. Your financial transactions and personal records are backed up and protected against unauthorized modification.</p>

<h4>6. Data Retention & Account Control</h4>
<p>You maintain full control over your data. You may update your profile details, leave a mess upon clearing your dues, or request complete account deletion at any time through the app settings or by contacting our support team.</p>

<h4>7. Contact Us</h4>
<p>If you have any questions, suggestions, or concerns regarding this Privacy Policy or your data, please contact our support team at <a href="mailto:support@mealmanager.app">support@mealmanager.app</a>.</p>',
                'meta_title'       => 'Privacy Policy | Meal Manager',
                'meta_description' => 'Learn how Meal Manager protects your personal, mess, meal, and financial information.',
                'meta_keywords'    => 'meal manager, privacy policy, mess privacy, data security, meal expense sharing',
                'status'           => 'active',
            ],
            [
                'name'             => 'Terms and Conditions',
                'slug'             => 'terms-and-conditions',
                'icon'             => 'ph-file-text',
                'title'            => 'Terms and Conditions - Meal Manager',
                'content'          => '<h4>1. Acceptance of Terms</h4>
<p>By downloading, registering, or accessing <strong>Meal Manager</strong>, you agree to comply with and be bound by these Terms and Conditions. If you do not agree to these terms, please do not use the application.</p>

<h4>2. User Accounts & Eligibility</h4>
<p>You must provide accurate and verifiable information during registration, including a valid phone number. You are responsible for safeguarding your login credentials and for all activities that occur under your account.</p>

<h4>3. Mess Membership & Roles</h4>
<p>Meal Manager operates on a role-based structure within individual messes:</p>
<ul>
    <li><strong>Owners & Managers:</strong> Responsible for maintaining fair records, logging verified bazar costs, entering member deposits, setting market schedules, and finalizing monthly billing periods.</li>
    <li><strong>Members:</strong> Responsible for inputting daily meal preferences prior to cutoff times, attending assigned shopping duties, and clearing monthly bill dues in a timely manner.</li>
</ul>

<h4>4. Financial Calculations & Disclaimer</h4>
<p>Meal Manager functions as an automated calculation, record-keeping, and accounting assistant for shared living expenses. The platform does not hold user funds or act as an escrow agent. Actual monetary transactions, deposits, and refunds are conducted directly between mess members and managers via cash or agreed external payment channels. Meal Manager is not liable for interpersonal disputes or unpaid balances between mess members.</p>

<h4>5. Meal Count & Daily Deadlines</h4>
<p>Mess managers have the authority to establish daily deadlines for enabling or disabling meals. Once a month has been finalized and closed by the mess manager, historical meal and expense records for that billing cycle become locked to preserve accounting integrity.</p>

<h4>6. User Conduct & Notice Board</h4>
<p>Users must interact respectfully. Any use of the community notice board, polls, or messaging features for harassment, fraud, hate speech, or defamatory remarks will lead to immediate suspension or termination of access.</p>

<h4>7. Account Termination & Leaving a Mess</h4>
<p>Members can request to leave a mess after settling all outstanding monthly dues with the mess manager. Meal Manager reserves the right to terminate accounts that violate these terms or compromise system security.</p>

<h4>8. Modifications to Terms</h4>
<p>We may periodically update these Terms and Conditions. Continued use of the application following any revisions constitutes your acceptance of the new terms.</p>

<h4>9. Contact Information</h4>
<p>For questions or assistance regarding these terms, please reach out to us at <a href="mailto:legal@mealmanager.app">legal@mealmanager.app</a>.</p>',
                'meta_title'       => 'Terms & Conditions | Meal Manager',
                'meta_description' => 'Read the terms of service and user agreements for Meal Manager application.',
                'meta_keywords'    => 'meal manager terms, terms and conditions, terms of service, mess rules',
                'status'           => 'active',
            ],
            [
                'name'             => 'Terms of Service',
                'slug'             => 'terms-of-service',
                'icon'             => 'ph-file-text',
                'title'            => 'Terms of Service - Meal Manager',
                'content'          => '<h4>1. Acceptance of Terms</h4>
<p>By downloading, registering, or accessing <strong>Meal Manager</strong>, you agree to comply with and be bound by these Terms and Conditions. If you do not agree to these terms, please do not use the application.</p>

<h4>2. User Accounts & Eligibility</h4>
<p>You must provide accurate and verifiable information during registration, including a valid phone number. You are responsible for safeguarding your login credentials and for all activities that occur under your account.</p>

<h4>3. Mess Membership & Roles</h4>
<p>Meal Manager operates on a role-based structure within individual messes:</p>
<ul>
    <li><strong>Owners & Managers:</strong> Responsible for maintaining fair records, logging verified bazar costs, entering member deposits, setting market schedules, and finalizing monthly billing periods.</li>
    <li><strong>Members:</strong> Responsible for inputting daily meal preferences prior to cutoff times, attending assigned shopping duties, and clearing monthly bill dues in a timely manner.</li>
</ul>

<h4>4. Financial Calculations & Disclaimer</h4>
<p>Meal Manager functions as an automated calculation, record-keeping, and accounting assistant for shared living expenses. The platform does not hold user funds or act as an escrow agent. Actual monetary transactions, deposits, and refunds are conducted directly between mess members and managers via cash or agreed external payment channels. Meal Manager is not liable for interpersonal disputes or unpaid balances between mess members.</p>

<h4>5. Meal Count & Daily Deadlines</h4>
<p>Mess managers have the authority to establish daily deadlines for enabling or disabling meals. Once a month has been finalized and closed by the mess manager, historical meal and expense records for that billing cycle become locked to preserve accounting integrity.</p>

<h4>6. User Conduct & Notice Board</h4>
<p>Users must interact respectfully. Any use of the community notice board, polls, or messaging features for harassment, fraud, hate speech, or defamatory remarks will lead to immediate suspension or termination of access.</p>

<h4>7. Account Termination & Leaving a Mess</h4>
<p>Members can request to leave a mess after settling all outstanding monthly dues with the mess manager. Meal Manager reserves the right to terminate accounts that violate these terms or compromise system security.</p>

<h4>8. Modifications to Terms</h4>
<p>We may periodically update these Terms and Conditions. Continued use of the application following any revisions constitutes your acceptance of the new terms.</p>

<h4>9. Contact Information</h4>
<p>For questions or assistance regarding these terms, please reach out to us at <a href="mailto:legal@mealmanager.app">legal@mealmanager.app</a>.</p>',
                'meta_title'       => 'Terms of Service | Meal Manager',
                'meta_description' => 'Read the terms of service and user agreements for Meal Manager application.',
                'meta_keywords'    => 'meal manager terms, terms and conditions, terms of service, mess rules',
                'status'           => 'active',
            ],
            [
                'name'             => 'About Us',
                'slug'             => 'about-us',
                'icon'             => 'ph-users-three',
                'title'            => 'About Meal Manager',
                'content'          => '<h4>About Meal Manager</h4>
<p><strong>Meal Manager</strong> is a modern, feature-rich meal and mess expense management solution built specifically for students, bachelors, flatmates, and hostel residents. Our goal is to eliminate calculation headaches, prevent misunderstandings, and make shared living hassle-free, transparent, and enjoyable.</p>

<h4>Key Highlights:</h4>
<ul>
    <li>Instant, automated meal rate calculation.</li>
    <li>Real-time market shopping (Bazar) duty schedule and push notification reminders.</li>
    <li>Digital notice board and voting polls for democratic decisions.</li>
    <li>Individual bill breakdown and seamless carry-over balance management across months.</li>
</ul>',
                'meta_title'       => 'About Us | Meal Manager',
                'meta_description' => 'Discover the mission and features of Meal Manager mess management system.',
                'meta_keywords'    => 'about meal manager, bachelor mess management, hostel meal app',
                'status'           => 'active',
            ],
            [
                'name'             => 'FAQ',
                'slug'             => 'faq',
                'icon'             => 'ph-question',
                'title'            => 'Frequently Asked Questions',
                'content'          => '<h4>1. How is the monthly meal rate calculated?</h4>
<p>The meal rate is calculated by dividing total mess expenses by the total number of meals consumed by all members: <code>Meal Rate = Total Expenses &divide; Total Meals</code>.</p>

<h4>2. Who can finalize the month?</h4>
<p>Only the assigned Mess Manager or Owner has the authority to generate the final monthly report and lock that billing month.</p>

<h4>3. Can I update meals after a month is closed?</h4>
<p>No. Once a month is finalized, meal records are locked to protect the integrity of financial settlements.</p>

<h4>4. How does the Bazar Schedule notification work?</h4>
<p>Assigned members receive automated push notifications on their mobile devices reminding them of their upcoming shopping duty date.</p>',
                'meta_title'       => 'FAQ | Meal Manager',
                'meta_description' => 'Frequently asked questions about Meal Manager app.',
                'meta_keywords'    => 'meal manager faq, questions, help',
                'status'           => 'active',
            ],
        ];

        foreach ($pages as $page) {
            Page::updateOrCreate(
                ['slug' => $page['slug']],
                $page
            );
        }
    }
}
