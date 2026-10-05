<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'Lò vi sóng cơ Sharp R-205VN(S) 20 Lít',
                'category_name' => 'Lò vi sóng cơ',
                'brand' => 'Sharp',
                'price' => 1250000,
                'original_price' => 1590000,
                'stock' => 50,
                'color' => 'Bạc (Silver)',
                'capacity' => '20 Lít',
                'power' => '800W',
                'image_url' => '/images/microwave_sharp.jpg',
                'description' => 'Thiết kế nhỏ gọn, dễ sử dụng với 2 núm vặn xoay cơ học siêu bền. Dung tích 20 lít phù hợp cho gia đình nhỏ 2-4 thành viên, tích hợp chức năng hâm nóng và rã đông 5 mức công suất.',
            ],
            [
                'name' => 'Lò vi sóng Retro Vintage Classic Xanh Ngọc',
                'category_name' => 'Lò vi sóng cơ',
                'brand' => 'Retro Classic',
                'price' => 2100000,
                'original_price' => 2450000,
                'stock' => 35,
                'color' => 'Xanh ngọc pastel',
                'capacity' => '20 Lít',
                'power' => '700W',
                'image_url' => '/images/microwave_retro.jpg',
                'description' => 'Thiết kế cổ điển Vintage phong cách Châu Âu màu xanh ngọc bích, mang lại cảm giác hoài cổ nhưng vẫn trang bị đầy đủ tính năng hâm nóng, rã đông hiện đại.',
            ],
            [
                'name' => 'Lò vi sóng Panasonic Inverter NN-SM33NBYUE 25 Lít',
                'category_name' => 'Lò vi sóng điện tử',
                'brand' => 'Panasonic',
                'price' => 3500000,
                'original_price' => 3990000,
                'stock' => 40,
                'color' => 'Đen bóng',
                'capacity' => '25 Lít',
                'power' => '900W',
                'image_url' => '/images/microwave_panasonic.jpg',
                'description' => 'Lò vi sóng thông minh với công nghệ Inverter siêu tiết kiệm điện, rã đông cực nhanh đều không chín mép thịt, màn hình cảm ứng sang trọng cho gian bếp hiện đại.',
            ],
            [
                'name' => 'Lò vi sóng Smart Inverter Thế Hệ Mới Pro',
                'category_name' => 'Lò vi sóng điện tử',
                'brand' => 'Panasonic',
                'price' => 4500000,
                'original_price' => 5200000,
                'stock' => 25,
                'color' => 'Xám Titan',
                'capacity' => '27 Lít',
                'power' => '1000W',
                'image_url' => '/images/microwave_modern.jpg',
                'description' => 'Công nghệ biến tần Inverter đời mới nhất, điều khiển điện tử kết hợp cảm biến độ ẩm thông minh tự động tinh chỉnh thời gian nấu hoàn hảo.',
            ],
            [
                'name' => 'Lò vi sóng Electrolux có nướng EMG23K38GB 23 Lít',
                'category_name' => 'Lò vi sóng có nướng',
                'brand' => 'Electrolux',
                'price' => 2800000,
                'original_price' => 3300000,
                'stock' => 30,
                'color' => 'Đen gương',
                'capacity' => '23 Lít',
                'power' => '1000W / Nướng 1000W',
                'image_url' => '/images/microwave_industrial.jpg',
                'description' => 'Tích hợp chức năng nướng đối lưu 1000W và vi sóng 800W, cho phép nấu đa dạng các món ăn giòn thơm rượm. Lòng lò tráng men cao cấp dễ dàng lau chùi.',
            ],
            [
                'name' => 'Lò vi sóng có nướng Sharp R-G302VN-S 23 Lít',
                'category_name' => 'Lò vi sóng có nướng',
                'brand' => 'Sharp',
                'price' => 2350000,
                'original_price' => 2790000,
                'stock' => 45,
                'color' => 'Bạc kim loại',
                'capacity' => '23 Lít',
                'power' => '800W / Nướng 1000W',
                'image_url' => '/images/microwave_sharp.jpg',
                'description' => 'Kết hợp nướng công suất 1000W cùng 5 mức công suất vi sóng linh hoạt. Bảng điều khiển song ngữ Việt - Anh thân thiện với mọi lứa tuổi người dùng.',
            ],
            [
                'name' => 'Lò vi sóng Bosch HMT75M654 Âm Tủ Cao Cấp',
                'category_name' => 'Lò vi sóng âm tủ',
                'brand' => 'Bosch',
                'price' => 12500000,
                'original_price' => 14900000,
                'stock' => 15,
                'color' => 'Inox viền đen',
                'capacity' => '25 Lít',
                'power' => '900W',
                'image_url' => '/images/microwave_modern.jpg',
                'description' => 'Lò vi sóng âm tủ nhập khẩu chính hãng Bosch tiêu chuẩn Châu Âu. Thiết kế gắn liền tủ bếp sang trọng, cửa mở nút bấm điện tử hiện đại.',
            ],
            [
                'name' => 'Lò vi sóng Hafele HM-B38A Âm Tủ Đa Năng',
                'category_name' => 'Lò vi sóng âm tủ',
                'brand' => 'Hafele',
                'price' => 9800000,
                'original_price' => 11500000,
                'stock' => 20,
                'color' => 'Thép không gỉ',
                'capacity' => '25 Lít',
                'power' => '900W / Nướng 1000W',
                'image_url' => '/images/microwave_retro.jpg',
                'description' => 'Thiết kế âm tủ lắp đặt gọn gàng, 8 chế độ nấu tự động được lập trình sẵn giúp việc nấu nướng trở nên dễ dàng và nhanh chóng hơn bao giờ hết.',
            ],
            [
                'name' => 'Lò vi sóng Công nghiệp Pro Master Chef 30 Lít',
                'category_name' => 'Lò vi sóng cơ',
                'brand' => 'ProMaster',
                'price' => 8900000,
                'original_price' => 9900000,
                'stock' => 10,
                'color' => 'Inox 304 xước',
                'capacity' => '30 Lít',
                'power' => '1500W',
                'image_url' => '/images/microwave_industrial.jpg',
                'description' => 'Lò vi sóng công suất lớn dành cho nhà hàng, quán cà phê và bếp công nghiệp. Toàn thân thép không gỉ Inox 304 siêu bền, chịu được cường độ hoạt động liên tục.',
            ],
            [
                'name' => 'Lò vi sóng Toshiba ER-SGS34(S)VN Điện Tử 34 Lít',
                'category_name' => 'Lò vi sóng điện tử',
                'brand' => 'Toshiba',
                'price' => 3890000,
                'original_price' => 4500000,
                'stock' => 30,
                'color' => 'Bạc ánh kim',
                'capacity' => '34 Lít',
                'power' => '1000W / Nướng 1100W',
                'image_url' => '/images/microwave_panasonic.jpg',
                'description' => 'Dung tích cực lớn 34 Lít chứa vừa gà nguyên con hoặc đĩa lớn. Màn hình LED hiển thị rõ nét, tích hợp 11 mức công suất và chức năng nướng giòn tiện lợi.',
            ],
        ];

        foreach ($products as $item) {
            $category = Category::firstOrCreate(
                ['slug' => Str::slug($item['category_name'])],
                ['name' => $item['category_name']]
            );

            Product::firstOrCreate(
                [
                    'name' => $item['name'],
                    'category_id' => $category->id,
                ],
                [
                    'brand' => $item['brand'],
                    'price' => $item['price'],
                    'original_price' => $item['original_price'],
                    'stock' => $item['stock'],
                    'color' => $item['color'],
                    'capacity' => $item['capacity'],
                    'power' => $item['power'],
                    'image_url' => $item['image_url'],
                    'description' => $item['description'],
                ]
            );
        }

        $this->command?->info('Sample microwave products seeded successfully (10 items).');
    }
}
