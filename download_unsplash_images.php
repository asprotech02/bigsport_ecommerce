<?php

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$pools = [
    'sepatu' => [
        'photo-1542291026-7eec264c27ff',
        'photo-1606107557195-0e29a4b5b4aa',
        'photo-1608231387042-66d1773070a5',
        'photo-1549298916-b41d501d3772',
        'photo-1595950653106-6c9ebd614d3a',
        'photo-1539185441755-769473a23570',
        'photo-1525966222134-fcfa99b8ae77',
        'photo-1584735935682-2f2b69dff9d2',
        'photo-1600185365483-26d7a4cc7519',
        'photo-1607522370275-f14206abe5d3',
        'photo-1514989940723-e8e51635b782',
        'photo-1560769629-975ec94e6a86',
        'photo-1491553895911-0055eca6402d',
        'photo-1556906781-9a412961c28c',
        'photo-1562183241-b937e95585b6',
        'photo-1515955656352-a1fa3ffcd111',
        'photo-1520639888713-7851133b1ed0',
        'photo-1511556532299-8f662fc26c06',
        'photo-1603808033192-082d6919d3e1',
        'photo-1597045566677-8cf032ed6634',
        'photo-1582588678413-dbf45f4823e9',
        'photo-1551107696-a4b0c5a0d9a2',
        'photo-1560343090-f0409e92791a',
        'photo-1535043934128-cf0b28d52f95',
        'photo-1605348532760-6753d2c43329'
    ],
    'tas' => [
        'photo-1553062407-98eeb64c6a62',
        'photo-1622560480605-d83c853bc5c3',
        'photo-1581605405669-fcdf81165afa',
        'photo-1547949003-9792a18a2601',
        'photo-1531088009183-5ff5b7c95f91',
        'photo-1552046122-03184de85e08'
    ],
    'hoodie' => [
        'photo-1556821840-3a63f95609a7',
        'photo-1620799140408-edc6dcb6d633',
        'photo-1609743522653-52354461eb27',
        'photo-1556905055-8f358a7a47b2',
        'photo-1618354691373-d851c5c3a990',
        'photo-1620799139507-2a76f79a2f4d',
        'photo-1620799140188-3b2a02fd9a77'
    ],
    'pakaian' => [
        'photo-1521572267360-ee0c2909d518',
        'photo-1583743814966-8936f5b7be1a',
        'photo-1618354691373-d851c5c3a990',
        'photo-1620799140408-edc6dcb6d633',
        'photo-1576566588028-4147f3842f27',
        'photo-1503342217505-b0a15ec3261c',
        'photo-1529374255404-311a2a4f1fd9',
        'photo-1581655353564-df123a1eb820',
        'photo-1602810318383-e386cc2a3ccf'
    ],
    'celana' => [
        'photo-1591195853828-11db59a44f6b',
        'photo-1604176354204-9268737828e4',
        'photo-1541099649105-f69ad21f3246',
        'photo-1624378439575-d8705ad7ae80',
        'photo-1594633312681-425c7b97ccd1',
        'photo-1565084888279-aca607ecce0c',
        'photo-1584308666744-24d5c474f2ae',
        'photo-1509551388413-e18d0ac5d495',
        'photo-1517445312882-bc9910d016b7',
        'photo-1605518216938-7c31b7b14ad0'
    ],
    'topi' => [
        'photo-1534215754734-18e55d13e346',
        'photo-1588850561407-ed78c282e89b'
    ],
    'kaos_kaki' => [
        'photo-1582966772680-860e372bb558',
        'photo-1608228088998-57828365d486'
    ],
    'aksesoris' => [
        'photo-1523275335684-37898b6baf30',
        'photo-1572635196237-14b3f281503f',
        'photo-1505740420928-5e560c06d30e'
    ]
];

$products = Product::all();
$total = $products->count();
$downloaded = 0;

echo "Starting download of clean white background Unsplash images for {$total} products...\n";

$dir = storage_path('app/public/products');
if (!is_dir($dir)) {
    mkdir($dir, 0755, true);
}

$originalPremiumIds = [151, 152, 153, 154, 155, 156, 157, 158, 159, 160, 162];

foreach ($products as $index => $p) {
    if (in_array($p->id, $originalPremiumIds)) {
        echo "[Product {$p->id}] Keeping premium original image: " . ($p->images->first()->image_path ?? '') . "\n";
        continue;
    }

    $name = strtolower($p->name);
    
    // Choose pool
    $poolName = 'aksesoris';
    if ($p->category_id == 1) { // Sepatu
        $poolName = 'sepatu';
    } elseif ($p->category_id == 2) { // Pakaian
        if (stripos($name, 'celana') !== false) {
            $poolName = 'celana';
        } elseif (stripos($name, 'hoodie') !== false || stripos($name, 'jaket') !== false) {
            $poolName = 'hoodie';
        } else {
            $poolName = 'pakaian';
        }
    } else { // Aksesoris
        if (stripos($name, 'tas') !== false) {
            $poolName = 'tas';
        } elseif (stripos($name, 'topi') !== false) {
            $poolName = 'topi';
        } elseif (stripos($name, 'kaos kaki') !== false) {
            $poolName = 'kaos_kaki';
        }
    }

    $pool = $pools[$poolName];
    $count = count($pool);
    
    // Select image using product ID hash to make sure it is stable and different for adjacent products
    $imgIndex = ($p->id) % $count;
    $photoId = $pool[$imgIndex];
    
    $url = "https://images.unsplash.com/{$photoId}?auto=format&fit=crop&w=600&h=600&q=80";
    $filename = "product_{$p->id}.jpg";
    $savePath = $dir . '/' . $filename;
    $dbPath = "products/" . $filename;

    echo "[Product " . ($index + 1) . "/{$total}] Downloading clean white bg image for ID {$p->id} ({$p->name}) from pool '{$poolName}'... ";

    // Download file with curl
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $data = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200 && !empty($data)) {
        file_put_contents($savePath, $data);
        
        // Copy to public directory as well to keep public symlink synchronized
        $publicPath = public_path('storage/products/' . $filename);
        $publicDir = dirname($publicPath);
        if (!is_dir($publicDir)) {
            mkdir($publicDir, 0755, true);
        }
        file_put_contents($publicPath, $data);

        // Update database
        DB::transaction(function () use ($p, $dbPath) {
            ProductImage::where('product_id', $p->id)->delete();
            ProductImage::create([
                'product_id' => $p->id,
                'image_path' => $dbPath,
                'is_primary' => true
            ]);
        });

        echo "Success.\n";
        $downloaded++;
    } else {
        echo "Failed (HTTP Code: {$httpCode}).\n";
    }

    // Add a tiny sleep to avoid rate limiting
    usleep(50000); // 50ms
}

echo "Completed! Downloaded {$downloaded} white background images.\n";
