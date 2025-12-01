<?php

namespace Database\Seeders;

use App\Models\Shop;
use App\Models\Vendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class VendorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vendors = [
            [
                'name' => 'Fashion Store',
                'email' => 'fashion@vendor.com',
                'password' => 'vendor123',
                'phone' => '+1234567890',
                'status' => 'approved',
                'commission_rate' => 10.00,
                'shop' => [
                    'name' => 'Fashion Paradise',
                    'description' => 'Your one-stop shop for trendy fashion items.',
                    'status' => 'approved',
                ],
            ],
            [
                'name' => 'Tech World',
                'email' => 'tech@vendor.com',
                'password' => 'vendor123',
                'phone' => '+1234567891',
                'status' => 'approved',
                'commission_rate' => 8.00,
                'shop' => [
                    'name' => 'Tech World Store',
                    'description' => 'Latest gadgets and electronics at best prices.',
                    'status' => 'approved',
                ],
            ],
            [
                'name' => 'Home Essentials',
                'email' => 'home@vendor.com',
                'password' => 'vendor123',
                'phone' => '+1234567892',
                'status' => 'approved',
                'commission_rate' => 12.00,
                'shop' => [
                    'name' => 'Home & Living',
                    'description' => 'Everything you need for your home.',
                    'status' => 'approved',
                ],
            ],
            [
                'name' => 'Pending Vendor',
                'email' => 'pending@vendor.com',
                'password' => 'vendor123',
                'phone' => '+1234567893',
                'status' => 'pending',
                'commission_rate' => 10.00,
                'shop' => [
                    'name' => 'New Shop',
                    'description' => 'A new shop waiting for approval.',
                    'status' => 'pending',
                ],
            ],
        ];

        foreach ($vendors as $vendorData) {
            $shopData = $vendorData['shop'];
            unset($vendorData['shop']);

            $vendor = Vendor::firstOrCreate(
                ['email' => $vendorData['email']],
                $vendorData
            );

            if ($vendor->wasRecentlyCreated) {
                Shop::create([
                    'vendor_id' => $vendor->id,
                    'name' => $shopData['name'],
                    'slug' => Str::slug($shopData['name']),
                    'description' => $shopData['description'],
                    'status' => $shopData['status'],
                ]);

                $this->command->info("Created vendor: {$vendor->name} with shop: {$shopData['name']}");
            } else {
                $this->command->info("Vendor already exists: {$vendor->email}");
            }
        }

        $this->command->info('');
        $this->command->info('=== Test Vendor Credentials ===');
        $this->command->info('Email: fashion@vendor.com | Password: vendor123');
        $this->command->info('Email: tech@vendor.com | Password: vendor123');
        $this->command->info('Email: home@vendor.com | Password: vendor123');
        $this->command->info('================================');
    }
}
