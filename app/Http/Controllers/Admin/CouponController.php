<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index()
    {
        $coupons = Coupon::latest()->paginate(20);
        return view('admin.coupons.index', compact('coupons'));
    }

    public function create()
    {
        return view('admin.coupons.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code'      => 'required|string|unique:coupons,code',
            'type'      => 'required|in:percentage,fixed',
            'value'     => 'required|numeric|min:0',
            'min_order' => 'nullable|numeric|min:0',
            'uses_left' => 'nullable|integer|min:1',
            'expires_at'=> 'nullable|date',
        ]);

        $data = $request->all();
        $data['code'] = strtoupper($data['code']);
        $data['is_active'] = $request->has('is_active');

        Coupon::create($data);
        return redirect()->route('admin.coupons.index')->with('success', 'Coupon created!');
    }

    public function edit(Coupon $coupon)
    {
        return view('admin.coupons.edit', compact('coupon'));
    }

    public function update(Request $request, Coupon $coupon)
    {
        $request->validate([
            'type'     => 'required|in:percentage,fixed',
            'value'    => 'required|numeric|min:0',
            'min_order'=> 'nullable|numeric|min:0',
        ]);

        $data = $request->except(['_token', '_method']);
        $data['is_active'] = $request->has('is_active');
        $coupon->update($data);
        return redirect()->route('admin.coupons.index')->with('success', 'Coupon updated!');
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();
        return back()->with('success', 'Coupon deleted.');
    }
}
