<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Entities\Master\Customer;
use App\Entities\Master\CustomerOtherAddress;
use App\Entities\Master\CustomerPhoto;
use Illuminate\Support\Str;
use Auth;
use Image;
use DB;

class ProspectController extends Controller
{
    public function index()
    {
        $data = Customer::whereDate('created_at', Carbon::today())
            ->latest()
            ->get();

        return view('sales.prospect.index', compact('data'));
    }

    public function create()
    {
        return view('sales.prospect.create');
    }

    public function store(Request $request)
    {
        // ✅ VALIDASI
        $request->validate([
            'name' => 'required',
            'photos.*' => 'image|mimes:jpg,jpeg,png|max:2048'
        ]);

        DB::beginTransaction();

        try {

            $user = Auth::guard('superuser')->user();

            // ✅ CREATE STORE (CUSTOMER)
            $customer = Customer::create([
                'name' => $request->name,
                'phone' => $request->phone,
                'address' => $request->address,
                'owner_name' => $request->owner_name,
                'count_member' => 1,
                'gps_latitude' => $request->gps_latitude,
                'gps_longitude' => $request->gps_longitude,
                'status' => 1,
                'existence' => 1,
                'created_by' => $user->id,
            ]);

            // dd($customer->id.'.'.$customer->count_member);
            // ✅ CREATE MEMBER DEFAULT (CLONE)
            CustomerOtherAddress::create([
                'id' => $customer->id.'.'.$customer->count_member,
                'customer_id' => $customer->id,
                'member_default' => 1,
                'name' => $request->name,
                'phone' => $request->phone,
                'address' => $request->address,
                'gps_latitude' => $request->gps_latitude,
                'gps_longitude' => $request->gps_longitude,
                'status' => 1,
            ]);

            // ✅ HANDLE UPLOAD FOTO
            if ($request->hasFile('photos')) {

                $path = public_path('uploads/customer');

                // Pastikan folder ada
                if (!file_exists($path)) {
                    mkdir($path, 0777, true);
                }

                foreach ($request->file('photos') as $photo) {

                    // Skip kalau file tidak valid
                    if (!$photo->isValid()) continue;

                    // Generate nama file unik
                    $filename = uniqid().'_'.Str::random(5).'.'.$photo->getClientOriginalExtension();

                    // Pindahkan file
                    $image = Image::make($photo->getRealPath());

                    // TEXT WATERMARK
                    $text = now()->format('Y-m-d H:i') . "\n" .
                            "Lat: ".$request->gps_latitude."\n" .
                            "Lng: ".$request->gps_longitude;

                    $fontPath = public_path('fonts/ARIALBD.ttf'); // gunakan font TTF bold

                    $image->text($text, 10, $image->height() - 10, function($font) use ($fontPath, $image) {
                        $font->file($fontPath);        // font bold
                        $font->size(24);                // ukuran font lebih besar
                        $font->color('#ffffff');        // warna putih
                        $font->align('left');           // rata kiri
                        $font->valign('bottom');        // rata bawah
                        $font->angle(0);                // rotasi 0°
                    });

                    // Save gambar
                    $image->save($path.'/'.$filename);

                    // Simpan ke DB
                    CustomerPhoto::create([
                        'customer_id' => $customer->id,
                        'file' => $filename,
                        'type' => 'store',
                        'gps_latitude' => $request->gps_latitude,
                        'gps_longitude' => $request->gps_longitude,
                        'taken_at' => now(),
                        'created_by' => $user->id
                    ]);
                }
            }

            DB::commit();

            return redirect('/prospect')->with('success', 'Prospek berhasil ditambahkan');

        } catch (\Exception $e) {
            dd($e);
            DB::rollback();

            return back()
                ->withInput()
                ->with('error', 'Gagal menyimpan: '.$e->getMessage());
        }
    }
}
