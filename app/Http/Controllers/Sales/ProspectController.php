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
    public function index(Request $request)
    {
        $startDate = Carbon::now()->startOfMonth()->addDays(27); // tanggal 28
        $endDate = Carbon::now();

        $data = CustomerOtherAddress::with('photos')
            ->whereBetween('created_at', [$startDate, $endDate])
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
        // dd($request->all());
        // ✅ VALIDASI
        $request->validate([
            'name' => 'required',
            // 'photos.*' => 'image|mimes:jpg,jpeg,png|max:2048'
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
                'text_provinsi' => $request->text_provinsi,
                'text_kota' => $request->text_kota,
                'text_kecamatan' => $request->text_kecamatan,
                'text_kelurahan' => $request->text_kelurahan,
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
                'text_provinsi' => $request->text_provinsi,
                'text_kota' => $request->text_kota,
                'text_kecamatan' => $request->text_kecamatan,
                'text_kelurahan' => $request->text_kelurahan,
                'status' => 1,
            ]);

            // ✅ HANDLE UPLOAD FOTO
            if ($request->photos) {

                $path = public_path('uploads/customer');

                if (!file_exists($path)) {
                    mkdir($path, 0777, true);
                }

                foreach ($request->photos as $base64) {

                    // ambil extension
                    preg_match('/data:image\/(\w+);base64,/', $base64, $type);
                    $ext = $type[1] ?? 'jpg';

                    // hapus prefix base64
                    $base64 = preg_replace('/^data:image\/\w+;base64,/', '', $base64);
                    $base64 = str_replace(' ', '+', $base64);

                    $imageData = base64_decode($base64);

                    $filename = uniqid().'_'.Str::random(5).'.'.$ext;

                    // simpan file sementara
                    file_put_contents($path.'/'.$filename, $imageData);

                    // load pakai Intervention Image
                    $image = Image::make($path.'/'.$filename);

                    // watermark
                    $text = now()->format('Y-m-d H:i') . "\n" .
                            "Lat: ".$request->gps_latitude."\n" .
                            "Lng: ".$request->gps_longitude;

                    $image->text($text, 10, $image->height() - 10, function($font) {
                        $font->size(20);
                        $font->color('#ffffff');
                        $font->align('left');
                        $font->valign('bottom');
                    });

                    $image->save($path.'/'.$filename);

                    // simpan DB
                    CustomerPhoto::create([
                        'customer_id' => $customer->id,
                        'customer_other_address_id' => $customer->id.'.'.$customer->count_member,
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
