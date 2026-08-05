<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\CryptoStore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CryptoStoreController extends Controller
{
    // ===================== INDEX (Yajra DataTable inline) =====================
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return datatables()->eloquent(CryptoStore::query()->withCount('ratings')->withAvg('ratings', 'rating')->orderByDesc('id'))
                ->addIndexColumn()
                ->addColumn('image', function ($row) {
                    return $row->image
                        ? '<img src="' . asset($row->image) . '" width="60" class="img-thumbnail">'
                        : '—';
                })
                ->editColumn('type', function ($row) {
                    return str_replace('_', ' ', ucwords($row->type ?? ''));
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at->format('d M Y');
                })
                ->editColumn('rating', function ($row) {
                    $avgRating = $row->ratings_avg_rating ?? 0;
                    $count     = $row->ratings_count ?? 0;
                    if ($count == 0) return '—';
                    $stars = '';
                    for ($i = 1; $i <= 5; $i++) {
                        $stars .= $i <= $avgRating
                            ? '<i class="fe fe-star text-warning"></i>'
                            : '<i class="fe fe-star text-muted"></i>';
                    }
                    return '<span>' . $stars . ' (' . number_format($avgRating, 1) . ') - ' . $count . ' reviews</span>';
                })
                ->addColumn('action', function ($row) {
                    $imageUrl = $row->image ? asset($row->image) : '';
                    $review   = addslashes($row->review ?? '');
                    $name     = addslashes($row->name);
                    $title    = addslashes($row->title);
                    $shortDesc = addslashes($row->short_description ?? '');

                return '
                    <div class="d-flex gap-1">
                        <a href="' . route('admin.crypto-stores.edit', $row->id) . '" class="btn btn-sm btn-primary">
                            <i class="fe fe-edit"></i> Edit
                        </a>
                        <button type="button" class="btn btn-sm btn-danger"
                            onclick="openDeleteModal(\'' . $row->id . '\', \'' . $name . '\')">
                            <i class="fe fe-trash-2"></i> Delete
                        </button>
                    </div>
                ';
            })
                ->addColumn('slug', function ($row) {
                    return '<code>' . ($row->slug ?? '—') . '</code>';
                })
                ->rawColumns(['image', 'rating', 'action', 'slug'])
                ->toJson();
    }

    return view('backend.layouts.cryptostores.index');
}

    // ===================== CREATE =====================
    public function create()
    {
        return view('backend.layouts.cryptostores.create');
    }    // ===================== STORE =====================
   public function store(Request $request)
{

    try {
        $request->validate([
            'name'              => 'required|string|max:255',
            'title'             => 'required|string|max:255',
            'short_description' => 'required|string|max:1000',
            'image'             => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'type'              => 'required|in:Tax_advisors,Legal_advisors,Crypto_partners',
            'experience_years'  => 'nullable|integer',
            'address'           => 'nullable|string',
            'phone'             => 'nullable|string|max:20',
            'linkedin_url'      => 'nullable|url',
            'twitter_url'       => 'nullable|url',
            'website'           => 'nullable|url',
            'contact_email'     => 'nullable|email',
            'our_mission'       => 'nullable|string',
            'success_stories'   => 'nullable|string',
            'legacy'            => 'nullable|string|max:255',
            'scale'             => 'nullable|string|max:255',
        ]);
        DB::beginTransaction();

        $data = $request->only([
            'name', 'title', 'short_description', 'type', 'experience_years', 'address', 'phone',
            'linkedin_url', 'twitter_url', 'website', 'contact_email', 'our_mission',
            'success_stories', 'legacy', 'scale'
        ]);

        // Handle Image Upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $data['image'] = Helper::fileUpload($image, 'crypto_store', $imageName);
        }

        // Create Crypto Store
        $cryptoStore = CryptoStore::create($data);

        // Supported Ecosystems
        if ($request->has('supported_ecosystems') && is_array($request->supported_ecosystems)) {
            foreach ($request->supported_ecosystems as $ecosystem) {
                if (!empty(trim($ecosystem))) {
                    $cryptoStore->supported_ecosystems()->create(['name' => trim($ecosystem)]);
                }
            }
        }

        // Verification Audits
        if ($request->has('verification_audits') && is_array($request->verification_audits)) {
            foreach ($request->verification_audits as $audit) {
                if (!empty(trim($audit))) {
                    $cryptoStore->verification_audits()->create(['name' => trim($audit)]);
                }
            }
        }

        // Expertises
        if ($request->has('expertises') && is_array($request->expertises)) {
            foreach ($request->expertises as $exp) {
                if (!empty(trim($exp))) {
                    $cryptoStore->expertises()->create(['name' => trim($exp)]);
                }
            }
        }

        DB::commit();

        return redirect()
            ->route('admin.crypto-stores.index')
            ->with('t-success', 'Crypto Store created successfully!');

    } catch (\Exception $e) {
        DB::rollBack();

        // Optional: Delete uploaded image if exists (in case of failure)
        if (isset($data['image']) && !empty($data['image'])) {
            // You can add image delete logic here if needed
        }

        return redirect()
            ->back()
            ->withInput()
            ->with('t-error', 'Something went wrong! Please try again. Error: ' . $e->getMessage());
    }
}

    // ===================== SHOW =====================
    public function show(CryptoStore $cryptoStore)
    {
        return view('backend.layouts.cryptostores.show', compact('cryptoStore'));
    }

    // ===================== EDIT =====================
    public function edit($id)
    {
        $cryptoStore = CryptoStore::with(['expertises', 'supported_ecosystems', 'verification_audits'])->findOrFail($id);
        return view('backend.layouts.cryptostores.edit', compact('cryptoStore'));
    }

    // ===================== UPDATE =====================
   public function update(Request $request, $id)
{
    try {
        $request->validate([
            'name'              => 'required|string|max:255',
            'title'             => 'required|string|max:255',
            'short_description' => 'required|string|max:1000',
            'image'             => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'type'              => 'required|in:Tax_advisors,Legal_advisors,Crypto_partners',
            'experience_years'  => 'nullable|integer',
            'address'           => 'nullable|string',
            'phone'             => 'nullable|string|max:20',
            'linkedin_url'      => 'nullable|url',
            'twitter_url'       => 'nullable|url',
            'website'           => 'nullable|url',
            'contact_email'     => 'nullable|email',
            'our_mission'       => 'nullable|string',
            'success_stories'   => 'nullable|string',
            'legacy'            => 'nullable|string|max:255',
            'scale'             => 'nullable|string|max:255',
        ]);

        $cryptoStore = CryptoStore::findOrFail($id);

        $data = $request->only([
            'name', 'title', 'short_description', 'type', 'experience_years', 'address', 'phone',
            'linkedin_url', 'twitter_url', 'website', 'contact_email', 'our_mission',
            'success_stories', 'legacy', 'scale'
        ]);

        // ✅ Image Update
        if ($request->hasFile('image')) {
            if ($cryptoStore->image && file_exists(public_path($cryptoStore->image))) {
                Helper::fileDelete(public_path($cryptoStore->image));
            }

            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $data['image'] = Helper::fileUpload($image, 'crypto_store', $imageName);
        }

        $cryptoStore->update($data);

        // =========================
        // 🔥 EXPERTISES
        // =========================
        $cryptoStore->expertises()->delete();

        if ($request->expertises) {
            foreach ($request->expertises as $exp) {
                if (!empty($exp)) {
                    $cryptoStore->expertises()->create([
                        'name' => $exp
                    ]);
                }
            }
        }

        // =========================
        // 🔥 VERIFICATION & AUDITS
        // =========================
        $cryptoStore->verification_audits()->delete();

        if ($request->verification_audits) {
            foreach ($request->verification_audits as $item) {
                if (!empty($item)) {
                    $cryptoStore->verification_audits()->create([
                        'name' => $item
                    ]);
                }
            }
        }

        // =========================
        // 🔥 SUPPORTED ECOSYSTEMS
        // =========================
        $cryptoStore->supported_ecosystems()->delete();

        if ($request->supported_ecosystems) {
            foreach ($request->supported_ecosystems as $item) {
                if (!empty($item)) {
                    $cryptoStore->supported_ecosystems()->create([
                        'name' => $item
                    ]);
                }
            }
        }

        return redirect()
            ->route('admin.crypto-stores.index')
            ->with('t-success', 'Crypto Store updated successfully!');

    } catch (\Exception $e) {

        return back()
            ->withInput()
            ->with('t-error', 'Something went wrong! ' . $e->getMessage());
    }
}

    public function destroy($id)
    {
        $cryptoStore = CryptoStore::findOrFail($id);
        if ($cryptoStore->image && file_exists(public_path($cryptoStore->image))) {
            Helper::fileDelete(public_path($cryptoStore->image));
        }

        $cryptoStore->delete();

        return redirect()->route('admin.crypto-stores.index')
            ->with('t-success', 'Crypto Store deleted successfully!');
    }
}
