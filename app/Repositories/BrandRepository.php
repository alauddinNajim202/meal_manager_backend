<?php

namespace App\Repositories;

use App\Helpers\Helper;
use App\Models\Brand;
use App\Repositories\Interfaces\BrandRepositoryInterface;

class BrandRepository implements BrandRepositoryInterface
{
    public function all()
    {
        return Brand::all();
    }

    public function find($id)
    {
        return Brand::find($id);
    }

    public function create(array $data)
    {
        if (isset($data['image'])) {
            $data['image'] = Helper::fileUpload($data['image'], 'brand', time() . '_' . getFileName($data['image']));
        }
        
        $data['slug'] = Helper::makeSlug(Brand::class, $data['name']);

        Brand::create($data);
    }

    public function update($id, array $data)
    {
        $brand = Brand::findOrFail($id);

        if (isset($data['image'])) {
            if ($brand->image && file_exists(public_path($brand->image))) {
                Helper::fileDelete(public_path($brand->image));
            }
            $data['image'] = Helper::fileUpload($data['image'], 'brand', time() . '_' . getFileName($data['image']));
        }

        $brand->update($data);
    }

    public function delete($id)
    {
        $data = Brand::findOrFail($id);
        if ($data->image && file_exists(public_path($data->image))) {
            Helper::fileDelete(public_path($data->image));
        }
        $data->delete();
    }
    public function status($id)
    {
        $data = Brand::findOrFail($id);
        if (!$data) {
            return response()->json([
                'status' => 't-error',
                'message' => 'Item not found.',
            ]);
        }
        $data->status = $data->status === 'active' ? 'inactive' : 'active';
        $data->save();
    }
}
