<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use App\Models\Medicine;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use App\Models\MedicineHistory;

class MedicineController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        try {
            $query = Medicine::query();
            
            if ($request->has('search')) {
                $query->search($request->search);
            }
            
            $medicines = $query->latest()->paginate(10);
            
            return Inertia::render('Admin/Medicines/Index', [
                'medicines' => $medicines->items() ?? [],
                'filters' => $request->only(['search'])
            ]);
        } catch (\Exception $e) {
            return Inertia::render('Admin/Medicines/Index', [
                'medicines' => [],
                'filters' => $request->only(['search']),
                'error' => 'Failed to load medicines'
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        return Inertia::render('Admin/Medicines/Create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'lprice' => 'required|numeric',
            'mprice' => 'required|numeric',
            'hprice' => 'required|numeric',
            'quantity' => 'required|numeric',
            'dosage' => 'required|string',
            'expdate' => 'required|date',
        ]);

        $existingMedicine = Medicine::where('name', $validated['name'])->first();

        if ($existingMedicine) {
            $newQuantity = $existingMedicine->quantity + $validated['quantity'];
            
            $existingMedicine->update([
                'quantity' => $newQuantity,
                'expdate' => $validated['expdate'],
            ]);

            MedicineHistory::create([
                'medicine_id' => $existingMedicine->id,
                'name' => $existingMedicine->name,
                'lprice' => $existingMedicine->lprice,
                'mprice' => $existingMedicine->mprice,
                'hprice' => $existingMedicine->hprice,
                'quantity_added' => $validated['quantity'],
                'total_quantity' => $newQuantity,
                'dosage' => $existingMedicine->dosage,
                'expdate' => $validated['expdate'],
                'action_type' => 'update'
            ]);

            return redirect()->route('admin.medicines.index')
                ->with('success', 'Medicine quantity updated and expiration date changed successfully');
        }

        $medicine = Medicine::create($validated);

        MedicineHistory::create([
            'medicine_id' => $medicine->id,
            'name' => $medicine->name,
            'lprice' => $medicine->lprice,
            'mprice' => $medicine->mprice,
            'hprice' => $medicine->hprice,
            'quantity_added' => $medicine->quantity,
            'total_quantity' => $medicine->quantity,
            'dosage' => $medicine->dosage,
            'expdate' => $medicine->expdate,
            'action_type' => 'add'
        ]);

        return redirect()->route('admin.medicines.index')
            ->with('success', 'New medicine added successfully');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Medicine  $medicine
     * @return \Inertia\Response
     */
    public function edit(Medicine $medicine)
    {
        return Inertia::render('Admin/Medicines/Edit', ['medicines' => $medicine]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Medicine  $medicine
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Medicine $medicine)
    {
        $request->validate([
            'name' => ['required', 'string'],
            'lprice' => ['required', 'string'],
            'mprice' => ['required', 'string'],
            'hprice' => ['required', 'string'],
            'quantity' => ['required', 'string'],
            'dosage' => ['required', 'string'],
            'expdate' => ['required', 'date_format:Y-m-d'],
        ]);

        $medicine->update($request->all());

        return Redirect::route('admin.medicines.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Medicine  $medicine
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Medicine $medicine)
    {
        $medicine->delete();
        return Redirect::route('admin.medicines.index')->with('success', 'Medicine has been deleted.');
    }

    public function history(Request $request)
    {
        try {
            $query = MedicineHistory::query()
                ->orderBy('created_at', 'desc');

            // Apply date filters if present
            if ($request->start_date && $request->end_date) {
                $query->whereBetween('created_at', [
                    $request->start_date . ' 00:00:00',
                    $request->end_date . ' 23:59:59'
                ]);
            }

            $histories = $query->paginate(10);

            return Inertia::render('Admin/Medicines/History', [
                'histories' => $histories,
                'filters' => $request->only(['start_date', 'end_date'])
            ]);
        } catch (\Exception $e) {
            return Inertia::render('Admin/Medicines/History', [
                'histories' => [],
                'filters' => $request->only(['start_date', 'end_date']),
                'error' => 'Failed to load history records'
            ]);
        }
    }
}