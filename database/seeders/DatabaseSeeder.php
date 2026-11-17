<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\BusinessSetting;
use App\Models\EventType;
use App\Models\Venue;
use App\Models\DecorationCategory;
use App\Models\ExpenseCategory;
use App\Models\Customer;
use App\Models\Booking;
use App\Models\BookingPayment;
use App\Models\Expense;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Admin & Staff Users
        $admin = User::firstOrCreate(
            ['email' => 'admin@stagedecor.pk'],
            [
                'name' => 'Admin Manager',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        $staff = User::firstOrCreate(
            ['email' => 'staff@stagedecor.pk'],
            [
                'name' => 'Operational Staff',
                'password' => Hash::make('password123'),
                'role' => 'staff',
                'is_active' => true,
            ]
        );

        // 2. Business Settings
        BusinessSetting::current();

        // 3. Event Types
        $eventTypes = [
            'Wedding' => 'Main wedding reception & ceremony',
            'Barat' => 'Traditional Barat entrance and stage setup',
            'Walima' => 'Grand reception setup with formal seating',
            'Mehndi' => 'Vibrant colorful setup with dholak and stage swing',
            'Engagement' => 'Intimate and elegant floral stage',
            'Birthday' => 'Themed birthday party backdrops and props',
            'Corporate Event' => 'Professional backdrop, rostrum, and lighting',
            'Other' => 'Custom gatherings and ceremonies',
        ];

        $eventModels = [];
        foreach ($eventTypes as $name => $desc) {
            $eventModels[$name] = EventType::firstOrCreate(
                ['name' => $name],
                ['description' => $desc, 'is_active' => true]
            );
        }

        // 4. Venues
        $venuesData = [
            [
                'name' => 'Royal Palm Golf & Country Club',
                'venue_type' => 'Hotel',
                'address' => '52 Canal Bank Road, Lahore',
                'contact_number' => '042-111602602',
                'notes' => 'Bab-ul-Shams hall, grand indoor capacity.',
            ],
            [
                'name' => "Faletti's Grand Banquet",
                'venue_type' => 'Banquet Hall',
                'address' => 'Egerton Road, Lahore',
                'contact_number' => '042-36363636',
                'notes' => 'Central indoor ballroom with high ceilings.',
            ],
            [
                'name' => 'Pearl Continental Crystal Ballroom',
                'venue_type' => 'Hotel',
                'address' => 'Mall Road, Lahore',
                'contact_number' => '042-111505505',
                'notes' => 'Luxury indoor hotel ballroom.',
            ],
            [
                'name' => 'Nishat Marquee',
                'venue_type' => 'Banquet Hall',
                'address' => 'Emporium Mall Complex, Johar Town, Lahore',
                'contact_number' => '042-35777777',
                'notes' => 'Spacious marquee with air conditioning.',
            ],
            [
                'name' => 'Model Town Community Hall',
                'venue_type' => 'Marriage Hall',
                'address' => 'Club Road, Model Town, Lahore',
                'contact_number' => '042-35882200',
                'notes' => 'Community hall suitable for medium gatherings.',
            ],
            [
                'name' => 'Client Residence Lawn',
                'venue_type' => 'Home',
                'address' => 'DHA Phase 5, Lahore',
                'contact_number' => '0300-9876543',
                'notes' => 'Outdoor home garden / lawn setup.',
            ],
            [
                'name' => 'Mughal Garden Open Grounds',
                'venue_type' => 'Outdoor Ground',
                'address' => 'Bedian Road, Lahore Cantt',
                'contact_number' => '0321-4455667',
                'notes' => 'Large open lawn for outdoor tents & stages.',
            ],
        ];

        $venueModels = [];
        foreach ($venuesData as $v) {
            $venueModels[$v['name']] = Venue::firstOrCreate(['name' => $v['name']], $v);
        }

        // 5. Decoration Categories
        $decorationCategoriesData = [
            [
                'name' => 'Basic Stage',
                'description' => 'Fabric drape backdrop, standard sofa set, carpet and warm focus lights.',
                'base_price' => 45000.00,
            ],
            [
                'name' => 'Premium Stage',
                'description' => 'Geometric wooden panels, artificial floral border, luxury chandelier & LED pars.',
                'base_price' => 85000.00,
            ],
            [
                'name' => 'Luxury Stage',
                'description' => 'Complete fresh floral wall, imported velvet seating, custom truss & ambient movers.',
                'base_price' => 150000.00,
            ],
            [
                'name' => 'Floral Decoration',
                'description' => 'Fresh imported roses, lilies, baby breath stage borders and grand walkway pillars.',
                'base_price' => 65000.00,
            ],
            [
                'name' => 'Custom Decoration',
                'description' => 'Theme-based custom archways, acrylic stage flooring, neon monogram & laser lights.',
                'base_price' => 110000.00,
            ],
            [
                'name' => 'Other',
                'description' => 'Minimal or standalone backdrop setup tailored to budget.',
                'base_price' => 30000.00,
            ],
        ];

        $decorModels = [];
        foreach ($decorationCategoriesData as $dc) {
            $decorModels[$dc['name']] = DecorationCategory::firstOrCreate(['name' => $dc['name']], $dc);
        }

        // 6. Expense Categories
        $expenseCategoriesData = [
            'Flowers' => 'Fresh and imported flowers, garlands, and floral foam',
            'Decoration Material' => 'Fabrics, drapery, carpets, props, backdrops, and ribbons',
            'Labour Charges' => 'Daily wages for setup, dismantling, and on-site decorators',
            'Transportation' => 'Loading, pickup, truck fuel, and logistics',
            'Lighting and Electrical' => 'LED pars, sharpies, warm spotlights, fairy lights, cables',
            'Equipment Rental' => 'Trusses, generators, sound PA, smoke machines, and lifts',
            'Stage Material' => 'Plywood, metal frames, acrylic sheets, foam sheets, adhesives',
            'Refreshments' => 'Staff food, tea, and on-site drinking water during setup',
            'Miscellaneous' => 'General petty cash, tapes, ties, and minor items',
            'Other' => 'Uncategorized emergency event expenses',
        ];

        $expCatModels = [];
        foreach ($expenseCategoriesData as $name => $desc) {
            $expCatModels[$name] = ExpenseCategory::firstOrCreate(
                ['name' => $name],
                ['description' => $desc, 'is_active' => true]
            );
        }

        // 7. Sample Customers
        $customersData = [
            ['name' => 'Chaudhry Tariq Mehmood', 'mobile_number' => '0300-4567890', 'alternate_mobile' => '0321-9876543', 'address' => 'House 12, Block J, Gulberg III, Lahore'],
            ['name' => 'Mrs. Nabila Farooq', 'mobile_number' => '0301-2345678', 'alternate_mobile' => '0333-1122334', 'address' => '24-C, Sector G, Phase 5, DHA Lahore'],
            ['name' => 'Malik Usman Rafiq', 'mobile_number' => '0322-8765432', 'alternate_mobile' => null, 'address' => 'Wapda Town, Phase 1, Lahore'],
            ['name' => 'Engr. Kamran Siddiqui', 'mobile_number' => '0302-3344556', 'alternate_mobile' => '0300-7788990', 'address' => 'Johar Town, Block F2, Lahore'],
            ['name' => 'Haji Bashir Sons Jewellers', 'mobile_number' => '0305-9988776', 'alternate_mobile' => null, 'address' => 'Anarkali Bazaar, Lahore'],
        ];

        $customerModels = [];
        foreach ($customersData as $c) {
            $customerModels[] = Customer::firstOrCreate(['mobile_number' => $c['mobile_number']], $c);
        }

        // 8. Sample Bookings, Payments, and Expenses
        $today = Carbon::today();

        // Booking 1: Today's Confirmed Event
        $b1 = Booking::firstOrCreate(
            ['booking_number' => 'STG-202611-0001'],
            [
                'customer_id' => $customerModels[0]->id,
                'event_type_id' => $eventModels['Barat']->id,
                'venue_id' => $venueModels["Faletti's Grand Banquet"]->id,
                'decoration_category_id' => $decorModels['Luxury Stage']->id,
                'booking_type' => 'indoor',
                'booking_date' => $today->copy()->subDays(10)->toDateString(),
                'event_date' => $today->toDateString(),
                'event_start_time' => '19:30',
                'event_end_time' => '23:45',
                'venue_address' => "Faletti's Grand Hall, Egerton Road",
                'decoration_details' => 'White and champagne gold theme, fresh import roses floral wall, luxury crystal chandeliers',
                'total_amount' => 160000.00,
                'discount' => 10000.00,
                'final_amount' => 150000.00,
                'status' => 'confirmed',
                'created_by' => $admin->id,
            ]
        );

        BookingPayment::firstOrCreate(
            ['receipt_number' => 'REC-202611-0001'],
            [
                'booking_id' => $b1->id,
                'customer_id' => $b1->customer_id,
                'payment_date' => $today->copy()->subDays(10)->toDateString(),
                'amount' => 75000.00,
                'payment_type' => 'advance',
                'payment_method' => 'bank',
                'transaction_reference' => 'HBL-DEP-99441',
                'remarks' => '50% advance received via online bank transfer',
                'received_by' => $admin->id,
                'is_refund' => false,
            ]
        );

        Expense::firstOrCreate(
            ['expense_number' => 'EXP-202611-0001'],
            [
                'expense_category_id' => $expCatModels['Flowers']->id,
                'booking_id' => $b1->id,
                'expense_date' => $today->toDateString(),
                'description' => '80 bundles imported red roses & baby breath from Mandi',
                'amount' => 38000.00,
                'payment_method' => 'cash',
                'recorded_by' => $admin->id,
            ]
        );

        Expense::firstOrCreate(
            ['expense_number' => 'EXP-202611-0002'],
            [
                'expense_category_id' => $expCatModels['Labour Charges']->id,
                'booking_id' => $b1->id,
                'expense_date' => $today->toDateString(),
                'description' => '6 workers setup & teardown daily wages',
                'amount' => 12000.00,
                'payment_method' => 'cash',
                'recorded_by' => $admin->id,
            ]
        );

        // Booking 2: Upcoming Confirmed Outdoor Event (in 3 days)
        $b2 = Booking::firstOrCreate(
            ['booking_number' => 'STG-202611-0002'],
            [
                'customer_id' => $customerModels[1]->id,
                'event_type_id' => $eventModels['Mehndi']->id,
                'venue_id' => $venueModels['Client Residence Lawn']->id,
                'decoration_category_id' => $decorModels['Floral Decoration']->id,
                'booking_type' => 'outdoor',
                'booking_date' => $today->copy()->subDays(5)->toDateString(),
                'event_date' => $today->copy()->addDays(3)->toDateString(),
                'event_start_time' => '18:00',
                'event_end_time' => '22:30',
                'venue_address' => 'Lawn 24-C, Sector G, Phase 5, DHA Lahore',
                'decoration_details' => 'Yellow marigold swing with fairy lights canopy & low floor cushion seating',
                'total_amount' => 95000.00,
                'discount' => 5000.00,
                'final_amount' => 90000.00,
                'status' => 'confirmed',
                'created_by' => $staff->id,
            ]
        );

        BookingPayment::firstOrCreate(
            ['receipt_number' => 'REC-202611-0002'],
            [
                'booking_id' => $b2->id,
                'customer_id' => $b2->customer_id,
                'payment_date' => $today->copy()->subDays(5)->toDateString(),
                'amount' => 50000.00,
                'payment_type' => 'advance',
                'payment_method' => 'cash',
                'transaction_reference' => 'Cash handed to manager',
                'remarks' => 'Cash advance payment',
                'received_by' => $staff->id,
                'is_refund' => false,
            ]
        );

        // Booking 3: Upcoming Tentative Event (in 6 days)
        Booking::firstOrCreate(
            ['booking_number' => 'STG-202611-0003'],
            [
                'customer_id' => $customerModels[2]->id,
                'event_type_id' => $eventModels['Walima']->id,
                'venue_id' => $venueModels['Royal Palm Golf & Country Club']->id,
                'decoration_category_id' => $decorModels['Premium Stage']->id,
                'booking_type' => 'indoor',
                'booking_date' => $today->copy()->subDays(2)->toDateString(),
                'event_date' => $today->copy()->addDays(6)->toDateString(),
                'event_start_time' => '20:00',
                'event_end_time' => '00:00',
                'venue_address' => 'Bab-ul-Shams hall, Royal Palm',
                'decoration_details' => 'Geometric gold arches with white faux floral borders and ambient warm pars',
                'total_amount' => 110000.00,
                'discount' => 0.00,
                'final_amount' => 110000.00,
                'status' => 'tentative',
                'created_by' => $admin->id,
            ]
        );

        // Booking 4: Completed Event in Past (Fully Paid)
        $b4 = Booking::firstOrCreate(
            ['booking_number' => 'STG-202611-0004'],
            [
                'customer_id' => $customerModels[3]->id,
                'event_type_id' => $eventModels['Corporate Event']->id,
                'venue_id' => $venueModels['Pearl Continental Crystal Ballroom']->id,
                'decoration_category_id' => $decorModels['Custom Decoration']->id,
                'booking_type' => 'indoor',
                'booking_date' => $today->copy()->subDays(20)->toDateString(),
                'event_date' => $today->copy()->subDays(7)->toDateString(),
                'event_start_time' => '10:00',
                'event_end_time' => '17:00',
                'venue_address' => 'Crystal Ballroom, PC Hotel Lahore',
                'decoration_details' => 'Annual corporate award stage with acrylic rostrum, LED backdrop frame and spot movers',
                'total_amount' => 125000.00,
                'discount' => 5000.00,
                'final_amount' => 120000.00,
                'status' => 'completed',
                'created_by' => $admin->id,
            ]
        );

        BookingPayment::firstOrCreate(
            ['receipt_number' => 'REC-202611-0003'],
            [
                'booking_id' => $b4->id,
                'customer_id' => $b4->customer_id,
                'payment_date' => $today->copy()->subDays(20)->toDateString(),
                'amount' => 60000.00,
                'payment_type' => 'advance',
                'payment_method' => 'bank',
                'transaction_reference' => 'CORP-PO-4581',
                'remarks' => 'PO advance cleared',
                'received_by' => $admin->id,
                'is_refund' => false,
            ]
        );

        BookingPayment::firstOrCreate(
            ['receipt_number' => 'REC-202611-0004'],
            [
                'booking_id' => $b4->id,
                'customer_id' => $b4->customer_id,
                'payment_date' => $today->copy()->subDays(6)->toDateString(),
                'amount' => 60000.00,
                'payment_type' => 'final',
                'payment_method' => 'bank',
                'transaction_reference' => 'CORP-INV-CLEAR-91',
                'remarks' => 'Final invoice clearance on event completion',
                'received_by' => $admin->id,
                'is_refund' => false,
            ]
        );

        Expense::firstOrCreate(
            ['expense_number' => 'EXP-202611-0003'],
            [
                'expense_category_id' => $expCatModels['Equipment Rental']->id,
                'booking_id' => $b4->id,
                'expense_date' => $today->copy()->subDays(7)->toDateString(),
                'description' => 'Heavy duty truss rig and spotlight rental',
                'amount' => 22000.00,
                'payment_method' => 'bank',
                'recorded_by' => $admin->id,
            ]
        );

        // General Business Overhead Expense
        Expense::firstOrCreate(
            ['expense_number' => 'EXP-202611-0004'],
            [
                'expense_category_id' => $expCatModels['Transportation']->id,
                'booking_id' => null,
                'expense_date' => $today->copy()->subDays(3)->toDateString(),
                'description' => 'Monthly diesel for Shehzore pickup truck',
                'amount' => 15000.00,
                'payment_method' => 'cash',
                'recorded_by' => $admin->id,
            ]
        );
    }
}
