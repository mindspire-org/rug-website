<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\Request;

class FilterController extends Controller
{
    private array $defaults = [
        'color' => [
            ['hex' => '#D4CFC6', 'name' => 'Neutrals'],
            ['hex' => '#3A6EA8', 'name' => 'Blues'],
            ['hex' => '#8B2020', 'name' => 'Reds'],
            ['hex' => '#2D5C3A', 'name' => 'Greens'],
            ['hex' => '#B07A4A', 'name' => 'Warm Tones'],
            ['hex' => '#5B7B8A', 'name' => 'Cool Tones'],
            ['hex' => '#D4C832', 'name' => 'Yellow'],
            ['hex' => '#1A1A1A', 'name' => 'Black'],
        ],
        'pattern' => ['Solid', 'Stripe', 'Grid', 'Geometric', 'Abstract', 'Classic & Ornate', 'Floral', 'Traditional', 'Modern'],
        'material' => ['Wool', 'Wool and Synthetic Blend', 'Silk', 'Silk and Wool', 'Nylon', 'Solution Dyed Nylon', 'Sisal/Plant Fibers', 'Solution-Dyed Acrylic', 'Polypropylene'],
        'room'     => ['Living Room', 'Bedroom', 'Dining Room', 'Hallway', 'Office', 'Outdoor', 'Staircase'],
        'construction' => ['Hand-Knotted', 'Hand-Tufted', 'Hand Loomed', 'Flatweave', 'Machine Made', 'Machine Tufted', 'Machine Woven', 'Petit-Point Wilton', 'Hooked'],
        'size'     => ['6×9', '8×10', '9×12', '10×14', '12×15', 'Custom'],
        'availability' => [
            ['value' => 'In Stock',      'label' => 'In Stock (2 Weeks)'],
            ['value' => 'Custom Size',   'label' => 'Custom Size (2-4 weeks)'],
            ['value' => 'Fully Custom',  'label' => 'Fully Custom (8-12 weeks)'],
        ],
    ];

    /**
     * Get merged filter options (defaults + saved settings).
     * Used by this controller and the ProductController for product forms.
     */
    public static function getOptions(): array
    {
        $saved = json_decode(Setting::get('filter_options', '{}'), true) ?: [];
        return array_merge((new self)->defaults, $saved);
    }

    public function index()
    {
        $options = self::getOptions();

        // Product counts for stats
        $stats = [
            'total_products'   => Product::active()->count(),
            'with_material'    => Product::active()->whereNotNull('material')->where('material', '!=', '')->count(),
            'with_style'       => Product::active()->whereNotNull('style')->where('style', '!=', '')->count(),
            'with_color'       => Product::active()->whereNotNull('refined_color')->where('refined_color', '!=', '')->count(),
            'with_dimensions'  => Product::active()->whereNotNull('dimensions')->where('dimensions', '!=', '')->count(),
        ];

        // Count products per filter value
        $counts = [];
        foreach ($options['material'] as $m) {
            $counts['material'][$m] = Product::active()->where('material', $m)->count();
        }
        foreach ($options['pattern'] as $p) {
            $counts['pattern'][$p] = Product::active()->where('style', $p)->count();
        }
        foreach ($options['color'] as $c) {
            $counts['color'][$c['name']] = Product::active()->where('refined_color', $c['name'])->count();
        }

        return view('admin.filters.index', compact('options', 'stats', 'counts'));
    }

    public function update(Request $request)
    {
        $options = [];

        // Color swatches
        $hexes = $request->input('color_hex', []);
        $names = $request->input('color_name', []);
        $options['color'] = [];
        foreach ($hexes as $i => $hex) {
            if (!empty($hex) && !empty($names[$i])) {
                $options['color'][] = ['hex' => $hex, 'name' => $names[$i]];
            }
        }

        // Simple list filters
        foreach (['pattern', 'material', 'room', 'construction', 'size'] as $key) {
            $raw = $request->input($key . '_items', '');
            $options[$key] = array_values(array_filter(array_map('trim', explode("\n", $raw))));
        }

        // Availability (value + label pairs)
        $avVals    = $request->input('avail_value', []);
        $avLabels  = $request->input('avail_label', []);
        $options['availability'] = [];
        foreach ($avVals as $i => $val) {
            if (!empty($val) && !empty($avLabels[$i])) {
                $options['availability'][] = ['value' => $val, 'label' => $avLabels[$i]];
            }
        }

        Setting::set('filter_options', json_encode($options));

        return back()->with('success', 'Filter options saved!');
    }
}
