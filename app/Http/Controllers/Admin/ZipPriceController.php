<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ZipPrice;
use Illuminate\Http\Request;

class ZipPriceController extends Controller
{
    public function index()
    {
        $zipPrices = ZipPrice::orderBy('zip_start')->get();
        return view('admin.zip-prices.index', compact('zipPrices'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'label'     => 'nullable|string|max:100',
            'zip_start' => 'required|string|max:10',
            'zip_end'   => 'required|string|max:10',
            'price'     => 'required|numeric|min:0',
            'active'    => 'nullable|boolean',
        ]);
        $data['active'] = $request->boolean('active', true);
        ZipPrice::create($data);

        return back()->with('success', 'ZIP price range added.');
    }

    public function update(Request $request, ZipPrice $zipPrice)
    {
        $data = $request->validate([
            'label'     => 'nullable|string|max:100',
            'zip_start' => 'required|string|max:10',
            'zip_end'   => 'required|string|max:10',
            'price'     => 'required|numeric|min:0',
            'active'    => 'nullable|boolean',
        ]);
        $data['active'] = $request->boolean('active');
        $zipPrice->update($data);

        return back()->with('success', 'ZIP price range updated.');
    }

    public function destroy(ZipPrice $zipPrice)
    {
        $zipPrice->delete();
        return back()->with('success', 'ZIP price range removed.');
    }
}
