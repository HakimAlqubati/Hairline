<?php

declare(strict_types=1);

return [
    'brand' => [
        'name' => 'Hairline Salon',
        'subname' => 'Advanced Admin Panel',
    ],

    'nav' => [
        'bookings' => 'Bookings & Scheduling',
        'services_catalog' => 'Services & Pricing Catalog',
        'specialists_team' => 'Specialists & Team',
        'customers_loyalty' => 'Customers & Loyalty',
        'website_content' => 'Website Content & CMS',
        'financials_logistics' => 'Financials & Logistics',
        'system_settings' => 'System Settings & Users',
    ],

    'resources' => [
        // Bookings & Operations
        'booking' => 'Booking',
        'bookings' => 'Bookings',
        'booking_item' => 'Booking Item',
        'booking_items' => 'Booking Items',
        'specialist_shift' => 'Specialist Shift',
        'specialist_shifts' => 'Specialist Shifts',
        'specialist_unavailability' => 'Unavailability / Time Off',
        'specialist_unavailabilities' => 'Unavailability Periods',

        // Services & Catalog
        'service' => 'Service',
        'services' => 'Services',
        'service_category' => 'Service Category',
        'service_categories' => 'Service Categories',
        'service_group' => 'Service Group',
        'service_groups' => 'Service Groups',
        'hair_length' => 'Hair Length',
        'hair_lengths' => 'Hair Lengths',
        'pricing_tier' => 'Pricing Tier',
        'pricing_tiers' => 'Pricing Tiers',
        'catalog_page' => 'Catalog Page',
        'catalog_pages' => 'Catalog Pages',

        // Staff & Specialists
        'specialist' => 'Specialist',
        'specialists' => 'Specialists',

        // Customers & Loyalty
        'customer' => 'Customer',
        'customers' => 'Customers',
        'customer_address' => 'Customer Address',
        'customer_addresses' => 'Customer Addresses',
        'membership_tier' => 'Membership Tier',
        'membership_tiers' => 'Membership Tiers',
        'loyalty_point_transaction' => 'Loyalty Transaction',
        'loyalty_point_transactions' => 'Loyalty Transactions',

        // CMS & Website
        'hero_slide' => 'Hero Slide',
        'hero_slides' => 'Hero Slides',
        'salon_gallery' => 'Gallery Item',
        'salon_galleries' => 'Salon Gallery',
        'founder_story' => 'Founder Story',
        'founder_stories' => 'Founder Stories',
        'cancellation_policy' => 'Cancellation Policy',
        'cancellation_policies' => 'Cancellation Policies',
        'salon_working_hour' => 'Working Hour',
        'salon_working_hours' => 'Salon Working Hours',

        // Financials & Logistics
        'payment' => 'Payment Transaction',
        'payments' => 'Payments',
        'payment_method' => 'Payment Method',
        'payment_methods' => 'Payment Methods',
        'district' => 'District / Zone',
        'districts' => 'Delivery Districts',

        // System
        'salon_setting' => 'Salon Setting',
        'salon_settings' => 'Salon Settings',
        'user' => 'User',
        'users' => 'Admin Users',
    ],

    'sections' => [
        'general_information' => 'General Information',
        'service_details' => 'Service Details & Hierarchy',
        'pricing_and_duration' => 'Pricing & Duration',
        'media' => 'Media & Images',
        'booking_info' => 'Booking & Schedule Details',
        'customer_info' => 'Customer & Contact Details',
        'financial_summary' => 'Financial Summary & Taxes',
        'status_and_notes' => 'Status & Administrative Notes',
        'specialist_profile' => 'Profile & Expertise',
        'work_schedule' => 'Work Schedule & Shifts',
        'contact_and_social' => 'Contact Info & Social Channels',
        'location_and_tax' => 'Location & Tax Information',
        'membership_rewards' => 'Rewards & Membership Privileges',
        'security' => 'Authentication & Security',
    ],

    'fields' => [
        // Common
        'id' => 'ID',
        'name' => 'Name',
        'slug' => 'Slug',
        'title' => 'Title',
        'subtitle' => 'Subtitle',
        'description' => 'Description',
        'notes' => 'Notes',
        'phone' => 'Phone Number',
        'email' => 'Email Address',
        'avatar' => 'Avatar',
        'image' => 'Image',
        'sort_order' => 'Sort Order',
        'is_active' => 'Active',
        'is_featured' => 'Featured',
        'created_at' => 'Created At',
        'updated_at' => 'Updated At',

        // Services & Catalog
        'category' => 'Primary Category',
        'service_group' => 'Service Group',
        'hair_length' => 'Hair Length',
        'base_price' => 'Base Price',
        'price' => 'Price',
        'duration_minutes' => 'Duration (Minutes)',
        'extra_duration_minutes' => 'Extra Duration (Minutes)',
        'custom_label' => 'Custom Label',
        'price_display_override' => 'Price Display Override',
        'inclusions' => 'Inclusions',
        'icon' => 'Icon',
        'container_class' => 'Container CSS Class',
        'type' => 'Type',
        'page_number' => 'Page Number',
        'layout_type' => 'Layout Type',
        'meta' => 'Meta Attributes',

        // Specialists
        'role' => 'Job Title / Role',
        'experience' => 'Years of Experience',
        'bio' => 'Biography',
        'is_home_specialist' => 'Available for Home Services',
        'day_of_week' => 'Day of Week',
        'start_time' => 'Start Time',
        'end_time' => 'End Time',
        'is_day_off' => 'Day Off',
        'start_datetime' => 'Start Date & Time',
        'end_datetime' => 'End Date & Time',
        'reason' => 'Reason',

        // Customers & Loyalty
        'customer_code' => 'Customer Code',
        'membership_tier' => 'Membership Tier',
        'total_points' => 'Total Points',
        'min_spend' => 'Minimum Spend',
        'discount_percentage' => 'Discount %',
        'points_multiplier' => 'Points Multiplier',
        'badge_color' => 'Badge Color',
        'points_change' => 'Points Change (+/-)',
        'balance_after' => 'Balance After',

        // Addresses & Districts
        'district' => 'District',
        'zone' => 'Zone',
        'street_name' => 'Street Name',
        'building_number' => 'Building Number',
        'floor_number' => 'Floor Number',
        'latitude' => 'Latitude',
        'longitude' => 'Longitude',
        'is_default' => 'Default Address',
        'delivery_fee' => 'Delivery Fee',
        'estimated_transit_minutes' => 'Estimated Transit (Minutes)',

        // Bookings
        'booking_number' => 'Booking #',
        'customer' => 'Customer',
        'customer_address' => 'Customer Address',
        'booking_type' => 'Booking Type',
        'booking_date' => 'Booking Date',
        'overall_start_time' => 'Overall Start Time',
        'overall_end_time' => 'Overall End Time',
        'status' => 'Booking Status',
        'subtotal_amount' => 'Subtotal',
        'discount_amount' => 'Discount Amount',
        'vat_amount' => 'VAT Amount (15%)',
        'total_amount' => 'Total Amount',
        'deposit_required' => 'Deposit Required',
        'deposit_paid' => 'Deposit Paid',
        'payment_status' => 'Payment Status',
        'admin_notes' => 'Admin Notes',

        // Booking Items
        'service' => 'Service',
        'specialist' => 'Specialist',
        'unit_price' => 'Unit Price',
        'scheduled_start_time' => 'Scheduled Start Time',
        'scheduled_end_time' => 'Scheduled End Time',

        // Payments
        'booking' => 'Booking',
        'payment_method' => 'Payment Method',
        'transaction_reference' => 'Transaction Reference',
        'amount' => 'Amount',
        'payment_type' => 'Payment Type',
        'paid_at' => 'Paid At',
        'gateway_response' => 'Gateway Response',
        'code' => 'Code',
        'is_online' => 'Online Payment',
        'instructions' => 'Payment Instructions',

        // CMS
        'badge_text' => 'Badge Text',
        'fallback_url' => 'Fallback URL',
        'tag' => 'Tag',
        'aspect_ratio' => 'Aspect Ratio',
        'founder_name' => 'Founder Name',
        'founder_title' => 'Founder Title',
        'intro_text' => 'Intro Text',
        'description_text' => 'Description Text',
        'vision_quote' => 'Vision Quote',
        'experience_badge' => 'Experience Badge',
        'signature_image_path' => 'Signature Image',
        'portrait_image_path' => 'Portrait Image',
        'policy_text' => 'Policy Content',
        'free_cancellation_hours' => 'Free Cancellation (Hours)',
        'partial_refund_hours' => 'Partial Refund (Hours)',
        'partial_refund_percentage' => 'Partial Refund %',
        'no_show_penalty_percentage' => 'No-Show Penalty %',
        'day_label' => 'Day Label',
        'open_time' => 'Opening Time',
        'close_time' => 'Closing Time',
        'is_closed' => 'Closed (Day Off)',

        // Settings
        'tagline' => 'Tagline / Slogan',
        'description_line1' => 'Description (Line 1)',
        'description_line2' => 'Description (Line 2)',
        'address' => 'Physical Address',
        'logo_path' => 'Salon Logo',
        'whatsapp' => 'WhatsApp',
        'instagram' => 'Instagram',
        'tiktok' => 'TikTok',
        'snapchat' => 'Snapchat',
        'google_maps_url' => 'Google Maps URL',
        'tax_number' => 'Tax Number',
        'vat_percentage' => 'VAT Percentage %',
        'stats' => 'Promotional Statistics',

        // Users
        'password' => 'Password',
        'password_confirmation' => 'Password Confirmation',
    ],

    'statuses' => [
        // Bookings
        'pending' => 'Pending',
        'confirmed' => 'Confirmed',
        'in_progress' => 'In Progress',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
        'no_show' => 'No-Show',

        // Payments
        'unpaid' => 'Unpaid',
        'partially_paid' => 'Partially Paid',
        'fully_paid' => 'Fully Paid',
        'refunded' => 'Refunded',
        'success' => 'Success',
        'failed' => 'Failed',

        // Common
        'active' => 'Active',
        'inactive' => 'Inactive',
    ],

    'options' => [
        'booking_type_salon' => 'In-Salon Service',
        'booking_type_home' => 'Home Service',
        'payment_type_deposit' => 'Deposit Payment',
        'payment_type_final' => 'Final Payment',
        'payment_type_refund' => 'Refund',
        'days' => [
            0 => 'Sunday',
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
        ],
    ],

    'filters' => [
        'category' => 'Filter by Category',
        'status' => 'Filter by Status',
        'booking_type' => 'Filter by Booking Type',
        'payment_status' => 'Filter by Payment Status',
        'active_status' => 'Active Records Only',
        'featured_status' => 'Featured Records Only',
        'specialist' => 'Filter by Specialist',
        'district' => 'Filter by District',
        'date' => 'Filter by Date',
    ],

    'actions' => [
        'create' => 'Create New',
        'edit' => 'Edit',
        'view' => 'View Details',
        'delete' => 'Delete',
        'save' => 'Save Changes',
        'cancel' => 'Cancel',
        'confirm' => 'Confirm',
    ],

    'units' => [
        'minutes' => 'min',
        'currency' => 'SAR',
        'points' => 'pts',
        'percentage' => '%',
        'hours' => 'hrs',
    ],

    'helpers' => [
        'price_display_override' => 'Custom label displayed on the service card instead of the numerical price, e.g.: "Starts from SAR 150"',
        'phone_format' => 'Please enter phone in international format, e.g.: 9665xxxxxxxx',
        'slug_auto' => 'Automatically generated from the name for friendly URLs',
    ],
];
