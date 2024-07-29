<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Http\Response\GlobalResponse;
use Illuminate\Support\Facades\DB;

class ServiceController extends Controller
{
    /**
     * index
     *
     * @return void
     */
    public function index(Request $request)
    {
        try {
            $search = $request->input('search', '');
            $perPage = $request->input('per_page', Service::count());
            $page = $request->input('page', 1);

            $skip = ($page - 1) * $perPage;

            $query = Service::query();

            if (!empty($search)) {
                $query->where('name', 'like', "%{$search}%");
                $query->orWhere('code_of_service', 'like', "%{$search}%");
            }

            //get all posts
            // $data = Service::with('detail_transaction')->latest()->paginate(5);
            $data = $query->orderBy('id', 'desc')
                ->skip($skip)
                ->take($perPage)
                ->get();

            //return collection of data as a resource
            // return new Resources(true, 'List Layanan', $posts);
            return GlobalResponse::jsonResponse(
                $data,
                200,
                'success',
                'Layanan berhasil diambil'
            );
        } catch (\Throwable $e) {
            return GlobalResponse::jsonResponse(null, 500, 'error', $e->getMessage());
        } catch (\Exception $e) {
            return GlobalResponse::jsonResponse(null, 500, 'error', $e->getMessage());
        }
    }

    /**
     * store
     *
     * @param  mixed $request
     * @return void
     */
    public function store(Request $request)
    {
        try {
            //define validation rules
            $validator = $request->validate([
                'name'     => 'required',
                'price'     => 'required|numeric',
                'code_of_service'   => 'required',
            ]);

            //create post
            $post = Service::create([
                'name'     => $request->name,
                'price'   => $request->price,
                'code_of_service'   => $request->code_of_service,
            ]);

            //return response
            return GlobalResponse::jsonResponse(
                $post,
                201,
                'success',
                'Layanan "' . $request->name . '" dengan Harga Rp' . number_format($request->price, 2, ',', '.') . ' Berhasil Ditambahkan!'
            );
        } catch (\Throwable $e) {
            return GlobalResponse::jsonResponse(null, 500, 'error', $e->getMessage());
        } catch (\Exception $e) {
            return GlobalResponse::jsonResponse(null, 500, 'error', $e->getMessage());
        }
    }

    /**
     * update
     *
     * @param  mixed $request
     * @param  mixed $id
     * @return void
     */
    public function update(Request $request, $id)
    {
        try {
            //define validation rules
            $validator = $request->validate([
                'name'     => 'required',
                'price'     => 'required|numeric',
                'code_of_service'   => 'required',
            ]);

            //find post by ID
            $data = Service::find($id);
            $olddata = Service::find($id);

            //create post
            $data->update([
                'name'     => $request->name,
                'price'     => $request->price,
                'code_of_service'     => $request->code_of_service,
            ]);

            if ($request->name == $olddata['name']) {
                //return response
                return GlobalResponse::jsonResponse(
                    $data,
                    201,
                    'success',
                    'Layanan "' . $request->name . '" berhasil diubah!'
                );
            } else {
                //return response
                return GlobalResponse::jsonResponse(
                    $data,
                    201,
                    'success',
                    'Layanan "' . $olddata['name'] . '" berhasil diubah dengan nama baru "' . $request->name . '"!'
                );
            }
        } catch (\Throwable $e) {
            return GlobalResponse::jsonResponse(null, 500, 'error', $e->getMessage());
        } catch (\Exception $e) {
            return GlobalResponse::jsonResponse(null, 500, 'error', $e->getMessage());
        }
    }

    /**
     * destroy
     *
     * @param  mixed $id
     * @return void
     */
    public function destroy($id)
    {
        try {
            //find post by ID
            $data = Service::find($id);

            //delete post
            $data->delete();

            DB::statement('alter table services auto_increment = 1');

            //return response
            return GlobalResponse::jsonResponse(
                null,
                200,
                'success',
                'Layanan "' . $data['name'] . '" berhasil dihapus!'
            );
        } catch (\Throwable $e) {
            return GlobalResponse::jsonResponse(null, 500, 'error', $e->getMessage());
        } catch (\Exception $e) {
            return GlobalResponse::jsonResponse(null, 500, 'error', $e->getMessage());
        }
    }
}
