<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
        ],
        'pattern' => ['Solid', 'Stripe', 'Grid', 'Geometric', 'Abstract', 'Classic & Ornate'],
        'material' => ['Wool', 'Wool & Silk', 'Natural Fibers', 'Silk', 'Performance Fibers'],
        'room'     => ['Living Room', 'Bedroom', 'Dining Room', 'Hallway', 'Office', 'Outdoor', 'Staircase'],
        'construction' => ['Hand-Knotted', 'Hand-Tufted', 'Flatweave', 'Machine Made', 'Hand-Loomed', 'Hooked'],
        'size'     => ['6×9', '8×10', '9×12', '10×14', '12×15', 'Custom'],
        'availability' => [
            ['value' => 'In Stock',      'label' => 'In Stock (2 Weeks)'],
            ['value' => 'Custom Size',   'label' => 'Custom Size (2-4 weeks)'],
            ['value' => 'Made to Order', 'label' => 'Made to Order (8-12 weeks)'],
        ],
    ];

    public function index()
    {
        $saved = json_decode(Setting::get('filter_options', '{}'), true) ?: [];
        $options = array_merge($this->defaults, $saved);
        return view('admin.filters.index', compact('options'));
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
